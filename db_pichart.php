<?php
	// tell the browser that we will be sending JSON as the output
	header("Content-Type: application/json; charset=UTF-8");
	
	require_once('initialize.php');
	global $db;
	
	// See: http://php.net/manual/en/wrappers.php.php#wrappers.php.input
	$json_string = file_get_contents('php://input');
	//$json_object = $json_string;//json_decode($json_string);
	
	// make a new empty object to hold our JSON response object
	$json_output_object = new stdClass();

	// See: http://php.net/manual/en/function.json-decode.php
	if ($json_string === null) {

		$json_output_object->error_message = json_last_error_msg();
		// encode the PHP JSON object into a JSON string and send it to the browser
		echo json_encode($json_output_object);
		// we can use exit here, since we sent our JSON response
		exit;
	}

			$params = explode("/",$json_string);

			echo $params[0];
			echo $params[1];
			
			$sql = "SELECT * FROM sbom WHERE ".$params[0]."=".$params[1]." ;";
			
			if($result = $db->query($sql)){
			
            if ($result->num_rows > 0) {
                   // output data of each row
                while($row = $result->fetch_assoc()) {
				
	
					
				
                }//end while
             }//end if
			  $result->close();
			}
                

	// now we'll build the JSON output object that we will send back to JavaScript
	// create a new order_number property and assign a random integer to it
	//$json_output_object->order_number = random_int(500, 1000);


	// encode the PHP JSON object into a JSON string and send it to the browser to be handled by JavaScript
	//echo json_encode($json_output_object);
	//echo $json_string;
	//echo "Rats";
	
?>