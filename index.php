<?php
/* turn error reporting on */

//ini_set('display_errors', 1);
//ini_set('log_errors', 1);
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
//error_reporting(E_ALL);


include('includes/config.php');

include('includes/Commonclass.php');
include('includes/phpmailer/class.phpmailer.php');

$data = file_get_contents("php://input");
//echo "Contents from  file_get_contents('php://input')----->";
$data = preg_replace("/[\n\r]/","",$data); 
$data = json_decode($data,true);
//print_r($data); die;
if(empty($data)){
	$data = $_POST;
}



if(!empty($data) && is_array($data)){
	foreach($data as $key=>$val){
		if(is_array($val))
			$data[$key]	= $val;
		else
			$data[$key]	= addslashes($val);
	}
	// convert to object.//
	$tempdata = json_encode($data);
	$data = json_decode($tempdata);
}
$common	= new Commonclass();

$functionName = '';
$string	= array();

if(isset($_GET['func'])){
	$functionName	= $_GET['func'];
}

if($functionName != ''){
	
	if(!method_exists($common,$functionName)){		
		$string['status'] = 'Error';
		$string['message'] = 'Method not specifies in this web service';		
	}else{		
		$string	= $common->$functionName($data);		
	}
	
}else{
	$string['status'] = 'Error';
	$string['message'] = 'Please specify Method name';	
}

$common->closeCon();

@header('content-type: application/json');
echo json_encode($string);
die;	
?>
