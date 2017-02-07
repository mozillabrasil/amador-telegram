<?php

define('__ROOT__', dirname(__DIR__));

require_once __ROOT__.'/init.php';

use Telegram\Bot\Api;
$telegram = new Api($config['telegram']['token']);

// Function to log errors
function error( $error ) {

	global $config;

	// Logs folder
	if ( (!file_exists($config['bot']['logs'])) && (!mkdir($config['bot']['logs'], 0777, true)) ) {
		error('Cannot create log folder: ' . $config['bot']['logs']);
		exit();
	}

	// Append error to log
	file_put_contents($config['bot']['logs'] . 'bot.log', $error . "\n", FILE_APPEND);

}

// Find a substring in a string
function find( $substring, $string ) {
	return (strpos($string, $substring) !== false);
}

// Function to decode JSON
function decodeJson( $string ) {

	// Try to decode JSON
	$json = json_decode($string, true);

	// Cannot decode JSON file
	$jsonerror = json_last_error();
	if ($jsonerror !== JSON_ERROR_NONE) {
		error('Cannot decode JSON file:' . $jsonerror);
		exit();
	}

	// Return JSON
	return $json;

}

// Function to read JSON files
function getJson( $file ) {

	// Try to open JSON file
	$json = file_get_contents($file);

	// Cannot open JSON file
	if ($json === false) {
		error('Cannot open JSON file:' . $file);
		exit();
	}

	// Decode JSON file
	$json = decodeJson($json);

	// Return JSON content as array
	return $json;

}

// Function to save JSON files
function saveJson( $file, $content ) {

	// Try to encode array to JSON
	$content = json_encode($content);

	// Cannot encode to JSON
	if ($content === false) {
		error('Cannot encode as JSON: ' . json_last_error_msg());
		exit();

		// Cannot save content to file
	} else if (file_put_contents($file, $content) === false) {
		error('Cannot save JSON: ' . $file);
		exit();
	}

}

// Log messages
function logMessage( $message ) {

	global $config;

	$year = date('Y');
	$month = date('m');
	$day = date('d');
	$groupid = $message['chat']['id'];
	$groupid = str_replace('-', 'L', $groupid);
	$grouppath = implode('/', [$config['logs']['path'], $groupid]);
	$path = implode('/', [$grouppath, $year, $month]);
	$file = $day . '.json';
	$logpath = implode('/', [$path, $file]);

	// Group Folder
	if ( (!file_exists($path)) && (!mkdir($path, 0777, true)) ) {
		error('Cannot create log folder: ' . $path);
		exit();
	}

	// Group information file
	if ( (!file_exists($grouppath . '/group.json')) ) {
		$content = ["name" => $message['chat']['title']];
		saveJson($grouppath . '/group.json', $content);
	}

	// Create or open log file
	$json = (!file_exists($logpath)) ? [] : getJson($logpath);

	// Push message to log
	array_push($json, $message);

	// Save log file
	saveJson($logpath, $json);

}

// Function to get karma word
function karmaWord( $message, $plus ) {

	// Set separator
	$separator = ($plus) ? '++' : '--';

	// Remove many whitespaces
	while (find('  ', $message)) {
		$message = str_replace('  ', ' ', $message);
	}

	// Parse
	$word = str_replace(' ' . $separator, $separator, $message);
	$word = explode($separator, $word)[0];
	$temp = explode(' ', $word);
	$word = end($temp);
	$word = strtolower($word);

	if (in_array($word, ['c', 'd'])) {
		return false;
	} else {
		return $word;
	}

}

// Function to manage karma db
function karma( $word, $plus, $message ) {

	global $config;
	global $telegram;

	// Value to increase/decrease
	$value = ($plus) ? 1 : -1;

	// Karma database
	$db = implode('/', [$config['bot']['data'], 'karma.json']);

	// Data Folder
	if ( (!file_exists($config['bot']['data'])) && (!mkdir($config['bot']['data'], 0777, true)) ) {
		error('Cannot create data folder: ' . $config['bot']['data']);
		exit();
	}

	// Open or create database
	$karmadb = (!file_exists($db)) ? [] : getJson($db);

	// Set new value
	$karmadb[$word] = (!isset($karmadb[$word])) ? $value : $karmadb[$word] + $value;

	// Save DB
	saveJson($db, $karmadb);

	// Karma message
	// 0
	if ($karmadb[$word] == 0) {
		$karmamsg = $word . ' não tem nenhum MozLove';

		// > 0
	} else if ($karmadb[$word] > 0) {
		$karmamsg = $word . ' tem ' . $karmadb[$word] . ' MozLove';

		// < 0
	} else {
		$karmamsg = $word . ' deve ' . abs($karmadb[$word]) . ' MozLove';
	}

	// Notify
	$notify = $telegram->sendMessage([
    'chat_id' => $message['chat']['id'],
    'text' => $karmamsg,
    'disable_notification' => false,
    'reply_to_message_id' => $message['message_id']
  ]);
  $messageId = $notify->getMessageId();

}

// Open stream content
$socket = file_get_contents('php://input');

// If has content
if (trim($socket) != '') {
	$json = decodeJson($socket);

	if ($json['message']['chat']['type'] == 'group') {
		logMessage($json['message']);
	}

	// Init
	if (find('++', $json['message']['text'])) {

		$word = karmaWord($json['message']['text'], true);

		if ($word !== false) {
			karma($word, true, $json['message']);
		}

	} else if (find('--', $json['message']['text'])) {

		$word = karmaWord($json['message']['text'], false);

		if ($word !== false) {
			karma($word, false, $json['message']);
		}

	}

}

?>
