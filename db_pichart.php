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
			$sql = "SELECT * 
					FROM sbom 
					WHERE ".$params[0]."='".$params[1]."'
					ORDER BY app_id ASC;";
			
			$output = [];
			
			//echo "SQL query: ".$sql,"<br />";
			if($result = $db->query($sql)){
			
            if ($result->num_rows > 0) {
                   // output data of each row
                while($row = $result->fetch_assoc()) {
				
				$output_str = $row['app_id']."@".$row['app_name']."@".$row['app_version']."@".$row['cmp_id']."@".$row['cmp_name']."@".$row['cmp_version']."@".$row['cmp_type']
						."@".$row['app_status']."@".$row['cmp_status']."@".$row['request_id']."@".$row['request_date']."@".$row['request_status']."@".$row['request_step'];
				if($row['notes'] ==	""){
					$output_str .= "@empty";
				}	
				else{
					$output_str .= "@".$row['notes'];
				}
				
				$output[] = explode('@',$output_str);
			
                }//end while
             }//end if
			  $result->close();
			}
                
	
	echo json_encode($output);
	
?>