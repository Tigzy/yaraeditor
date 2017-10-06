<?php
 
/*
    The important thing to realize is that the config file should be included in every
    page of your project, or at least any page you want access to these settings.
    This allows you to confidently use these settings throughout a project because
    if something changes such as your database credentials, or a path to a specific resource,
    you'll only need to update it here.
*/
 
$config = array(
    "version" => "0.6",
    "db" => array(
        "usercake" => array(
            "dbname" => "yed",
            "username" => "root",
            "password" => "",
            "host" => "localhost"
        ),
        "signatures" => array(
            "dbname" => "yed",
            "username" => "root",
            "password" => "",
            "host" => "localhost"
        ),
    ),
    "urls" => array(
        "baseUrl" => "http://localhost/yed/"
    ),
    "leftnav" => array(
    	array(
            "name" => "Home",
            "link" => "/index.php",
            "icon" => "fa fa-home",
        ),
    	array(
    		"name" => "Edition",    		
    		"icon" => "fa fa-pencil",
    		"link" => 
    		array(
    			array(
		            "name" => "List",
		            "link" => "/files.php",
		            "icon" => "fa fa-list",
		        ),
		        array(
		            "name" => "Create",
		            "link" => "/edit.php",
		            "icon" => "fa fa-pencil",
		        ),
		    	array(
		            "name" => "Search",
		            "link" => "/search.php",
		            "icon" => "fa fa-search",
		        ),
    			array(
		            "name" => "History",
		            "link" => "/history.php",
		            "icon" => "fa fa-history",
		        ),
    			array(
		            "name" => "Recycle Bin",
		            "link" => "/recycle.php",
		            "icon" => "fa fa-trash",
		        ),
		    )
    	),
    	array(
    		"name" => "Tests",    		
    		"icon" => "fa fa-refresh",
    		"link" => 
    		array(
    			array(
		            "name" => "List",
		            "link" => "/tests.php",
		            "icon" => "fa fa-list",
		        ),
		    )
    	),
    	array(
            "name" => "Statistics",
            "link" => "/stats.php",
            "icon" => "fa fa-pie-chart",
        ),
    ),
	"modules" => array(
		"yaradata" => array(
			"enabled" => True,
			"class" => "YaraData",
			"priority" => 9,
		),
	),
	"tests" => array(
		"storage" => "/data/storage/"	
	),
	"available_imports" => array(
		"pe","elf","cuckoo","magic","hash","math"
	)
);

$GLOBALS["config"] = $config;
 
/*
    I will usually place the following in a bootstrap file or some type of environment
    setup file (code that is run at the start of every page request), but they work 
    just as well in your config file if it's in php (some alternatives to php are xml or ini files).
*/
 
/*
    Creating constants for heavily used paths makes things a lot easier.
    ex. require_once(LIBRARY_PATH . "Paginator.php")
*/
//defined("LIBRARY_PATH")
//    or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));
     
//defined("TEMPLATES_PATH")
//    or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/templates'));
 
/*
    Error reporting.
*/
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);
 
?>