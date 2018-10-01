<?php
/**
 * Convert Image To Gray Scale 
 * 
 * @author Jeong Ook Moon
 */

// define constant variables for lum case
define("W1", .3); define("W2", .6); define("W3", .1);

function convertImageToGrayScale( $imgIn, $imgOut, $method )
{
	// Create an image identifier from a file
	$image = imagecreatefromjpeg ( $imgIn );
	// Retrieve width & height of the image
	list( $width, $height ) = getimagesize( $imgIn );
	// Return value to identify if a method runs successfully
	$flag = true;
	
	// Record starting time, source: https://stackoverflow.com/questions/6245971/accurate-way-to-measure-execution-times-of-php-scripts
	$s_time = microtime(true);

	// Loop through each pixel of the image
	for( $w_index=0; $w_index<$width; $w_index++ ) {
		for( $h_index=0; $h_index<$height; $h_index++ ) {
			// Get the index of a pixel's color
			$color_index = imagecolorat( $image, $w_index, $h_index );
			// Make it human readable source: http://php.net/manual/en/function.imagecolorsforindex.php
			$rgb = imagecolorsforindex($image, $color_index);
			// Assign each r.g.b. index
			$r = $rgb["red"]; $g = $rgb["green"]; $b = $rgb["blue"];
						
			// Initialize grey variable
			$grey;

			// Assign grey value according to the method parameter
			if( $method === 'avg' ) {	
				$grey = ( $r + $g + $b ) / 3;
			}
			else if( $method === 'light') {
				$grey = ( max( $r, $g, $b ) + min( $r, $g, $b )) / 2;
			}
			else if ( $method === 'lum') {
				$grey = W1 * $r + W2 * $g + W3 * $b;
			}
			
			// Get grey color index where r === g === b
			$grey_index = imagecolorallocate($image, $grey, $grey, $grey);
			
			// Make sure if any method returns false
			if($grey_index === false || $rgb === false || imagesetpixel( $image, $w_index, $h_index, $grey_index ) === false) {
				$flag = false;
			}
		}
	}
	// Record time difference
	$time_diff = microtime(true) - $s_time;

	// Write converted image to a file
	imagejpeg($image, $imgOut);
	// Returns an array containing flag and time difference values
	return $return = array(
		"flag" => $flag,
		"time_diff" => $time_diff,
	);
}
// Call grayscale conversion method and save the return array
$output = convertImageToGrayScale( 'input.jpg', 'output1.jpg', 'avg');

// If the conversion is successful, it shows '1' in the browser
echo ">>Average" . "<br>" . "Conversion Result : " . $output["flag"] . "<br>";
// Display elasped time with 4 decimal places, Source: https://stackoverflow.com/questions/4483540/show-a-number-to-2-decimal-places
echo "Elapsed Time : " . number_format((float)$output["time_diff"]*1000, 4, '.', '') . " milliseconds" . "<br>" . "<br>";

$output = convertImageToGrayScale( 'input.jpg', 'output2.jpg', 'light');
echo ">>Light" . "<br>" . "Conversion Result : " . $output["flag"] . "<br>";
echo "Elapsed Time : " . number_format((float)$output["time_diff"]*1000, 4, '.', '') . " milliseconds" . "<br>" . "<br>";

$output = convertImageToGrayScale( 'input.jpg', 'output3.jpg', 'lum');
echo ">>Luminous" . "<br>" . "Conversion Result : " . $output["flag"] . "<br>";
echo "Elapsed Time : " . number_format((float)$output["time_diff"]*1000, 4, '.', '') . " milliseconds" . "<br>" . "<br>";
?>