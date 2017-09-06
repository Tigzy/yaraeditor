<?php
require_once(__DIR__."/src/config.php");
require_once(__DIR__.'/src/core.php');
require_once(__DIR__.'/src/lib/restlib.php');
require_once(__DIR__."/src/lib/usercake/init.php");

$user = null;
$key  = null;

class Rest_Api extends Rest_Rest {
	public function __construct(){
		parent::__construct();				// Init parent contructor	
	}
	
	public function processApi()
	{		
		global $user;
		global $key;
		$current_user = UCUser::getCurrentUser();
		
		// Extract requested API
		$func = isset($_REQUEST['action']) ? strtolower(trim(str_replace("/","",$_REQUEST['action']))) : null;	
		if (!$func && isset($_POST['action'])) $func = strtolower(trim(str_replace("/","",$_POST['action']))) ;	
		
		// Could not extract function, and is not a DELETE request nor a DOWNLOAD request
		if (!$func) {
			$this->response('',406);
		}
		
		// Extract API key
		if($current_user != NULL) // if logged in, we get it from current cookie
			$key = $current_user->Activationtoken();
		else {
			if (!isset($key) && isset($_REQUEST['token'])) 	$key = $_REQUEST['token'];
			if (!isset($key) && isset($_POST['token'])) 	$key = $_POST['token'];	
		}
					
		// Save user id		
		$user = new UCUser(UCUser::GetByAPIKey($key));	
				
		// Go to selected route			
		if((int)method_exists($this,$func) > 0)				$this->$func();
		else												$this->unknown($func);
	}
	
	private function getCore() {
		return new YEdCore();
	}
	
	public function getParameter($key) {
		$key_as_header = 'HTTP_' . strtoupper(trim(str_replace("-","_",$key)));
		$value = isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;   // Search in request
		if (!$value && isset($_POST[$key])) $value = $_POST[$key];  // Search in post
		if (!$value && isset($_SERVER[$key_as_header])) $value = $_SERVER[$key_as_header]; // Search in headers
		return $value;
	}

	private function validateKey() {
		global $key;
		
		if (!isset($key)) $this->response('',401);
		$is_api_valid = UCUser::ValidateAPIKey($key); 
		if (!$is_api_valid) $this->response('',401);	
	}
	
	//===========================================================================
	// Routes
	
	// If the route is unknown, give a chance to the modules
	public function unknown($func) {        
        $core 		= $this->getCore();
		$results 	= $core->ModuleAction($func, $this);
		if (!$results) {
        	$this->response('',404);
        	return false;
        }        
        // Answer handled by the modules in case it's found.
	}
	
	public function getfilestable() 
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$folder 			= -1;
		$folder_param		= $this->getParameter('folder');
		if ($folder_param) $folder = $folder_param;
		
		// Get results
		$core 								= $this->getCore();	
		$data_container 					= new stdClass();
		$data_container->data 				= $core->GetFiles( $folder );
		$data_container->draw 				= 1;
		$data_container->recordsTotal 		= count($data_container->data);
		$data_container->recordsFiltered 	= count($data_container->data);
		if (!is_array($data_container->data)) {
			$this->response("unable to get files",403);
			return false;
		}
		$this->response(json_encode($data_container),200);
	}
	
	public function getfiles() 
	{
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$folder 			= -1;
		$folder_param		= $this->getParameter('folder');
		if ($folder_param) $folder = $folder_param;
		
		// Get results
		$core 				= $this->getCore();	
		$data 				= $core->GetFiles( $folder );
		if (!$data) {
			$this->response("unable to get files",403);
			return false;
		}
		$this->response(json_encode($data),200);
	}
	
	public function getfile() 
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$file_id = $this->getParameter("id");
		if (is_null($file_id)) {$this->response('missing id parameter',400); return false; }	
		
		// Get results
		$core = $this->getCore();	
		$data = $core->GetFile($file_id);
		if (!$data) {
			$this->response("unable to get file",403);
			return false;
		}
		$this->response(json_encode($data),200);
	}
	
	public function exportfile()
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$file_id = $this->getParameter("id");
		if (is_null($file_id)) {$this->response('missing id parameter',400); return false; }	
		
		// Get results
		$core 		= $this->getCore();	
		$results 	= $core->ExportFile($file_id);
		if (!$results) {
			$this->response("unable to export file",403);
			return false;
		}
	}
	
	public function exportrule()
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$rule_id = $this->getParameter("id");
		if (is_null($rule_id)) {$this->response('missing id parameter',400); return false; }	
		
		// Get results
		$core 		= $this->getCore();	
		$results 	= $core->ExportRule($rule_id);
		if (!$results) {
			$this->response("unable to export rule",403);
			return false;
		}
	}
	
	public function getrules() 
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$file 				= -1;
		$file_param			= $this->getParameter('file');
		if ($file_param) $file = $file_param;
		
		$limit 				= -1;
		$limit_param		= $this->getParameter('limit');
		if ($limit_param) $limit = $limit_param;
		
		// Get results
		$core 								= $this->getCore();	
		$data_container 					= new stdClass();
		$data_container->data 				= $core->GetRules( $file, $limit, YEdDatabase::status_not_recyclebin );
		$data_container->draw 				= 1;
		$data_container->recordsTotal 		= count($data_container->data);
		$data_container->recordsFiltered 	= count($data_container->data);
		if (!is_array($data_container->data)) {
			$this->response("unable to get rules",403);
			return false;
		}
		$this->response(json_encode($data_container),200);
	}
	
	public function searchrules() 
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$params 					= new stdClass();
		$params->quick 				= $this->getParameter('quick') ? $this->getParameter('quick') : -1;	
		$params->file 				= $this->getParameter('file') ? $this->getParameter('file') : -1;		
		$params->limit 				= $this->getParameter('limit') ? $this->getParameter('limit') : -1;	
		$params->is_private 		= $this->getParameter('is_private') ? $this->getParameter('is_private') : -1;
		$params->is_global 			= $this->getParameter('is_global') ? $this->getParameter('is_global') : -1;
		$params->is_public 			= $this->getParameter('is_public') ? $this->getParameter('is_public') : -1;
		$params->name 				= $this->getParameter('name') ? $this->getParameter('name') : -1;
		$params->tags 				= $this->getParameter('tags') ? $this->getParameter('tags') : -1;
		$params->author				= $this->getParameter('author') ? $this->getParameter('author') : -1;	
		$params->threat				= $this->getParameter('threat') ? $this->getParameter('threat') : -1;	
		$params->comment			= $this->getParameter('comment') ? $this->getParameter('comment') : -1;	
		$params->metas				= $this->getParameter('metas') ? $this->getParameter('metas') : -1;	
		$params->strings			= $this->getParameter('strings') ? $this->getParameter('strings') : -1;	
		$params->condition   		= $this->getParameter('condition') ? $this->getParameter('condition') : -1;	
		
		// Get results
		$core 								= $this->getCore();	
		$data_container 					= new stdClass();
		$data_container->data 				= $core->SearchRules( $params );
		$data_container->draw 				= 1;
		$data_container->recordsTotal 		= count($data_container->data);
		$data_container->recordsFiltered 	= count($data_container->data);
		if (!is_array($data_container->data)) {
			$this->response("unable to get rules",403);
			return false;
		}
		$this->response(json_encode($data_container),200);
	}
	
	public function getrule() 
	{
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$rule_id = $this->getParameter("id");
		if (is_null($rule_id)) {$this->response('missing id parameter',400); return false; }	
		
		// Get results
		$core = $this->getCore();	
		$rule = $core->GetRule($rule_id);
		if ($rule != NULL) {
			if (!isset($rule["is_public"]) || !$rule["is_public"]) {
				$this->validateKey();
			}			
			$this->response(json_encode($rule),200);
		}
		else
			$this->response("unable to find the rule",404);
	}
	
	public function updaterule() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }		
		$core = $this->getCore();
		
		$rule_id = $this->getParameter("id");
		if (is_null($rule_id)) $rule_id = -1;
				
		$rule_content = $this->getParameter("content");
		if (!$rule_content) {$this->response('missing content parameter',400); return false; }		
		
		// Decode data
		$rule_content = json_decode($rule_content);
		if (!$rule_content) {$this->response('bad content parameter',400); return false; }		
		
		// Sanity check, #1 check fields existence
		if (!isset($rule_content->file_id)) {$this->response('file_id not set',400); return false; }
		if (!isset($rule_content->is_private)) {$this->response('is_private not set',400); return false; }
		if (!isset($rule_content->is_global)) {$this->response('is_global not set',400); return false; }
		if (!isset($rule_content->is_public)) {$this->response('is_public not set',400); return false; }	
		if (!isset($rule_content->name)) {$this->response('rulename not set',400); return false; }		
		if (!isset($rule_content->threat)) {$this->response('threat not set',400); return false; }		
		if (!isset($rule_content->comment)) {$this->response('comment not set',400); return false; }
		if (!isset($rule_content->author)) {$this->response('author not set',400); return false; }
		if (!isset($rule_content->tags)) {$this->response('tags not set',400); return false; }
		if (!isset($rule_content->metas)) {$this->response('metas not set',400); return false; }
		if (!isset($rule_content->strings)) {$this->response('strings not set',400); return false; }
		if (!isset($rule_content->condition)) {$this->response('condition not set',400); return false; }
		
		// Sanity check, #2 check for mandatory fields
		if (empty($rule_content->file_id)) {$this->response('file cannot be empty',400); return false; }
		if ($rule_content->name == "") {$this->response('rulename cannot be empty',400); return false; }
		if (empty($rule_content->strings)) {$this->response('strings cannot be empty',400); return false; }
		if ($rule_content->condition == "") {$this->response('condition cannot be empty',400); return false; }
				
		$data 		= new stdClass();		
		$data->name = $rule_content->name;
		
		$core = $this->getCore();	
		if ($rule_id == -1) 
		{
			$id = $core->CreateRule($rule_content);
			if ($id == 0) { $this->response('unable to create rule',400); return false; }
			
			$data->id = $id;
			$this->response(json_encode($data), 201);
		}
		else 
		{
			if (!$core->UpdateRule($rule_id, $rule_content)) { $this->response('unable to update rule',400); return false; }
			
			$data->id = $rule_id;
			$this->response(json_encode($data), 200);
		}		
	}
	
	public function deletefile() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$file_id = $this->getParameter("id");
		if (is_null($file_id)) {$this->response('missing id parameter',400); return false; }
		
		$core = $this->getCore();
		if ($core->DeleteFile($file_id)) {		
			$this->response("{}",200);
		}
		else {
			$this->response('Unable to delete file',406); 
			return false;
		}
	}
	
	public function copyfile() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$file_id = $this->getParameter("id");
		if (is_null($file_id)) {$this->response('missing id parameter',400); return false; }
		$file_name = $this->getParameter("name");
		if (is_null($file_name)) $file_name = "";
		
		$core 		= $this->getCore();		
		$new_name 	= $file_name;
		$id 		= $core->CopyFile($file_id, $new_name);
		if ($id == 0) {
			$this->response('Unable to copy file',406); 
			return false;
		}
		
		$data 		= new stdClass();
		$data->id 	= $id;
		$data->name = $new_name;
		
		$this->response(json_encode($data),201);
	}
	
	public function addfile() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$file_name = $this->getParameter("name");
		if (!$file_name) {$this->response('missing name parameter',400); return false; }	
		$file_imports = $this->getParameter("imports");
		if (!$file_imports) $file_imports = array();
		
		$core = $this->getCore();
		$id = $core->AddFile($file_name, $file_imports);
		if ($id == 0) {
			$this->response('Unable to add file',406); 
			return false;
		}
		
		$data 		= new stdClass();
		$data->id 	= $id;
		$data->name = $file_name;
		
		$this->response(json_encode($data),201);
	}
	
	public function updatefile() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$file_id = $this->getParameter("id");
		if (is_null($file_id)) {$this->response('missing id parameter',400); return false; }	
		$file_name = $this->getParameter("name");
		if (!$file_name) {$this->response('missing name parameter',400); return false; }	
		$file_imports = $this->getParameter("imports");
		if (!$file_imports) $file_imports = array();
		
		$core = $this->getCore();
		$id = $core->UpdateFile($file_id, $file_name, $file_imports);
		if ($id == 0) {
			$this->response('Unable to update file',406); 
			return false;
		}
		
		$data 		= new stdClass();
		$data->id 	= $id;
		$data->name = $file_name;
		
		$this->response(json_encode($data),201);
	}
	
	public function moverulerecyclebin() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$rule_id = $this->getParameter("id");
		if (is_null($rule_id)) {$this->response('missing id parameter',400); return false; }
		
		$core = $this->getCore();
		if ($core->MoveRuleToRecycleBin($rule_id)) {		
			$this->response("{}",200);
		}
		else {
			$this->response('Unable to move rule to recycle bin',406); 
			return false;
		}
	}
	
	public function deleterule() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$rule_id = $this->getParameter("id");
		if (is_null($rule_id)) {$this->response('missing id parameter',400); return false; }
		
		$core = $this->getCore();
		if ($core->DeleteRule($rule_id)) {		
			$this->response("{}",200);
		}
		else {
			$this->response('Unable to delete rule',406); 
			return false;
		}
	}
	
	public function restorerule() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$rule_id = $this->getParameter("id");
		if (is_null($rule_id)) {$this->response('missing id parameter',400); return false; }
		
		$core = $this->getCore();
		if ($core->RestoreRule($rule_id)) {		
			$this->response("{}",200);
		}
		else {
			$this->response('Unable to delete rule',406); 
			return false;
		}
	}
	
	public function copyrule() 
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }	
		
		$rule_id = $this->getParameter("id");
		if (is_null($rule_id)) {$this->response('missing id parameter',400); return false; }
		$rule_name = $this->getParameter("name");
		if (is_null($rule_name)) $rule_name = "";
		
		$core 		= $this->getCore();		
		$new_name 	= $rule_name;
		$id 		= $core->CopyRule($rule_id, $new_name);
		if ($id == 0) {
			$this->response('Unable to copy rule',406); 
			return false;
		}
		
		$data 		= new stdClass();
		$data->id 	= $id;
		$data->name = $new_name;
		
		$this->response(json_encode($data),201);
	}
	
	public function getstorageinfo() {
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }
		$core 		= $this->getCore();
		$results 	= $core->GetStorageInfo();
		echo json_encode($results);
	}
	
	public function getsubmissionsperuserdata() {	
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$core 			= $this->getCore();
		$data 			= $core->GetSubmissionsPerUser();	
		echo json_encode($data);
	}
	
	public function gettagsdata() {	
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$core 			= $this->getCore();
		$data 			= $core->GetTags();	
		echo json_encode($data);
	}
	
	public function getsubmissionsdata() {
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$days_count 	= -1;
		$days_count_val = $this->getParameter("days_count");		
		if ($days_count_val) $days_count = $days_count_val;
		
		$core 			= $this->getCore();
		$data 			= $core->GetSubmissions($days_count);	
		$labels 		= array();
		$points 		= array();							
		foreach($data as $val)
		{
		    $labels[] = $val["date"];
		    $points[] = $val["count"];
		}				
		$data_new = new stdClass();
		$data_new->labels 			= $labels;
		$data_new->points 			= $points;
		echo json_encode($data_new);
	}
	
	public function gethistory() 
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$limit 				= -1;
		$limit_param		= $this->getParameter('limit');
		if ($limit_param) $limit = $limit_param;
		
		// Get results
		$core 								= $this->getCore();	
		$data_container 					= new stdClass();
		$data_container->data 				= $core->GetHistory($limit);
		$data_container->draw 				= 1;
		$data_container->recordsTotal 		= count($data_container->data);
		$data_container->recordsFiltered 	= count($data_container->data);
		if (!is_array($data_container->data)) {
			$this->response("unable to get history",403);
			return false;
		}
		$this->response(json_encode($data_container),200);
	}
	
	public function clearhistory()
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }		
		
		$core = $this->getCore();	
		if ($core->ClearHistory()) {		
			$this->response("{}",200);
		}
		else {
			$this->response('Unable to clear history',406); 
			return false;
		}
	}
	
	public function getrecycle() 
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$file 				= -1;
		$file_param			= $this->getParameter('file');
		if ($file_param) $file = $file_param;
		
		$limit 				= -1;
		$limit_param		= $this->getParameter('limit');
		if ($limit_param) $limit = $limit_param;
		
		// Get results
		$core 								= $this->getCore();	
		$data_container 					= new stdClass();
		$data_container->data 				= $core->GetRules( $file, $limit, YEdDatabase::status_recyclebin );
		$data_container->draw 				= 1;
		$data_container->recordsTotal 		= count($data_container->data);
		$data_container->recordsFiltered 	= count($data_container->data);
		if (!is_array($data_container->data)) {
			$this->response("unable to get rules",403);
			return false;
		}
		$this->response(json_encode($data_container),200);
	}
	
	public function clearrecycle()
	{
		$this->validateKey();
		if($this->get_request_method() != "POST"){ $this->response('',406); return false; }		
		
		$core = $this->getCore();	
		if ($core->ClearRecycleBin()) {		
			$this->response("{}",200);
		}
		else {
			$this->response('Unable to clear history',406); 
			return false;
		}
	}
	
	public function searchthreat()
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$request = $this->getParameter("request");
		if (is_null($request)) {$this->response('missing request parameter',400); return false; }
		
		$core = $this->getCore();	
		$data = $core->SearchThreat($request);
		if (!is_array($data)) {
			$this->response("unable to search threats",403);
			return false;
		}
		$this->response(json_encode($data),200);
	}
	
	public function searchrulename()
	{
		$this->validateKey();
		if($this->get_request_method() != "GET"){ $this->response('',406); return false; }		
		
		$request = $this->getParameter("request");
		if (is_null($request)) {$this->response('missing request parameter',400); return false; }
		
		$core = $this->getCore();	
		$data = $core->SearchRuleName($request);
		if (!is_array($data)) {
			$this->response("unable to search threats",403);
			return false;
		}
		$this->response(json_encode($data),200);
	}
}

// Initiiate Library
$api = new Rest_Api;
$api->processApi();

?>