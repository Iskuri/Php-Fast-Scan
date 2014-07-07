<?php

//$tcpPacket = str_split("cdfc23299f064fb300000000a0023908d99c0000020405b40402080a040f7f740000000001030307",4);
$tcpPacket = str_split("cdfc23299f064fb300000000a002390800000000020405b40402080a040f7f740000000001030307",4);
//$tcpPacket = str_split("d31c23298c3c6cd600000000a002390800000000020405b40402080a0436f95c0000000001030307",4);
$checksumReal = "d99c";

$byteArray = array();

foreach($tcpPacket as $testByte) {
	
	if(strlen($testByte) < 4) {
		$testByte = $testByte."00";
	}
	
	$byteArray[] = hexdec($testByte);
}

$pseudoHeader = generatePseudoHeader("192.168.1.64","8.8.8.8",count($tcpPacket)*2);

$pseudoHeader = array_merge($pseudoHeader,$byteArray);

$byteSum = 0;

foreach($pseudoHeader as $byte) {
	$byteSum += $byte;
}

$byteLeft = ($byteSum & 0xFFFF0000) >> 16;
$byteRight = $byteSum & 0xFFFF;

$byteSum = $byteLeft + $byteRight;

$byteSum = $byteSum ^ 0xFFFF;


die(var_dump(dechex($byteSum)));

function calculateCheckSum($tcpContent) {
	
}

function generatePseudoHeader($srcAddress, $dstAddress, $headerLength) {

	$pseudoHeader = array();

	$srcAddress = splitIpAddress($srcAddress);
	$dstAddress = splitIpAddress($dstAddress);

	$pseudoHeader[0] = $srcAddress[0];
	$pseudoHeader[1] = $srcAddress[1];
	$pseudoHeader[2] = $srcAddress[2];
	$pseudoHeader[3] = $srcAddress[3];
	$pseudoHeader[4] = $dstAddress[0];
	$pseudoHeader[5] = $dstAddress[1];
	$pseudoHeader[6] = $dstAddress[2];
	$pseudoHeader[7] = $dstAddress[3];
	$pseudoHeader[8] = 0x00;
	$pseudoHeader[9] = 0x06;
	$pseudoHeader[10] = 0x00;
	$pseudoHeader[11] = $headerLength;
	
	$pseudoSplit = array();
	
	for($i = 0 ; $i < count($pseudoHeader) ; $i+=2) {
		$pseudoSplit[] = ($pseudoHeader[$i] << 8) + $pseudoHeader[$i+1];
	}
	
	return $pseudoSplit;
}

function splitIpAddress($ipAddress) {
	
	$ipArray = array();
	$ipAddress = explode(".",$ipAddress);
	
	foreach($ipAddress as $byte) {
		$ipArray[] = intval($byte);
	}
	
	return $ipArray;
}

function splitPort($portNumber) {

	return array(($portNumber & 0xFF00) >> 8, $portNumber & 0xFF);
}

?>
