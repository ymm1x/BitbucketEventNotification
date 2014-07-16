BitbucketEventNotification
==========================

Notify Chatwork when pull request events occured.

## Requires

* PHP 5.3+ with cURL with composer
* Access token of Chatwork

## Installation

1. Get source code from GitHub, either using Git, or by downloading directly.
2. Copy the source files to public directory in your server.
3. Setup correct permissions
    * `chmod -R 777 tmp`
4. Adjust chatwork token in your config file.
    * `cp config/chatwork.json.default config/chatwork.json`
    * `vim config/chatwork.json`
5. Plugin install with composer. (You need to install composer.)
    * `composer install`
6. Please set the following post destination url in chat work your account setting page. (room_id is chatwork room id.)
    * Pull Request POST hook:
    * http://example.com/bitbucket_event_notification/gateway.php?room_id=1000000000
