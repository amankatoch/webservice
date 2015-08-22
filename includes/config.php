<?php
/********************************************
 * Defining Constants
 * Used within whole application
 DB: TrackMyHeritage
 *******************************************/

define('HOSTNAME','50.62.209.49:3306');	//HOST NAME
define('USERNAME','thorlarssen');		//USERNAME
define('PASSWORD','thor@1234567');		//PASSWORD
define('DBNAME','trackheritage');	//DATABASE NAME

$root	= explode('/',$_SERVER['REQUEST_URI']);
$rootPath	= $root[1];
define("HTTP_HOST","http://".$_SERVER['HTTP_HOST']."/".$rootPath."/himanshul/MyShare/"); //ROOT PATH TO THE APPLICATION
//define("IMAGE_URL","http://".$_SERVER['HTTP_HOST']."/$rootPath/thumbs/"); //ROOT PATH TO THE APPLICATION
define("CONTACT_EMAIL","kapil_khurana@esferasoft.com");
define("TEST_EMAIL","kapil_khurana@esferasoft.com,sheenam_garg@esferasoft.com,niraj_kumar@esferasoft.com");

//ob_start();
//session_start();

?>

