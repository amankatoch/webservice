<?php
$msg = '';
$msg1 = '';
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 1){
	$msg = '<span style="color:#009933">Thanks for signing up to TrackMyHeritage!</span>';
	$msg1 = 'Your email address is confirmed! You can login to TrackMyHeritage App!';
}else{
	$msg = '<span style="color:#ff0000">ERROR</span>';
	$msg1 = '<span style="color:#ff0000">Wrong Request!</span>';
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Confirmation</title>
	<script type="text/javascript">
		function outoClose(){
			window.location.assign("http://gmail.com/");			
		} 
	</script>
	</head>
	
	<body onload="setTimeout('outoClose()', 30000)">
		
		<div style="text-align: center;font-style: italic;font-size: x-large;">
			<div>
				<?php echo $msg; ?>
			</div>
			<div>
				<?php echo $msg1; ?>
			</div>
		</div>
		
	</body>
</html>
