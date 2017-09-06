<?php

require_once(__DIR__.'/config.php');
require_once(__DIR__.'/modules.php');
require_once(__DIR__.'/database.php');
require_once(__DIR__.'/utils.php');
require_once(__DIR__.'/lib/usercake/user.php');

// This class stores everything needed for MRF to work
class YEdCore
{
	private static $instance 	= null;	
	private $modules			= array();
	private $database			= null;
	
	const perm_user_admin 			= 2;
	const perm_user_reader 			= 3;
	const perm_user_contributor 	= 4;
	const perm_user_manager 		= 5;
	const perm_user_publisher 		= 6;
	
	public function __construct()
	{		
		$callbacks = array(
	    	//'parse_form_data' => array($this, 'OnParseFormData')
	    );
		$this->modules  = new Modules($GLOBALS["config"]["modules"], $callbacks);			// Load modules
		$this->database = new YEdDatabase(
			$GLOBALS["config"]["db"]["signatures"]["host"], 
			$GLOBALS["config"]["db"]["signatures"]["dbname"],
			$GLOBALS["config"]["db"]["signatures"]["username"], 
			$GLOBALS["config"]["db"]["signatures"]["password"]
		);
	}
	
	public function __destruct()
	{
		
	}
	
	public static function getInstance()
	{
		if ( !isset(self::$instance)) {
			self::$instance = new self;
		}	
		return self::$instance;
	}
	
	public function CheckPermissions($permission, $rule_id = NULL)
	{
		global $user;
		return $this->HasPermission($user->Id(), $permission, $rule_id);
	}
	
	// Check if we can touch the file
	public function HasPermission($user, $permission, $rule_id = null)
	{	
		if (!$user) return False;
				
		// Edit permissions
		if ($permission == 'read') {			
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_reader, self::perm_user_contributor, self::perm_user_manager, self::perm_user_publisher));
		} 
		else if ($permission == 'add') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_contributor, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'edit' && $rule_id) {
			$rule = $this->GetRule($rule_id);
			if (!$rule) return False;			
			if ($rule["author_id"] == $user && UCUser::ValidateUserPermission($user, array(self::perm_user_contributor))) return True;	// Contributor can only edit own rules
			else return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		} 
		else if ($permission == 'add_file') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'edit_file') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'clear_history') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'clear_recycle') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'validate') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'publish') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_publisher));
		}
		return False;
	}
	
	public function GetUsers(){
		global $user_db;
		return $user_db->UsersFullData();
	}
	
	public function ModuleAction($action, $api) {
		return $this->modules->Notify($action, $api);	// Call modules, data is passed by reference
	}
	
	public function GetFiles($folder_id)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		return $this->database->GetFiles();
	}
	
	public function ExportFile($file_id)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		// Generate file
		$file_path	= tempnam(sys_get_temp_dir(), 'yed');
		$file_name 	= basename($file_path);
		$content 	= $this->GetFileExport($file_id, $file_name);
		$file 		= fopen($file_path, 'w');
		if (!$file) { return false; }
		
		fwrite($file, $content);
		fclose($file);		
		
		// Generate headers
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        header('Content-Length: '.filesize($file_path));
        header('Last-Modified: '.gmdate('D, d M Y H:i:s T', filemtime($file_path)));
        readfile($file_path);
        return True;
	}
	
	public function ExportRule($rule_id)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		// Generate file
		$file_path	= tempnam(sys_get_temp_dir(), 'yed');
		$file_name 	= basename($file_path);
		$content 	= $this->GetRuleExport($rule_id, $file_name);
		$file 		= fopen($file_path, 'w');
		if (!$file) { return false; }
		
		fwrite($file, $content);
		fclose($file);		
		
		// Generate headers
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        header('Content-Length: '.filesize($file_path));
        header('Last-Modified: '.gmdate('D, d M Y H:i:s T', filemtime($file_path)));
        readfile($file_path);
        return True;
	}
	
	public function GetFileExport($file_id, &$file_name)
	{
		$content = "";
		
		$file_data = $this->GetFile($file_id);		
		$file_name = $file_data["name"] . ".yar";
		
		// Imports
		foreach($file_data["imports"] as $import)
		{
			$content .= "import " . $import . "\n";
		}
		if (!empty($file_data["imports"])) $content .= "\n"; 
		
		// Rules
		$rules = $this->GetRules($file_id);		
		foreach($rules as $rule)
		{
			$dummy 	  = "";
			$content .= $this->GetRuleExport($rule["id"], $dummy) . "\n";
		}
		
		return $content;
	}
	
	public function GetRuleExport($rule_id, &$file_name)
	{
		$content 	= "";		
		$rule 		= $this->GetRule($rule_id);	
		$file_name  = $rule["name"] . ".yar";
		
		// comment
		if (!empty($rule["comment"])) {
			$content .= "/*\n" . $rule["comment"] . "\n*/\n";
		}
		
		if ($rule["is_private"]) {
			$content .= "private ";
		}
		else if ($rule["is_global"]) {
			$content .= "global ";
		} 
		
		$content 	.= "rule " . $rule["name"];
		
		// tags
		if (!empty($rule["tags"])) 
		{
			$content .= " :";
			foreach($rule["tags"] as $tag) {
				$content .= " " . $tag;
			}
		}
		
		$content .= "\n{\n";
		
		// metas
		if (!empty($rule["metas"]) || !empty($rule["threat"]) || !empty($rule["author"])) 
		{
			$content .= "    meta:\n";
			$content .= "        author = " . $rule["author"] . "\n";
			$content .= "        threat = " . $rule["threat"] . "\n";			
			foreach($rule["metas"] as $meta) {
				$content .= "        " . $meta["name"] . " = ";
				if ($meta["type"] == "bool") 		$content .= $meta["value"];
				else if ($meta["type"] == "int") 	$content .= $meta["value"];
				else								$content .= "\"" . $meta["value"] . "\"";
				$content .= "\n";
			}
		}
		
		// strings
		if (!empty($rule["strings"])) 
		{
			$content .= "    strings:\n";
			foreach($rule["strings"] as $string) {
				$content .= "        " . $string["name"] . " = " . $string["value"] . "\n";
			}
		}
		
		// condition
		$content .= "    condition:\n        " . $rule["cond"] . "\n";
		
		$content .= "}\n";
		return $content;
	}
	
	public function GetFile($file_id)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		return $this->database->GetFile($file_id);
	}	
	
	public function AddFile($file_name, $imports)
	{
		if (!$this->CheckPermissions('add_file'))
			return False;
					
		$id = $this->database->AddFile($file_name, $imports);
		if ($id != 0) {
			$this->AddFileActionToHistory('add', $id, $file_name);
		}
		return $id;
	}
	
	public function UpdateFile($file_id, $file_name, $imports)
	{
		if (!$this->CheckPermissions('edit_file'))
			return False;
		
		$old_file 	= $this->GetFile($file_id);			
		$success 	= $this->database->UpdateFile($file_id, $file_name, $imports);
		if ($success) {
			$this->AddFileActionToHistory('edit', $file_id, $file_name, $this->GetFile($file_id), $old_file);
		}
		return $success;
	}
	
	public function DeleteFile($file_id)
	{
		if (!$this->CheckPermissions('edit_file'))
			return False;
		
		$old_file 	= $this->GetFile($file_id);	
		$success 	= $this->database->DeleteFile($file_id);
		if ($success) {
			$this->database->DeleteFileMetas($file_id);
			$this->AddFileActionToHistory('delete', $file_id, $old_file["name"], array(), $old_file);
			
			// remove rules
			$rules_to_remove = $this->database->GetRules($file_id, -1, YEdDatabase::status_all);
			foreach($rules_to_remove as $rule) {
				$success = $success && $this->DeleteRule($rule["id"]);
			}
		}
		return $success;
	}
	
	public function CopyFile($file_id, &$new_name)
	{
		if (!$this->CheckPermissions('edit_file'))
			return 0;
		
		$file 	= $this->database->GetFile($file_id);		
		$id 	= $this->database->CopyFile($file_id);
		if ($id != 0) 
		{
			$this->database->CopyFileMetas($file_id, $id);			
			
			// Copy rules and move into new file
			$rules_to_copy = $this->database->GetRules($file_id, -1, YEdDatabase::status_not_recyclebin);
			foreach($rules_to_copy as $rule) {
				$new_rule_id = $this->CopyRule($rule["id"], $rule["name"]);
				$this->MoveRule($new_rule_id, $id);
			}
			
			// Change name
			if (empty($new_name)) {
				$new_name = $file["name"];
			}
			while ($this->database->FileExists($new_name)) {
				$new_name = $new_name . " (copy)";
			}
			$this->database->RenameFile($id, $new_name);
			$this->AddFileActionToHistory('add', $id, $new_name);
		}
		return $id;
	}
	
	//=====================================
	
	public function GetRules($file_id = -1, $limit = -1, $status = YEdDatabase::status_not_recyclebin)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		$rules = $this->database->GetRules($file_id, $limit, $status);	
		$users = $this->GetUsers();
		foreach($rules as &$rule)
		{
			$rule["author"] = "";
			foreach($users as $user)
			{
				if ($user["id"] == $rule["author_id"])
				{
					$rule["author"] = $user["display_name"];
				}
			}
		}
		return $rules;
	}
	
	public function SearchRules($params)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		$rules = $this->database->SearchRules($params);
		$users = $this->GetUsers();
		foreach($rules as &$rule)
		{
			$rule["author"] = "";
			foreach($users as $user)
			{
				if ($user["id"] == $rule["author_id"])
				{
					$rule["author"] = $user["display_name"];
				}
			}
		}
		return $rules;
	}
	
	public function GetRule($rule_id)
	{
		if (!$this->CheckPermissions('read'))
			return NULL;
		
		$rule = $this->database->GetRule($rule_id);
		if (empty($rule)) return NULL;
		
		// Special metas
		$rule["author_id"] 	= $this->database->GetRuleMetaValue($rule_id, "__author");
		$rule["comment"] 	= $this->database->GetRuleMetaValue($rule_id, "__comment");
		$rule["threat"] 	= $this->database->GetRuleMetaValue($rule_id, "__threat");
		$rule["is_public"] 	= $this->database->GetRuleMetaValue($rule_id, "__public");
		
		// Retrieve author information
		$rule_user 			= new UCUser($rule["author_id"], true);		
		$rule["author"] 	= $rule_user->DisplayName();
		
		// Get metas
		$rule["metas"]		= $this->database->GetMetas($rule_id);
		
		// Get strings		
		$rule["strings"]	= $this->database->GetStrings($rule_id);
		
		return $rule;
	}
	
	public function CreateRule($rule_content)
	{
		if (!$this->CheckPermissions('add'))
			return 0;
		
		$id = $this->database->CreateRule($rule_content);
		if ($id == 0) {
			return 0;
		}
		
		$this->database->CreateRuleMeta($id, "__author", $rule_content->author_id);
		$this->database->CreateRuleMeta($id, "__comment", $rule_content->comment);
		$this->database->CreateRuleMeta($id, "__threat", $rule_content->threat);
		$this->database->CreateRuleMeta($id, "__public", $rule_content->is_public);
		
		// Create metas
		foreach($rule_content->metas as $meta)
		{
			$meta_type  = 'string';
			$meta_type = $this->ParseMetaValue($meta->value, $meta_value);			
			$this->database->CreateOrUpdateMeta($id, $meta->name, $meta_value, $meta_type);
		}
		
		// Create strings				
		foreach($rule_content->strings as $string)
		{	
			$this->database->CreateOrUpdateString($id, $string->name, $string->value);
		}
		
		$this->database->MarkFileAsUpdated($rule_content->file_id);
		$this->AddRuleActionToHistory('add', $id, $rule_content->name, $this->GetRule($id), NULL);
		return $id;
	}
	
	public function UpdateRule($rule_id, $rule_content)
	{
		if (!$this->CheckPermissions('edit', $rule_id))
			return False;
		
		$old_value = $this->GetRule($rule_id);
		$success = $this->database->UpdateRule($rule_id, $rule_content);	
		
		$this->database->UpdateRuleMeta($rule_id, "__author", $rule_content->author_id);
		$this->database->UpdateRuleMeta($rule_id, "__comment", $rule_content->comment);
		$this->database->UpdateRuleMeta($rule_id, "__threat", $rule_content->threat);
		$this->database->UpdateRuleMeta($rule_id, "__public", $rule_content->is_public);
		
		// Update metas
		//================================
		$existing_metas	= $this->database->GetMetas($rule_id);		
		
		// Remove old
		foreach($existing_metas as $meta_existing)
		{
			$found = false;
			foreach($rule_content->metas as $meta_new) {
				if ($meta_existing["name"] == $meta_new->name) {
					$found = true;
				}
			}
			if (!$found) {
				$this->database->DeleteMeta($rule_id, $meta_existing["name"]);
			}
		}
		
		// Update or create
		foreach($rule_content->metas as $meta)
		{
			$meta_type  = 'string';
			$meta_type = $this->ParseMetaValue($meta->value, $meta_value);			
			$this->database->CreateOrUpdateMeta($rule_id, $meta->name, $meta_value, $meta_type);
		}
		
		// Update strings
		//================================
		$existing_strings	= $this->database->GetStrings($rule_id);		
		
		// Remove old
		foreach($existing_strings as $string_existing)
		{
			$found = false;
			foreach($rule_content->strings as $string_new) {
				if ($string_existing["name"] == $string_new->name) {
					$found = true;
				}
			}
			if (!$found) {
				$this->database->DeleteString($rule_id, $string_existing["name"]);
			}
		}
		
		// Update or create
		foreach($rule_content->strings as $string)
		{		
			$this->database->CreateOrUpdateString($rule_id, $string->name, $string->value);
		}	
		
		$this->database->MarkFileAsUpdated($rule_content->file_id);
		$this->AddRuleActionToHistory('edit', $rule_id, $rule_content->name, $this->GetRule($rule_id), $old_value);
		return $success;
	}
	
	public function DeleteRule($rule_id)
	{
		if (!$this->CheckPermissions('edit', $rule_id))
			return False;
		
		$rule 	 = $this->database->GetRule($rule_id);
		$success = $this->database->DeleteRule($rule_id);
		if ($success) {
			$this->database->DeleteRuleMetas($rule_id);
			
			// delete metas
			$this->database->DeleteMetas($rule_id);
			
			// delete strings
			$this->database->DeleteStrings($rule_id);
			
			$this->database->MarkFileAsUpdated($rule["file_id"]);
			$this->AddRuleActionToHistory('delete', $rule_id, $rule["name"], NULL, $rule);
		}
		return $success;
	}
	
	public function MoveRuleToRecycleBin($rule_id)
	{
		if (!$this->CheckPermissions('edit', $rule_id))
			return False;
		
		$rule 	 = $this->database->GetRule($rule_id);
		$success = $this->database->MoveRuleToRecycleBin($rule_id);
		if ($success) {			
			$this->database->MarkFileAsUpdated($rule["file_id"]);
			$this->AddRuleActionToHistory('recyclebin', $rule_id, $rule["name"], NULL, $rule);
		}
		return $success;
	}
	
	public function RestoreRule($rule_id)
	{
		if (!$this->CheckPermissions('edit', $rule_id))
			return False;
		
		$rule 	 = $this->database->GetRule($rule_id);
		$success = $this->database->RestoreRule($rule_id);
		if ($success) {			
			$this->database->MarkFileAsUpdated($rule["file_id"]);
			$this->AddRuleActionToHistory('restore', $rule_id, $rule["name"], NULL, $rule);
		}
		return $success;
	}
	
	public function CopyRule($rule_id, &$new_name)
	{
		if (!$this->CheckPermissions('add'))
			return False;
		
		$rule 	= $this->database->GetRule($rule_id);		
		$id 	= $this->database->CopyRule($rule_id);
		if ($id != 0) 
		{
			$this->database->CopyRuleMetas($rule_id, $id);
			
			// Change name
			if (empty($new_name)) {
				$new_name = $rule["name"];
			}
			while ($this->database->RuleExists($new_name)) {
				$new_name = $new_name . " (copy)";
			}
			$this->database->RenameRule($id, $new_name);
			
			// copy metas
			$existing_metas	= $this->database->GetMetas($rule_id);
			foreach($existing_metas as $meta_existing) {
				$this->database->CreateOrUpdateMeta($id, $meta_existing["name"], $meta_existing["value"], $meta_existing["type"]);
			}
			
			// copy strings
			$existing_strings = $this->database->GetStrings($rule_id);
			foreach($existing_strings as $string_existing) {
				$this->database->CreateOrUpdateString($id, $string_existing["name"], $string_existing["value"]);
			}
			
			$this->database->MarkFileAsUpdated($rule["file_id"]);
			$this->AddRuleActionToHistory('add', $id, $new_name, $rule, NULL);
		}
		return $id;
	}
	
	public function MoveRule($rule_id, $file_id)
	{
		if (!$this->CheckPermissions('edit', $rule_id))
			return False;
		
		$rule 	 = $this->database->GetRule($rule_id);		
		$success = $this->database->MoveRule($rule_id, $file_id);
		if ($success) {
			$this->AddRuleActionToHistory('edit', $rule_id, $rule["name"], $this->GetRule($rule_id), $rule);
		}
		return $success;
	}
	
	//====================================================
	
	public function GetStorageInfo() 
	{
	   	$obj = new stdClass();
		$obj->files 	= $this->database->GetFilesCount();
		$obj->rules 	= $this->database->GetTotalRulesCount();	
		return $obj;
    } 
	
	public function GetSubmissionsPerUser()	
	{
		$data = $this->database->GetSubmissionsPerUser();
		foreach ($data as &$uploader_data)
		{
			 $uploader_data["avatar"] 	= "";
			 $uploader_data["name"] 	= "Unknown";
			
			 // Fetch user data
		     $user_obj = new UCUser($uploader_data['uploader']);
		     if (!$user_obj) {
		     	continue;
		     }
			
			// Get user data
			$uploader_data["avatar"] = ResizeImage($user_obj->Avatar(), 72, 72);
		    $uploader_data["name"]   = $user_obj->Name();
		}	
		return $data;
	}
	
	public function GetTags()	{
		return $this->database->GetTags();
	}
	
	public function GetSubmissions($days_count)	{
		return $this->database->GetSubmissions($days_count);
	}
	
	//====================================================
	
	public function ParseMetaValue($meta_value, &$sanitized_meta_value)
	{
		// Bool type
		$lo_value = strtolower($meta_value);
		if ($lo_value == "true" || $lo_value == "false") 
		{
			$sanitized_meta_value = $lo_value;
			return "bool";
		}
		// Int type
		$int_value = intval($meta_value);
		if ($int_value != 0)
		{
			$sanitized_meta_value = $int_value;
			return "int";
		}		
		// String type
		$sanitized_meta_value = $meta_value;
		return "string";
	}
	
	//====================================================
	
	public function ClearHistory()
	{
		if (!$this->CheckPermissions('clear_history'))
			return False;
		
		return $this->database->ClearHistory();
	}
	
	public function GetHistory($limit)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		$history 	= $this->database->GetHistory($limit);	
		$users 		= $this->GetUsers();
		foreach($history as &$item)
		{
			$item["user_name"] = "";
			foreach($users as $user)
			{
				if ($user["id"] == $item["user"])
				{
					$item["user_name"] = $user["display_name"];
				}
			}
		}
		return $history;
	}
	
	public function AddFileActionToHistory($action, $file_id, $file_name, $file_value = array(), $file_old_value = array())
	{
		global $user;		
		$item 				= new stdClass();
		$item->user 		= $user->Id();
		$item->action 		= $action;
		$item->item_id 		= $file_id;
		$item->item_type    = 'file';
		$item->item_name 	= $file_name;
		$item->item_value 	= json_encode($file_value);
		$item->item_oldvalue = json_encode($file_old_value);		
		return $this->database->AddToHistory($item);
	}
	
	public function AddRuleActionToHistory($action, $rule_id, $rule_name, $rule_value = array(), $rule_old_value = array())
	{
		global $user;		
		$item 				= new stdClass();
		$item->user 		= $user->Id();
		$item->action 		= $action;
		$item->item_id 		= $rule_id;
		$item->item_type    = 'rule';
		$item->item_name 	= $rule_name;
		$item->item_value 	= json_encode($rule_value);	
		$item->item_oldvalue = json_encode($rule_old_value);		
		return $this->database->AddToHistory($item);
	}
	
	//====================================================
	
	public function ClearRecycleBin()
	{
		if (!$this->CheckPermissions('clear_recycle'))
			return False;
		
		// remove rules
		$success = true;
		$rules_to_remove = $this->database->GetRules(-1, -1, YEdDatabase::status_recyclebin);
		foreach($rules_to_remove as $rule) {
			$success = $success && $this->DeleteRule($rule["id"]);
		}
		return $success;
	}
	
	public function SearchThreat($request)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		return $this->database->SearchThreat($request);
	}
	
	public function SearchRuleName($request)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		return $this->database->SearchRuleName($request);
	}
	
	//====================================================
	
	public function ExecuteCron()
	{		
	    // Modules
		$data = array();
		$this->modules->Notify("OnExecuteCron", $data);	// Call modules, data inside is passed by reference
	}
	
	public function CreateDatabase()
	{
		$success = $this->database->Create();		
		$data = array();
		$this->modules->Notify("OnCreateDatabase", $data);	// Call modules, data inside is passed by reference
		return $success;
	}
}