/*
8.7 Reverse an inout array.
*/
function array87($number_array) {
	$reverse_array = array_reverse($number_array,true);
	print_r($reverse_array);
}

$input_array=[1,2,3,4,5,6,7,8,9,0 ];

echo "<br>"."Reverse Array: ".print_r($input_array);

$reverse_array = array87($input_array);
