BitbucketEventNotification
==========================

Notify Chatwork or Slack, when Bitbucket pull request events occured.

## Notification of flow

* Pull Request on Bitbucket
    * occurred hook post
* BitbucketEventNotification (this application)
    * api post
* Chat Services (Chatwork, Slack...)

## Requires

* PHP 5.3+ with cURL with composer
* Access token of Chatwork, if you want notification to that.
* Access token of Slack, if you want notification to that. (xoxp-xxxxx)

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

## Parameters of gateway.php

|Key|Description|Example for Chatwork|Example for Slack|
|:---|:---|:---|:---|
|destination_service|Post destination service name.|chatwork|slack|
|room_id|Post destination.|1000000000|#bitbucket, C1234567890|
