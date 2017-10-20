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
	
	public function CheckPermissions($permission, $item_id = NULL)
	{
		global $user;
		return $this->HasPermission($user->Id(), $permission, $item_id);
	}
	
	// Check if we can touch the file
	public function HasPermission($user, $permission, $item_id = null)
	{	
		if ($permission != 'read' && !$user) return False;
				
		// Edit permissions
		if ($permission == 'read') {	
			// Needs to be system wide for sharing rules
			return true; //UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_reader, self::perm_user_contributor, self::perm_user_manager, self::perm_user_publisher));
		} 
		else if ($permission == 'add') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_contributor, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'add_comment') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_contributor, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'edit' && $item_id) {
			$rule = $this->GetRule($item_id);
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
		else if ($permission == 'read_all_recycle') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'validate') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'publish') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_publisher));
		}
		else if ($permission == 'add_test') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_contributor, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'run_test') {
			return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_contributor, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'edit_testset') {
			$set = $this->GetTestSet($item_id);		
			if (!$set) return False;	
			if ($set["author"] == $user && UCUser::ValidateUserPermission($user, array(self::perm_user_contributor))) return True;	// Contributor can only edit own rules
			else return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'edit_test') {
			$test = $this->GetTest($item_id);
			$set  = NULL;
			if (!$test || $test["id"] == NULL) {
				$set = $this->GetTestSet($item_id);
			}
			else {
				$set = $this->GetTestSet($test["set_id"]);
			}			
			if (!$set) return False;	
			if ($set["author"] == $user && UCUser::ValidateUserPermission($user, array(self::perm_user_contributor))) return True;	// Contributor can only edit own rules
			else return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
		}
		else if ($permission == 'edit_comment') {
			$set = $this->GetComment($item_id);		
			if (!$set) return False;	
			if ($set["author"] == $user && UCUser::ValidateUserPermission($user, array(self::perm_user_contributor))) return True;	// Contributor can only edit own rules
			else return UCUser::ValidateUserPermission($user, array(self::perm_user_admin, self::perm_user_manager, self::perm_user_publisher));
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
			$content .= "        author = \"" . $rule["author"] . "\"\n";
			$content .= "        threat = \"" . $rule["threat"] . "\"\n";			
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
	
	public function ImportRules($file_id, $content)
	{
		global $user;
		if (!$this->CheckPermissions('add'))
			return False;
		
		// Get file
		$file = $this->GetFile($file_id);
		
		// Parse content with module		
		$data = array('content' => &$content);
		if (!$this->modules->Notify("OnYaraParseString", $data) || !isset($data['rules'])) {
			return False;
		}
		if (isset($data['rules']->valid) && !$data['rules']->valid) {
			return False;
		}
			
		// Add rules
		$success = True;
		$imports_needed = $file["imports"];
		foreach ($data['rules'] as $rule)
		{
			$rule_obj 				= (object) $rule;
			$rule_obj->file_id 		= $file_id;
			$rule_obj->author 		= $user->DisplayName();
			$rule_obj->author_id 	= $user->Id();
			$rule_obj->is_public    = False;
			
			// Merge imports
			$rule_imports 	= array_map( function($item) { return trim($item,'"'); }, $rule_obj->imports);			
			$imports_needed = array_unique(array_merge($imports_needed, $rule_imports));
			
			if (!$this->CreateRule($rule_obj)) {
				$success = False;
			}
		}
			
		// Update file
		$this->UpdateFile($file_id, $file["name"], $imports_needed);
		
        return $success;
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
	
	public function DeleteFiles($ids)
	{
		if (!is_array($ids)) {
			return False;
		}	
		// Verify first
		foreach($ids as $id) {
			if (!$this->CheckPermissions('edit_file', $id))
				return False;
		}
		// Do actual removal
		foreach($ids as $id) {
			if (!$this->DeleteFile($id))
				return False;
		}
		return True;
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
	
	public function GetRules($file_id = -1, $limit = -1, $status = YEdDatabase::status_not_recyclebin, $user = -1)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		$rules = $this->database->GetRules($file_id, $limit, $status, $user);	
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
		
		$rule_content->threat = trim($rule_content->threat, '"');
		
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
	
	public function DeleteRules($ids)
	{
		if (!is_array($ids)) {
			return False;
		}	
		// Verify first
		foreach($ids as $id) {
			if (!$this->CheckPermissions('edit', $id))
				return False;
		}
		// Do actual removal
		foreach($ids as $id) {
			if (!$this->DeleteRule($id))
				return False;
		}
		return True;
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
	
	public function MoveRulesToRecycleBin($ids)
	{
		if (!is_array($ids)) {
			return False;
		}	
		// Verify first
		foreach($ids as $id) {
			if (!$this->CheckPermissions('edit', $id))
				return False;
		}
		// Do actual removal
		foreach($ids as $id) {
			if (!$this->MoveRuleToRecycleBin($id))
				return False;
		}
		return True;
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
	
	public function AddTestSet($test_name, $rule_id)
	{
		global $user;
		if (!$this->CheckPermissions('add_test'))
			return False;
					
		$id = $this->database->AddTestSet($test_name, $rule_id, $user->Id());
		/*if ($id != 0) {
			$this->AddFileActionToHistory('add', $id, $file_name);
		}*/
		return $id;
	}
	
	public function GetTestSets($rule_id, $show_myitems_only)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		// If user cannot read all recycle, filter his own items
		$user_id = -1;
		if ($show_myitems_only)		
		{
			global $user;
			$user_id = $user->Id();
		}
		
		$tests = $this->database->GetTestSets($rule_id, $user_id);
		$users = $this->GetUsers();
		foreach($tests as &$test)
		{
			$test["author"] = "";
			foreach($users as $user)
			{
				if ($user["id"] == $test["author_id"])
				{
					$test["author"] = $user["display_name"];
				}
			}
		}
		return $tests;
	}
	
	public function GetTestSet($id)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		return $this->database->GetTestSet($id);
	}
	
	public function UpdateTestSet($id, $name, $rule_id)
	{
		if (!$this->CheckPermissions('edit_testset', $id))
			return False;
		
		//$old_file 	= $this->GetFile($file_id);			
		$success 	= $this->database->UpdateTestSet($id, $name, $rule_id);
		/*if ($success) {
			$this->AddFileActionToHistory('edit', $file_id, $file_name, $this->GetFile($file_id), $old_file);
		}*/
		return $success;
	}
	
	public function DeleteTestSet($id)
	{
		if (!$this->CheckPermissions('edit_testset', $id))
			return False;
		
		return $this->database->DeleteTestSet($id);
	}
	
	public function DeleteTestSets($ids)
	{
		if (!is_array($ids)) {
			return False;
		}	
		// Verify first
		foreach($ids as $id) {
			if (!$this->CheckPermissions('edit_testset', $id))
				return False;
		}
		// Do actual removal
		foreach($ids as $id) {
			if (!$this->DeleteTestSet($id))
				return False;
		}
		return True;
	}
	
	public function RunTestSet($id)
	{
		if (!$this->CheckPermissions('run_test'))
			return NULL;
		
		$tests = $this->GetTests($id);
		foreach($tests as $test)
		{
			$this->YaraRunTest($test["id"], False);
		}
		$this->UpdateTestSetStatus($id);
		
		// Return data
		$set = $this->GetTestSet($id);
		return $set["status"];
	}
	
	public function UpdateTestSetStatus($id)
	{
		if (!$this->CheckPermissions('edit_testset', $id))
			return False;
		
		$set = $this->GetTestSet($id);
		if (!$set || $set["id"] == NULL) return False;	
		$tests = $this->GetTests($id);
		
		$ran 	= False;
		$passed = True;
		foreach($tests as $test)
		{
			if ($test["status"] == YEdDatabase::status_idle) {
				continue;
			}
			else if ($test["status"] == YEdDatabase::status_passed) {
				$ran 	= True;
				$passed &= True;
			}
			else if ($test["status"] == YEdDatabase::status_failed) {
				$ran 	= True;
				$passed &= False;
			}
		}
		if (!$ran) $passed = False;
		
		// Update tests set status
		if (!$ran) {
			$this->database->SetTestSetStatus($id, YEdDatabase::status_idle);
		}
		else {
			$this->database->SetTestSetStatus($id, $passed ? YEdDatabase::status_passed : YEdDatabase::status_failed);
		}
		return True;
	}
	
	public function ValidateTestData($type, &$content, $file)
	{
		if ($type != 'file' && $type != 'string_ansi' && $type != 'string_unicode' && $type != 'buffer')
			return false;
		
		// Sanity check and formatting for buffer
		if ($type == 'buffer') {
			$content = str_replace(" ", "", $content);	// Remove all spaces (in case of buffer with spaces: DE AD C0 DE)
			$content = strtoupper($content);			// Upper case
			if (!ctype_xdigit($content)) {				// Validate content
				return false;
			}
		}
		else if ($type == 'file') {
			if (!$file || !isset($file["tmp_name"])) {
				return false;
			}		
			// store file
			$md5 	= md5_file($file['tmp_name']);
			$path 	= $GLOBALS["config"]["tests"]["storage"] . $md5;
			$index  = 0;
			while (file_exists($path)) {
				$path = $GLOBALS["config"]["tests"]["storage"] . $md5 . '-' . strval($index);
				$index++;
			}			
			if (!move_uploaded_file($file['tmp_name'], $path)) {
				return false;
			}			
			$content = basename($path);
		}
		return true;
	}
	
	public function AddTest($testset_id, $type, $content, $file)
	{
		if (!$this->CheckPermissions('add_test'))
			return False;
			
		if (!$this->ValidateTestData($type, $content, $file))
			return false;
			
		$id = $this->database->AddTest($testset_id, $type, $content);
		/*if ($id != 0) {
			$this->AddFileActionToHistory('add', $id, $file_name);
		}*/
		return $id;
	}
	
	public function GetTests($testset_id)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		return $this->database->GetTests($testset_id);
	}
	
	public function GetTest($id)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		return $this->database->GetTest($id);
	}
	
	public function UpdateTest($id, $type, $content, $file)
	{
		if (!$this->CheckPermissions('edit_test', $id))
			return False;
		
		if (!$this->ValidateTestData($type, $content, $file))
			return false;
		
		$test = $this->database->GetTest($id);
		if ($test['type'] == 'file') {
			unlink($GLOBALS["config"]["tests"]["storage"] . $test['content']);
		}
		
		//$old_file 	= $this->GetFile($file_id);			
		$success 	= $this->database->UpdateTest($id, $type, $content);
		/*if ($success) {
			$this->AddFileActionToHistory('edit', $file_id, $file_name, $this->GetFile($file_id), $old_file);
		}*/
		return $success;
	}
	
	public function DeleteTest($id)
	{
		if (!$this->CheckPermissions('edit_test', $id))
			return False;
		
		$test = $this->database->GetTest($id);
		if ($test['type'] == 'file') {
			unlink($GLOBALS["config"]["tests"]["storage"] . $test['content']);
		}
			
		return $this->database->DeleteTest($id);
	}
	
	public function CopyTest($id)
	{
		if (!$this->CheckPermissions('edit_test', $id))
			return 0;
		
		$test 	= $this->database->GetTest($id);		
		$new_id = $this->database->CopyTest($id);
		if ($new_id != 0) 
		{
			$test = $this->database->GetTest($new_id);
			if ($test['type'] == 'file') 
			{
				// Duplicate file
				$md5    	= $test['content'];
				$old_path 	= $GLOBALS["config"]["tests"]["storage"] . $md5;
				$path 		= $GLOBALS["config"]["tests"]["storage"] . $md5;
				$index  	= 0;
				while (file_exists($path)) {
					$path = $GLOBALS["config"]["tests"]["storage"] . $md5 . '-' . strval($index);
					$index++;
				}
				if (!copy_file($old_path, $path)) {
					return 0;
				}
				$this->database->UpdateTest($new_id, $test['type'], $path);
			}
			
			//$this->AddFileActionToHistory('add', $id, $new_name);
		}
		return $new_id;
	}
	
	public function RunTest($id)
	{
		if (!$this->CheckPermissions('run_test'))
			return 0;
		
		return $this->YaraRunTest($id);
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
		if (is_numeric($meta_value))
		{
			$sanitized_meta_value = intval($meta_value);;
			return "int";
		}		
		// String type
		$sanitized_meta_value = trim($meta_value, '"');
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
	
	public function AddCommentActionToHistory($action, $rule_id, $rule_name, $comment_value = array(), $comment_old_value = array())
	{
		global $user;		
		$item 				= new stdClass();
		$item->user 		= $user->Id();
		$item->action 		= $action;
		$item->item_id 		= $rule_id;
		$item->item_type    = 'comment';
		$item->item_name 	= $rule_name;
		$item->item_value 	= json_encode($comment_value);	
		$item->item_oldvalue = json_encode($comment_old_value);		
		return $this->database->AddToHistory($item);
	}
	
	//====================================================	
	
	public function GetComment($comment_id)
	{
		if (!$this->CheckPermissions('read'))
			return NULL;
		
		$comment 	= $this->database->GetComment($comment_id);
		$users 		= $this->GetUsers();
		
		global $user;		
		
		$comment["fullname"] = "";
		foreach($users as $user2)
		{
			if ($user2["id"] == $comment["author"]) {
				$comment["fullname"] = $user2["display_name"];		
				$comment["profile_picture_url"] = empty($user2["avatar"]) ? "" : ("data:image/png;base64," . $user2["avatar"]);
			}
		}
		$comment["created_by_current_user"]  = ($user->Id() == $comment["author"]) ? True : False;
		$comment["user_has_upvoted"] 	  	 = False; //TODO
		
		return $comment;
	}
	
	public function GetComments($rule_id)
	{
		if (!$this->CheckPermissions('read'))
			return NULL;
		
		$comments 	= $this->database->GetComments($rule_id);
		$users 		= $this->GetUsers();
		
		global $user;		
		foreach($comments as &$item)
		{
			$item["fullname"] = "";
			foreach($users as $user2)
			{
				if ($user2["id"] == $item["author"]) {
					$item["fullname"] 			 = $user2["display_name"];	
					$item["profile_picture_url"] = empty($user2["avatar"]) ? "" : ("data:image/png;base64," . $user2["avatar"]);
				}
			}
			$item["created_by_current_user"] = ($user->Id() == $item["author"]) ? True : False;
			$item["user_has_upvoted"] 	     = False; //TODO
		}	
		return $comments;
	}
	
	public function AddComment($rule_id, $parent, $pings, $comment)
	{
		if (!$this->CheckPermissions('add_comment'))
			return NULL;
		
		global $user;
		$id = $this->database->AddComment($rule_id, $parent, $comment, $pings, $user->Id());
		if ($id != 0) {
			$rule = $this->GetRule($rule_id);
			$this->AddCommentActionToHistory('add', $rule_id, $rule["name"], $this->GetComment($id), NULL);
		}
		return $id;
	}
	
	public function UpdateComment($comment_id, $pings, $comment)
	{
		if (!$this->CheckPermissions('edit_comment'))
			return NULL;
		
		$old_value = $this->GetComment($comment_id);
		$success = $this->database->UpdateComment($comment_id, $comment, $pings);
		if ($success) {
			$rule 	 = $this->GetRule($old_value["rule_id"]);
			$this->AddCommentActionToHistory('edit', $rule["id"], $rule["name"], $this->GetComment($comment_id), $old_value);	
		}
		return $success;
	}
	
	public function DeleteComment($comment_id)
	{
		if (!$this->CheckPermissions('edit_comment'))
			return NULL;
		
		$old_value = $this->GetComment($comment_id);
		$success = $this->database->DeleteComment($comment_id);
		if ($success) {
			$rule = $this->GetRule($old_value["rule_id"]);
			$this->AddCommentActionToHistory('delete', $rule["id"], $rule["name"], NULL, $old_value);
		}
		return $success;
	}
	
	//====================================================
	
	public function GetRecycleBin($file_id = -1, $limit = -1)
	{
		if (!$this->CheckPermissions('read'))
			return False;
		
		// If user cannot read all recycle, filter his own items
		$user_id = -1;
		if (!$this->CheckPermissions('read_all_recycle'))		
		{
			global $user;
			$user_id = $user->Id();
		}
		
		return $this->GetRules( $file_id, $limit, YEdDatabase::status_recyclebin, $user_id );			
	}
	
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
	
	public function YaraCheckSyntax($rule_id)
	{
		if (!$this->CheckPermissions('read'))
			return NULL;
		
		$rule_name 		= "";
		$rule_export 	= $this->GetRuleExport($rule_id, $rule_name);
		
		$data = array('rule_export' => &$rule_export);
		if (!$this->modules->Notify("OnYaraCheckSyntax", $data) || !isset($data['rule_check'])) {
			return NULL;
		}
		return $data['rule_check'];
	}
	
	public function YaraRunTest($test_id, $update_set_status = True)
	{
		if (!$this->CheckPermissions('read'))
			return NULL;
		
		// Get test content
		$test = $this->GetTest($test_id);
		if (!$test) return NULL;
		$set = $this->GetTestSet($test["set_id"]);
		if (!$set) return NULL;	
		
		// Get rule content
		$rule_name 		= "";
		$rule_export 	= $this->GetRuleExport($set["rule_id"], $rule_name);				
		
		$data = array('rule_export' => &$rule_export, 'test_type' => $test["type"], 'test_data' => $test["content"], 'storage_path' => $GLOBALS["config"]["tests"]["storage"]);
		if (!$this->modules->Notify("OnYaraTest", $data) || !isset($data['rule_test'])) {
			return NULL;
		}
		
		// Update test results
		$this->database->SetTestResults($test_id, json_encode($data['rule_test']));
		
		// Update test status
		$passed = False;
		if (isset($data['rule_test']) && isset($data['rule_test']->has_matches)) {
			if ($data['rule_test']->has_matches) {
				$passed = True;				
			}
		}
		
		// Update status
		$this->database->SetTestStatus($test_id,  $passed ? YEdDatabase::status_passed : YEdDatabase::status_failed);
		
		// Update tests set status
		if ($update_set_status) {
			$this->UpdateTestSetStatus($set["id"]);
		}		
		
		return $data['rule_test'];
	}
	
	//====================================================
	
	public function ExecuteCron()
	{		
		// Update tests
		$tests = $this->database->GetTestsToUpdate();
		foreach($tests as $test) {
			$this->RunTest($test["id"]);
		}
		
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