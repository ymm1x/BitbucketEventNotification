BitbucketEventNotification
==========================

Notify Chatwork or Slack, when receive pull request notifications from Bitbucket.

## Notification Flow

1. Pull Request on Bitbucket
2. Hook post request to your server will occur.
3. This application receives the request, and post notification messages to the chat. (Chatwork or Slack)

## Supported Pull Request Notification

- Created
- Merged
- Updated
- Declined
- Comment created (on the pull request page)

## Requires

* PHP 5.3+ with cURL with composer
* Access token of Chatwork, if you want notification to that.
* Access token of Slack, if you want notification to that. (xoxp-xxxxx)
    * If you want to create a token to access to the page of [Slack API](https://api.slack.com/).
* Bitbucket repository with administrator right.

## Installation

1. Get source code from GitHub, either using Git, or by downloading directly.
2. Copy the source files to public directory in your server.
3. Setup correct permissions
    * `chmod -R 777 tmp`
4. Adjust token in your config file.
    * for the Chatwork
        * `cp config/chatwork.json.default config/chatwork.json`
        * `vim config/chatwork.json`
    * for the Slack
        * `cp config/slack.json.default config/slack.json`
        * `vim config/slack.json`
5. Plugin install with composer. (You need to install composer.)
    * `composer install`
6. Please set the following post destination url in chat work your account setting page. (room_id is chatwork room id.)
    * Sample pull request post hook url (for Chatwork):
        * http://example.com/bitbucket_event_notification/gateway.php?destination_service=chatwork&room_id=1000000000
    * Sample pull request post hook url (for Slack):
        * http://example.com/bitbucket_event_notification/gateway.php?destination_service=slack&room_id=C1234567890

## Parameters of gateway.php (GET)

|Key|Description|Example for Chatwork|Example for Slack|
|:---|:---|:---|:---|
|destination_service|Post destination service name.|chatwork|slack|
|room_id|Post destination.|1000000000|#bitbucket, C1234567890|

* If you include the # to the channel name, please URL-encoded.
    * Example: `#bitbucket` -> `%23bitbucket`
