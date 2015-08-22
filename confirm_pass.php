<?php

include 'includes/config.php';

$con	= mysql_connect(HOSTNAME,USERNAME,PASSWORD);


if(isset($_GET['email']) && isset($_GET['token']))
{
   $email=$_GET['email'];
   $pass=urldecode($_GET['token']);
   $con	= mysql_connect(HOSTNAME,USERNAME,PASSWORD);
   $select_db=mysql_select_db("trackheritage",$con);

   if (!$con) {
    die('Could not connect: ' . mysql_error());
    }
     $qry="update users set password='".$pass."' where email='".$email."' and token='".$pass."' ";
     $run=mysql_query($qry);
   
     $qry1="update users set token=0 where email='".$email."' and token='".$pass."' ";
     $run1=mysql_query($qry1);



   if($run)
   {
    
   	 echo '<h2>Congratulations password successfully reset </h2>';
   }
   else
   {
   	echo '<h2>Sorry could not reset you password please try again';
   }




}
else
{
	echo 'Sorry Unauthorized access';
}



?>

