<?php 
require_once(__DIR__."/../src/config.php");
require_once(__DIR__.'/../src/core.php');
require_once(__DIR__."/../src/lib/usercake/init.php");

//=================================================================
//Signatures DB

function Install()
{
	global $user_db;
	$success = true;
	
	//=================================================================
	// Permissions
	
	$permissions_entry = "
	INSERT INTO `".$user_db->Prefix()."permissions` (`id`, `name`) VALUES
	(3, 'Reader'),
	(4, 'Contributor'),
	(5, 'Manager'),
	(6, 'Publisher')
	";
	
	if($user_db->Execute($permissions_entry))
	{
		echo "<p>Inserted custom permissions into ".$user_db->Prefix()."permissions table.....</p>";
	}
	else
	{
		echo "<p>Error inserting permissions.</p>";
		$success = false;
	}

	//=================================================================
	//Malware DB
	
	$core = new YEdCore();
	$success &= $core->CreateDatabase();
	
	return $success;
}

?>