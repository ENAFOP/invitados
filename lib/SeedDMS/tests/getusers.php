<?php
include("config.php");
include("SeedDMS/SeedDMS_Core.php");

$db = new SeedDMS_Core_DatabaseAccess($g_config['type'], $g_config['hostname'], $g_config['user'], $g_config['passwd'], $g_config['name']);
$db->connect() or die ("Could not connect to db-server \"" . $g_config['hostname'] . "\"");

$dms = new SeedDMS_Core_DMS($db, $g_config['contentDir'], $g_config['contentOffsetDir']);

$users = $dms->getAllUsers();
foreach($users as $user)
	echo $user->getId()." ".$user->getLogin()." ".$user->getFullname()."\n";

?>
