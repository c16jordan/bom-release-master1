<?php

// tell the browser that we will be sending JSON as the output
header("Content-Type: application/json; charset=UTF-8");

// We want to read the raw HTTP POST value.  To do so, we use php://input.
// See: http://php.net/manual/en/wrappers.php.php#wrappers.php.input
$json_string = file_get_contents('php://input');
// write out the POST input to a file so we can inspect it
// this is for debugging purposes only!
file_put_contents("json_input_to_php.txt", $json_string);

// try to decode the JSON sent by JavaScript from the browser via the POST request
$json_object = json_decode($json_string);
//echo print_r($json_object);


// make a new empty object to hold our JSON response object
$json_output_object = new stdClass();
// if json_decode returns null, then there was an error decoding the JSON
// See: http://php.net/manual/en/function.json-decode.php
if ($json_object === null) {
    // create a new property named error_message and assign the last JSON decode error to it
    $json_output_object->error_message = json_last_error_msg();
    // encode the PHP JSON object into a JSON string and send it to the browser
    echo json_encode($json_output_object);
    // we can use exit here, since we sent our JSON response
    exit;
}

// write the decoded JSON PHP object to a file so we can inspect it
// this is for debugging purposes only!
file_put_contents("php_decoded_json.txt", print_r($json_object, true));

// if we want to get information out $json_object, we access the property that we want, for example:
// $json_object->tire_qty

// now we'll build the JSON output object that we will send back to JavaScript
// create a new order_number property and assign a random integer to it
$json_output_object->order_number = random_int(500, 1000);

date_default_timezone_set('America/Chicago');
// create a new order_data property and assign the current date and time to it
$json_output_object->order_date = date(DATE_RFC2822);

// encode the PHP JSON object into a JSON string and send it to the browser to be handled by JavaScript
echo json_encode($json_output_object);

?>