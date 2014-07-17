<?php
ini_set('display_errors', 0);

require 'lib/autoload.php';
require 'vendor/autoload.php';

use BitbucketEventNotification\Chatwork\ChatworkAPI;
use BitbucketEventNotification\JsonParser\JsonParser;
use BitbucketEventNotification\Model\PullRequestApproval;
use BitbucketEventNotification\Model\PullRequestFactory;
use BitbucketEventNotification\Network\AccessSource;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

header('Content-type: application/json; charset=utf-8');

// Prepare logger (Monolog)
$logger = new Logger('general');
$loggingPath = __DIR__ . '/tmp/logs/error.log';
$logger->pushHandler(new StreamHandler($loggingPath, Logger::ERROR));

// Check arguments
if (!isset($_GET['room_id']) || !$_GET['room_id']) {
    $logger->err('Invalid room id parameter.');
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Invalid access.'));
    exit;
}

// Access source ip check
$accessSource = new AccessSource($_SERVER['REMOTE_ADDR']);
if ($accessSource->isForbidden()) {
    $logger->err('Unauthorized access.');
    $logger->err('IP Address: ' . $_SERVER['REMOTE_ADDR']);
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Invalid access.'));
    exit;
}
unset($accessSource);

// Get json sent from Chatwork
$rawJson = file_get_contents('php://input');

// Test json
/*
$rawJson = <<<EOT
{
    "pullrequest_merged": {
        "description": "",
        "title": "Inbox changes",
        "close_source_branch": true,
        "destination": {
            "commit": {
                "hash": "82d48819e5f7",
                "links": {
                    "self": {
                        "href": "https://api.bitbucket.org/2.0/repositories/evzijst/bitbucket2/commit/82d48819e5f7"
                    }
                }
            },
            "repository": {
                "links": {
                    "self": {
                        "href": "https://api.bitbucket.org/2.0/repositories/evzijst/bitbucket2"
                    },
                    "avatar": {
                        "href": "https://bitbucket.org/m/d864f6bcaa94/img/language-avatars/default_16.png"
                    }
                },
                "full_name": "evzijst/bitbucket2",
                "name": "bitbucket2"
            },
            "branch": {
                "name": "staging"
            }
        },
        "reason": "",
        "source": {
            "commit": {
                "hash": "325625d47b0a",
                "links": {
                    "self": {
                        "href": "https://api.bitbucket.org/2.0/repositories/evzijst/bitbucket2/commit/325625d47b0a"
                    }
                }
            },
            "repository": {
                "links": {
                    "self": {
                        "href": "https://api.bitbucket.org/2.0/repositories/evzijst/bitbucket2"
                    },
                    "avatar": {
                        "href": "https://bitbucket.org/m/d864f6bcaa94/img/language-avatars/default_16.png"
                    }
                },
                "full_name": "evzijst/bitbucket2",
                "name": "bitbucket2"
            },
            "branch": {
                "name": "mfrauenholtz/inbox"
            }
        },
        "state": "MERGED",
        "author": {
            "username": "evzijst",
            "display_name": "Erik van Zijst",
            "links": {
                "self": {
                    "href": "https://api.bitbucket.org/2.0/users/evzijst"
                },
                "avatar": {
                    "href": "https://bitbucket-staging-assetroot.s3.amazonaws.com/c/photos/2013/Oct/28/evzijst-avatar-3454044670-3_avatar.png"
                }
            }
        },
        "date": "2013-11-08T19:49:12.233187+00:00"
    }
}
EOT;
*/

// Parse json
$parser = new JsonParser();
$jsonData = $parser->parse($rawJson);
$pullRequest = PullRequestFactory::create($jsonData);

if ($pullRequest === null) {
    $logger->err('Failed to parse json data. An unsupported json format.');
    if (!empty($jsonData)) {
        $logger->err('json data:' . print_r($jsonData, true));
    }
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Failed to parse data.'));
    exit;
}

$postText = $pullRequest->toNotifyString();
unset($jsonData, $rawJson, $pullRequest);

// Post message
$chatworkAPI = new ChatworkAPI();
$response = $chatworkAPI->postMessage($_GET['room_id'], $postText);
unset($chatworkAPI);

// Response
if ($response !== null) {
    header('HTTP', true, 200);
    echo json_encode($response);
    exit;
} else {
    header('HTTP', true, 403);
    echo json_encode(array('result' => false, 'message' => 'Failed to post message.'));
    exit;
}
