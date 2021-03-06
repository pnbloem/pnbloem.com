<?php
 
/*
    The important thing to realize is that the config file should be included in every
    page of your project, or at least any page you want access to these settings.
    This allows you to confidently use these settings throughout a project because
    if something changes such as your database credentials, or a path to a specific resource,
    you'll only need to update it here.
*/
$config = array(
    "db" => array(
        // required
		'database_type' => 'mysql',
		'database_name' => 'pnbloemc_poetry',
		'server' => 'localhost',
		'username' => 'pnbloemc_paul',
		'password' => 'W0lverines01*',
		'charset' => 'utf8',
	 
		// optional
		'port' => 3306
    ),
    "urls" => array(
        "baseUrl" => "http://pnbloem.com"
    ),
    "paths" => array(
        "resources" => "/path/to/resources",
        "images" => array(
            "content" => $_SERVER["DOCUMENT_ROOT"] . "/images/content",
            "layout" => $_SERVER["DOCUMENT_ROOT"] . "/images/layout"
        )
    ),
    "error" => ""
); 
?>