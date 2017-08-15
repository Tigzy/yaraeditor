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
	
	//const perm_user_admin 			= 2;
	
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
	
	public function GetUsers(){
		global $user_db;
		return $user_db->UsersFullData();
	}
	
	public function ModuleAction($action, $api) {
		return $this->modules->Notify($action, $api);	// Call modules, data is passed by reference
	}
	
	public function GetFiles($folder_id)
	{
		return $this->database->GetFiles();
	}
	
	public function ExportFile($file_id)
	{
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
		return $this->database->GetFile($file_id);
	}	
	
	public function AddFile($file_name, $imports)
	{
		return $this->database->AddFile($file_name, $imports);
	}
	
	public function UpdateFile($file_id, $file_name, $imports)
	{
		return $this->database->UpdateFile($file_id, $file_name, $imports);
	}
	
	public function DeleteFile($file_id)
	{
		$success = $this->database->DeleteFile($file_id);
		if ($success) {
			$this->database->DeleteFileMetas($file_id);
			
			// remove rules
			$rules_to_remove = $this->database->GetRules($file_id);
			foreach($rules_to_remove as $rule) {
				$success = $success && $this->DeleteRule($rule["id"]);
			}
		}
		return $success;
	}
	
	public function CopyFile($file_id, &$new_name)
	{
		$file 	= $this->database->GetFile($file_id);		
		$id 	= $this->database->CopyFile($file_id);
		if ($id != 0) 
		{
			$this->database->CopyFileMetas($file_id, $id);
			
			// Copy rules and move into new file
			$rules_to_copy = $this->database->GetRules($file_id);
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
		}
		return $id;
	}
	
	//=====================================
	
	public function GetRules($file_id)
	{
		$rules = $this->database->GetRules($file_id);
		foreach($rules as &$rule)
		{
			$rule_user 			= new UCUser($rule["author_id"], true);		
			$rule["author"] 	= $rule_user->DisplayName();
		}
		return $rules;
	}
	
	public function GetRule($rule_id)
	{
		$rule = $this->database->GetRule($rule_id);
		if (empty($rule)) return NULL;
		
		// Special metas
		$rule["author_id"] 	= $this->database->GetRuleMetaValue($rule_id, "__author");
		$rule["comment"] 	= $this->database->GetRuleMetaValue($rule_id, "__comment");
		$rule["threat"] 	= $this->database->GetRuleMetaValue($rule_id, "__threat");
		
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
		$id = $this->database->CreateRule($rule_content);
		
		$this->database->CreateRuleMeta($id, "__author", $rule_content->author_id);
		$this->database->CreateRuleMeta($id, "__comment", $rule_content->comment);
		$this->database->CreateRuleMeta($id, "__threat", $rule_content->threat);
		
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
		return $id;
	}
	
	public function UpdateRule($rule_id, $rule_content)
	{
		$success = $this->database->UpdateRule($rule_id, $rule_content);	
		
		$this->database->UpdateRuleMeta($rule_id, "__author", $rule_content->author_id);
		$this->database->UpdateRuleMeta($rule_id, "__comment", $rule_content->comment);
		$this->database->UpdateRuleMeta($rule_id, "__threat", $rule_content->threat);
		
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
		return $success;
	}
	
	public function DeleteRule($rule_id)
	{
		$rule 	 = $this->database->GetRule($rule_id);
		$success = $this->database->DeleteRule($rule_id);
		if ($success) {
			$this->database->DeleteRuleMetas($rule_id);
			
			// delete metas
			$this->database->DeleteMetas($rule_id);
			
			// delete strings
			$this->database->DeleteStrings($rule_id);
			
			$this->database->MarkFileAsUpdated($rule["file_id"]);
		}
		return $success;
	}
	
	public function CopyRule($rule_id, &$new_name)
	{
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
		}
		return $id;
	}
	
	public function MoveRule($rule_id, $file_id)
	{
		return $this->database->MoveRule($rule_id, $file_id);
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