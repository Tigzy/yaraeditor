<?php

// Interface for MRF modules
abstract class IModule
{
	protected $config = array();	
	protected $callbacks = array(
    	//'parse_form_data' => null,
    );
	
	public function __construct(array $mod_conf = array(), $callbacks = null)
	{
		$this->config = $mod_conf;
		if ($callbacks) {
        	$this->callbacks = $callbacks + $this->callbacks;
        }
	}
	
	public function __destruct()
	{
		
	}
	
	protected function execute_callback($name, $params = array()) {
    	if (isset($this->callbacks[$name])) {
    		return call_user_func_array($this->callbacks[$name], $params);
    	}
    	return False;
    }
    
    public function GetConfig() {
    	return $this->config;
    }
	
	//abstract public function OnFileUpload(&$file);		// Function called when a file has been uploaded
}