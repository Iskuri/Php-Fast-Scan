<?php

$sock = socket_create(AF_INET, SOCK_RAW, SOL_TCP);
$dstPort = 9001;

//die(var_dump(splitPort($dstPort)));

$header = array();
$dstPortSplit = splitPort($dstPort);
$srcPortSplit = splitPort(53000);

$header[0] = $srcPortSplit[0];
$header[1] = $srcPortSplit[1];
$header[2] = $dstPortSplit[0];
$header[3] = $dstPortSplit[1];
$header[4] = 0xFA; // sequence number
$header[5] = 0x34; // sequence number
$header[6] = 0x7e; // sequence number
$header[8] = 0x17; // sequence number
$header[9] = 0x00; // ack number
$header[10] = 0x00; // ack number
$header[11] = 0x00; // ack number
$header[12] = 0x00; // ack number

$offsetHeaders = getOffsetHeaders();
$header[13] = $offsetHeaders[0];
$header[14] = $offsetHeaders[1];
$header[15] = 0x39;// window size
$header[16] = 0x08;// window size
	
// checksum stuff (annoying)
$header[17] = 0;
$header[18] = 0;

// options
$header[19] = 0x00;
$header[20] = 0x00;
$header[21] = 0x02;
$header[22] = 0x04;

$header[23] = 0x05;
$header[24] = 0xb4;

$header[25] = 0x04;
$header[26] = 0x02;


$header[27] = 0x08;
$header[28] = 0x0a;
$header[29] = 0x03;
$header[30] = 0xbf;
$header[31] = 0x2c;
$header[32] = 0xe4;

$header[33] = 0x00;
$header[34] = 0x00;
$header[35] = 0x00;
$header[36] = 0x00;
$header[37] = 0x01;
$header[38] = 0x03;
$header[39] = 0x03;
$header[40] = 0x07;

$pseudoHeader = generatePseudoHeader("192.168.1.64","8.8.8.8",count($header));
$pseudoHeader = array_merge($pseudoHeader,$header);

$checkSum = splitPort(getCheckSum($pseudoHeader));

// checksum stuff (annoying)
$header[17] = $checkSum[0];
$header[18] = $checkSum[1];

socket_sendto($sock, getHeaderPacket($header), strlen(getHeaderPacket($header)), 0, "8.8.8.8", $dstPort);
socket_sendto($sock, getHeaderPacket($header), strlen(getHeaderPacket($header)), 0, "8.8.8.9", $dstPort);

function getCheckSum($packet) {

	$byteSum = 0;

	for($i = 0 ; $i < count($packet) ; $i += 2) {
		$byteSum += ($packet[$i] << 8) + $packet[$i]+1;
	}

	$byteLeft = ($byteSum & 0xFFFF0000) >> 16;
	$byteRight = $byteSum & 0xFFFF;

	$byteSum = $byteLeft + $byteRight;

	$byteSum = $byteSum ^ 0xFFFF;
	
	return $byteSum;
}

function getHeaderPacket($headerArray) {
	
	$headerString = "";
	
	foreach($headerArray as $headerByte) {
		$headerString .= chr($headerByte);
	}
	
	return $headerString;
}

function splitPort($portNumber) {

	return array(($portNumber & 0xFF00) >> 8, $portNumber & 0xFF);
}

function getOffsetHeaders() {
	
	$offsetHeaders = array();
	$offsetHeaders[0] = 0xA0;
	$offsetHeaders[1] = 0x02;
	return $offsetHeaders;
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
	
//	$pseudoSplit = array();
//	
//	for($i = 0 ; $i < count($pseudoHeader) ; $i+=2) {
//		$pseudoSplit[] = ($pseudoHeader[$i] << 8) + $pseudoHeader[$i+1];
//	}
	
	return $pseudoHeader;
}

function splitIpAddress($ipAddress) {
	
	$ipArray = array();
	$ipAddress = explode(".",$ipAddress);
	
	foreach($ipAddress as $byte) {
		$ipArray[] = intval($byte);
	}
	
	return $ipArray;
}


?>
