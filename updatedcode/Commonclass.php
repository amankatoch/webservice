<?php

class Commonclass{
	
	/*****************************************************************************************************
	 * Constructor use to get connected to server and selecting database
	 ******************************************************************************************************/
	function __construct(){
		
		$con	= mysql_connect(HOSTNAME,USERNAME,PASSWORD);
		
		if(!$con){
			$response['error']		= 1;
			$response['response']['error']	='unable to connect to server';
			echo json_encode($response);
			die;
			
		}		
		if($con){
			$rec	= mysql_select_db(DBNAME,$con);
			if(!$rec){
				$response['error']		= 1;
				$response['response']['error']	='Unable to select Database';
				echo json_encode($response);
				die;
			 }		
		}
		//ob_start();
		//session_start();
	}
	/********************************************************************************
	* function is used to set the query and will return the resource identifier 
	*********************************************************************************/	
	function query($query){
		if($query){
			$result	= mysql_query($query);
			return $result;
		}		
	}
	
	function fetch_object($result){
		if(mysql_num_rows($result)>0){
			
			while($rec	= mysql_fetch_object($result)){
				$row[]	= $rec;
			}
			return $row;
		}
	}
	
	function fetch_array($result){
		if($result && mysql_num_rows($result)){
			$row	= array();
			while($rec	= mysql_fetch_assoc($result)){
				$row[]	= $rec;
			}
			return $row;
		}
	}	
	
	function fetchNumRows($result){
		return mysql_num_rows($result);
	}
	
	function closeCon(){
		mysql_close();
	}	
	
	function lastInsertedId(){
		return mysql_insert_id();
	}
	
	/********************************************************************************
	* function is used to set and get the password 
	*********************************************************************************/	
	function setPassword($password){
		$password = base64_encode($password);
		$salt5 = mt_rand(10000,99999);		
		$salt3 = mt_rand(100,999);		
		$salt1 = mt_rand(0,25);
		$letter1 = range("a","z");
		$salt2 = mt_rand(0,25);
		$letter2 = range("A","Z");
		$password = base64_encode($letter2[$salt2].$salt5.$letter1[$salt1].$password.$letter1[$salt2].$salt3.$letter2[$salt1]);
		return str_replace("=", "#", $password);
	}
	
	function getPassword($password){
		$password = base64_decode(str_replace("#", "=", $password));
		return $password = base64_decode(substr($password,7,-5));				
	}
	
	/*************************************************************************** 
	 * function is used to register device 
	 * Params required: first_name,lase_name,phone,email,user_name,password.	 
	 * return status, message and user_id
	 ****************************************************************************/
	
	function userRegistration($data){
		$response	= array();
		$requiredData['email'] = @trim($data->email);
		$requiredData['user_name'] = @trim($data->user_name);
		$requiredData['password'] = $this->setPassword(@trim($data->password));

		foreach($requiredData AS $key=>$val){
			if(trim($val) == ''){
				$response['status']	= 'Error';
				$response['message'] = 'Please Specify '.ucwords(str_replace("_"," ",$key));				
				return $response;
			}
		}
		
		if($this->checkUserByUserName(addslashes($requiredData['user_name']))){
				$response['status']	= 'Error';
				$response['message'] = 'User already exists with this user name '.       $requiredData['user_name'];				
				return $response;
		}
		
		if($this->checkUserByEmail(addslashes($requiredData['email']))){
				$response['status']	= 'Error';
				$response['message'] = 'User already exists with this email '.$requiredData['email'];				
				return $response;
		}
		
		
			$query	= "INSERT INTO `users` (`email`, `user_name`, `password`, `auth_code`, `user_type`, `logtime`) VALUES ('".$requiredData['email']."', '".$requiredData['user_name']."', '".$requiredData['password']."', '".@trim($data->password)."', '1', '".date('Y-m-d H:i:s')."')";
			$this->query($query);
		
				
		
		$user_id = $this->lastInsertedId();		
		/*$to = $requiredData['email'];
		$subject = "TrackMyHeritage account ";		
		$tokan = $this->setPassword($requiredData['user_name']."#/#".$user_id);
		$message = "Dear ".$requiredData['user_name'].",<br><br>
					Thanks for registered with TrackMyHeritage!<br>
					Your account has been created successfully. Your login credentials are given below:<br>
					<b>User Name  -  ".$requiredData['user_name']." <br>
					Password  - ".@trim($data->password)." <br><br></b>
					If you didn't sign up to TrackMyHeritage, please discard this e-mail and we won't e-mail you again.<br><br>
					With Regards<br>
					Team Support";
		$this->sendMail($message,$to,$subject,$from=CONTACT_EMAIL);*/
		$response['status']	= 'Success';
		$response['message'] = 'User registered successfully';
		$response['user_id']  = $user_id;		
		$response['user_name']  = $requiredData['user_name'];		
		$response['user_type']  = 1;		
		return $response;
	}
	
	
	/*************************************************************************** 
	 * function is used for User Confirmation
	 * Params required: action.	 
	 * return ???.
	 ****************************************************************************/
	
	function userConfirmation($data){
		$requiredData['action'] = $this->getPassword(@trim($data->action));
		$user_name_id = explode("#/#",$requiredData['action']);
		$rec = $this->query("UPDATE `users` SET `status`=1 where `user_name` = '".@$user_name_id[0]."' AND `id`= ".@$user_name_id[1]);
		if($rec){
			$url = HTTP_HOST."confirm.php?action=1";
			echo "<meta http-equiv=Refresh content=0;url=".$url.">";			
			exit;
		}else{
			$url = HTTP_HOST."confirm.php?action=0";
			echo "<meta http-equiv=Refresh content=0;url=".$url.">";			
			exit;		
		}
	}
	
	/*************************************************************************** 
	 * function is used to check User exist or not By email
	 * Params required: email.	 
	 * return true or false.
	 * used within class.
	 ****************************************************************************/
	 
	function checkUserByEmail($email){
		$rec	= $this->query("SELECT `id` from `users` where `email` = '$email' AND `user_type` = '1'");		
		if($rec && mysql_num_rows($rec)>0){			
			//if user with the email got found
			return true;
		}else{
			//if user with the email got not found
			return false;
		}
	}	
	
	/*************************************************************************** 
	 * function is used to check User exist or not By UserName
	 * Params required: user_name.	 
	 * return true or false.
	 * used within class.
	 ****************************************************************************/
	function checkUserByUserName($user_name){
		$rec	= $this->query("SELECT `id` from `users` where `user_name` = '$user_name' ");		
		if($rec && mysql_num_rows($rec)>0){		
			//if user with the user_name got found
			return true;
		}else{
			//if user with the user_name got not found
			return false;
		}
	}
	
	
	
	
	
	
	
	/*************************************************************************** 
	 * function is used to check User exist or not By UserID
	 * Params required: user_id.	 
	 * return true or false.
	 * used within class.
	 ****************************************************************************/
	function checkUserByUserID($user_id){
		$rec	= $this->query("SELECT * from `users` where `id` = ".$user_id);		
		if($rec && mysql_num_rows($rec) == 1){
			//if user with the user_name got found
			return $rec;
		}else{
			//if user with the user_name got not found
			return false;
		}
	}
	
	/*************************************************************************** 
	 * function is used to login 
	 * Params required: user_name,password.	
	 * return status, message and user_id
	 ****************************************************************************/
	function userLogin($data){
		//die($data);
		$response	= array();

				
			$requiredData['user_name'] = @trim($data->user_name);
			$requiredData['password'] = @trim($data->password);
			
			foreach($requiredData AS $key=>$val){
				if(trim($val) == ''){
					$response['status']	= 'Error';
					$response['message'] = 'Please Specify '.ucwords(str_replace("_"," ",$key));				
					return $response;
				}			
			}
			$rec = $this->query("SELECT `id`, `user_name`, `password`, `user_type`, `status`, `token` from `users` where `user_name` = '".$requiredData['user_name']."' ");
			if($rec && mysql_num_rows($rec)== 1){
				$user_info = mysql_fetch_object($rec);
				
				if($requiredData['user_name'] == $user_info->user_name && $requiredData['password'] == $this->getPassword($user_info->password)){
					
					$response['status']	= 'Success';
					$response['message'] = 'User login Successfully';
					$response['user_id']  = $user_info->id;
					$response['user_name']  = $user_info->user_name;
					$response['user_type']  = 1;
					return $response;
				}elseif($requiredData['user_type'] == $user_info->user_type){
					$response['status']	= 'Error';
					$response['message'] = 'Sorry! You cannot access another account';
					return $response;
				}else{
					$response['status']	= 'Error';
					$response['message'] = 'Invalid user name or password. Please try again';
					return $response;
				}
			}else{
				$response['status']	= 'Error';
				$response['message'] = 'Invalid user name or password. Please try again';
				return $response;
			}
		
		}

		/*************************************************************************** 
	 * function is used to setplaces 
	 * Params required: user_id,city,country,year,lat,long.	
	 * return status, message 
	 ****************************************************************************/
	    function addPlaces($data)
	    {  
	    	$response=array();

	    	$requiredData['user_id'] = @trim($data->user_id);
	    	$requiredData['user_city']=@trim($data->user_city);
	    	$requiredData['user_country']=@trim($data->user_country);
	    	$requiredData['user_year']=@trim($data->user_year);
	    	$requiredData['user_lat'] = @trim($data->user_lat);
	    	$requiredData['user_long']=@trim($data->user_long);
	    	
	    	foreach($requiredData AS $key=>$val){
			if(trim($val) == ''){
				$response['status']	= 'Error';
				$response['message'] = 'Please Specify '.ucwords(str_replace("_"," ",$key));				
				return $response;
		    	}
		     }


	    	 if($requiredData['user_id'] == ''){
					$response['status']	= 'Error';
					$response['message'] = 'Sorry! user id is missing ';
					return $response;
				}
				elseif($requiredData['user_city'] == ''){
					$response['status']	= 'Error';
					$response['message'] = 'Sorry! city is missing ';
					return $response;
				}elseif($requiredData['user_country'] == ''){
					$response['status']	= 'Error';
					$response['message'] = 'Sorry! country is missing ';
					return $response;
				}
				elseif($requiredData['user_country'] == ''){
					$response['status']	= 'Error';
					$response['message'] = 'Sorry! year is missing ';
					return $response;
				}
				else
				{
                  
			     $query	= "INSERT INTO `places` (`user_id`, `city`, `country`, `year`, `lat`, `long`) VALUES ('".$requiredData['user_id']."', '".$requiredData['user_city']."', '".$requiredData['user_country']."','".$requiredData['user_year']."','".$requiredData['user_lat']."','".$requiredData['user_long']."')";
			     $this->query($query);
			     
                 $response['status']='Success';
                 $response['message']='Places information stored successfully';
				 $response['place_id']=mysql_insert_id();
				 return $response;
				 }
        }



          function getPlaces($data)
	    {  
	    	$response=array();

	    	$requiredData['user_id'] = @trim($data->user_id);
	    		    	
	    	foreach($requiredData AS $key=>$val){
			if(trim($val) == ''){
				$response['status']	= 'Error';
				$response['message'] = 'Please Specify '.ucwords(str_replace("_"," ",$key));				
				return $response;
		    	}
		     }
		 
             $result=array(array());
			$query	= "select * from places where user_id='".$requiredData['user_id']."' ";
			$rec=$this->query($query);
			if($rec && mysql_num_rows($rec)>= 1){
				$i=0;
				while($row=mysql_fetch_array($rec))
				
				{
					$result[$i]['place_id']=$row['id'];
					$result[$i]['user_id']=$row['user_id'];
					$result[$i]['city']=$row['city'];
					$result[$i]['country']=$row['country'];
					$result[$i]['year']=$row['year'];
					$result[$i]['lat']=$row['lat'];
					$result[$i]['long']=$row['long'];
                     $i++;
				}
				
				
			}else{
				$response['status']	= 'Error';
				$response['message'] = 'No places found for the user';
				return $response;
			}
			$response['status']='Success';
			$response['message']='Places for user are';
			$response['data']=$result;
			return $response;


	  	 }

}
?>
