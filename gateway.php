<?php
const DEBUG_MODE = false;
const APP_DIR = __DIR__;

ini_set('display_errors', 0);

require 'lib/autoload.php';
require 'vendor/autoload.php';

use BitbucketEventNotification\DestinationService\DestinationService;
use BitbucketEventNotification\JsonParser\JsonParser;
use BitbucketEventNotification\Log\MLog;
use BitbucketEventNotification\Network\AccessSource;
use BitbucketEventNotification\PullRequest\PullRequest;
use Monolog\Logger;

header('Content-type: application/json; charset=utf-8');

// Initialize Monolog instance for debug mode
if (DEBUG_MODE) {
    MLog::initInstance(Logger::INFO);
    MLog::getInstance()->info('Access from ip address: ' . $_SERVER['REMOTE_ADDR']);
}

// Check arguments
if (!isset($_GET['room_id']) || !$_GET['room_id']) {
    MLog::getInstance()->err('Invalid room id parameter.');
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Invalid access.'));
    exit;
}

if (!isset($_GET['destination_service']) || !$_GET['destination_service']) {
    // default service: chatwork (keep backward compatibility)
    $_GET['destination_service'] = 'chatwork';
}

// Access source ip check
$accessSource = new AccessSource($_SERVER['REMOTE_ADDR']);
if ($accessSource->isForbidden()) {
    MLog::getInstance()->err('Unauthorized access.');
    MLog::getInstance()->err('IP Address: ' . $_SERVER['REMOTE_ADDR']);
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Invalid access.'));
    exit;
}
unset($accessSource);

// Get json from chatwork
$inputPath = DEBUG_MODE ? APP_DIR . '/sample-merged-hook-request.json' : 'php://input';
$inputPath = APP_DIR . '/sample-merged-hook-request.json';
$rawJson = file_get_contents($inputPath);
MLog::getInstance()->info('Request json: ' . json_encode(json_decode($rawJson)));

// Parse json
$parser = new JsonParser();
$jsonData = $parser->parse($rawJson);
$pullRequest = PullRequest::create($jsonData);

MLog::getInstance()->info("Pull request type is " . get_class($pullRequest));

if ($pullRequest === null) {
    MLog::getInstance()->err('Failed to parse json data. An unsupported json format.');
    if (!empty($jsonData)) {
        MLog::getInstance()->err('json data:' . print_r($jsonData, true));
    }
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Failed to parse data.'));
    exit;
}

// Decide destination service
$destinationService = DestinationService::create($_GET['destination_service'], $pullRequest);
MLog::getInstance()->info("Destination service type is " . get_class($destinationService));
if (!$destinationService) {
    MLog::getInstance()->err('Invalid destination service parameter.');
    MLog::getInstance()->err('Specified destination service value:' . $_GET['destination_service']);
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Invalid access.'));
    exit;
}

// Post message
$response = $destinationService->postMessage(array('room_id' => $_GET['room_id']));

// Response
if ($response !== null) {
    MLog::getInstance()->info('Return success response.');
    header('HTTP', true, 200);
    echo json_encode($response);
    exit;
} else {
    MLog::getInstance()->err('Return error response.');
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Failed to post message.'));
    exit;
}
