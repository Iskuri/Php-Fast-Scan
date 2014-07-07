<?php

$testBytes = str_split("4500003044224000800600008c7c19acae241e2b",4);
$byteArray = array();

foreach($testBytes as $testByte) {
	$byteArray[] = hexdec($testByte);
}

//die(var_dump($byteArray));

for($i = 0 ; $i < 100000 ; $i++) {

	$byteSum = 0;

	foreach($byteArray as $byte) {
		$byteSum += $byte;
	}

	$byteLeft = ($byteSum & 0xFFFF0000) >> 16;
	$byteRight = $byteSum & 0xFFFF;

	$byteSum = $byteLeft + $byteRight;

	$byteSum = $byteSum ^ 0xFFFF;
}
?>
