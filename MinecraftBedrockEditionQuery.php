<?php

# referfence url => https://github.com/xPaw/PHP-Minecraft-Query

$ip = '';
$port = 19132;
$timeout = 5;

$socket = fsockopen('udp://' . $ip, $port, $error_code, $error_message, $timeout);

stream_set_timeout($socket, $timeout);
stream_set_blocking($socket, true);

if($socket === false){
    echo $error_message . '(' . $error_code . ')';
    return;
}

// hardcoded magic https://github.com/facebookarchive/RakNet/blob/1a169895a900c9fc4841c556e16514182b75faf8/Source/RakPeer.cpp#L135
$OFFLINE_MESSAGE_DATA_ID = pack('c*', 0x00, 0xFF, 0xFF, 0x00, 0xFE, 0xFE, 0xFE, 0xFE, 0xFD, 0xFD, 0xFD, 0xFD, 0x12, 0x34, 0x56, 0x78);
$command = pack('cQ', 0x01, time()); // DefaultMessageIDTypes::ID_UNCONNECTED_PING + 64bit current time
$command .= $OFFLINE_MESSAGE_DATA_ID;
$command .= pack('Q', 2); // 64bit guid
$length  = strlen($command);

fwrite($socket, $command, $length);

$data = fread($socket, 4096);

$data = substr($data, 35);
$data = explode(';', $data);

$info = [
	'Name' => $data[0] ?? null,
	'Motd' => $data[1] ?? null,
	'Protocol' => $data[2] ?? null,
	'Version' => $data[3] ?? null,
	'Players' => isset($data[4]) ? (int)$data[4] : 0,
	'MaxPlayers' => isset($data[5]) ? (int)$data[5] : 0,
	'ServerId' => $data[6] ?? null,
	'Engine' => $data[7] ?? null,
	'GameMode' => $data[8] ?? null
];

var_dump($info);

?>