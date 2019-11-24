<?php
	/*
		https://stackoverflow.com/questions/2367594/open-url-while-passing-post-data-with-jquery
		
		Intermediate, round-about way to get a single value from Jquery to a php superglobal (in this case $_POST)
		
		How to send data from Jquery to another php page which saves it from the PHP POST variable into a SESSION var
		to be used in yet another PHP page which is loaded from the initiating HTML page with jquery
	*/
	
	include("./nav.php");

	$_SESSION['name'] = $_POST['name'];
?>