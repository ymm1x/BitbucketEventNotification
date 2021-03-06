<?php
use BitbucketEventNotification\DestinationService\DestinationService;
use BitbucketEventNotification\JsonParser\JsonParser;
use BitbucketEventNotification\Log\MLog;
use BitbucketEventNotification\PullRequest\PullRequest;
use Monolog\Logger;

require 'lib/autoload.php';
require 'vendor/autoload.php';

/**
 * @const Log level for the Monolog.
 */
const LOG_LEVEL = Logger::WARNING;

/**
 * @const Forcibly overwrite to test JSON the request.
 *        The contents of the overwrite json contents see the following URL.
 *        https://confluence.atlassian.com/display/BITBUCKET/Pull+Request+POST+hook+management
 */
const USE_TEST_JSON = false;

/**
 * @const This script directory path.
 */
const APP_DIR = __DIR__;

ini_set('display_errors', 0);
MLog::initInstance(LOG_LEVEL);
MLog::getInstance()->info('Access from ip address: ' . $_SERVER['REMOTE_ADDR']);

header('Content-type: application/json; charset=utf-8');

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

// Get json from chatwork
$inputPath = USE_TEST_JSON ? APP_DIR . '/sample-merged-hook-request.json' : 'php://input';
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
