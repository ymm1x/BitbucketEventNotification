<?php
namespace BitbucketEventNotification\Api;

use BitbucketEventNotification\Config\ConfigLoader;
use BitbucketEventNotification\Log\MLog;

/**
 * This class is api interface for Slack.
 * Require token is setting.
 *
 * @package BitbucketEventNotification\Api
 */
class SlackApiClient
{
    /**
     * @const Base api url.
     */
    const BASE_API_URL = 'https://slack.com/api/';

    /**
     * Post message to slack channel.
     *
     * @param mixed $channel Channel to send message to. Can be a public channel, private group or IM channel. Can be an encoded ID, or a name.
     * @param string $message Message for post
     * @param array $attachments Structured message attachments.
     * @return mixed returns the response array if api successful.
     *               otherwise, returns null.
     */
    public function postMessage($channel, $message, array $attachments = array())
    {
        $option = array(
            'token' => $this->loadAccessToken(),
            'channel' => $channel,
            'text' => $message,
            'username' => 'Bitbucket',
            'icon_url' => 'https://slack.global.ssl.fastly.net/20653/img/services/bitbucket_48.png',
            'attachments' => json_encode($attachments),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::BASE_API_URL . 'chat.postMessage');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($option, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($statusCode === 200 && $response = json_decode($response)) {
            if ($response->ok === true) {
                MLog::getInstance()->info("Successful response data:" . json_encode($response));
                return $response;
            }
        }

        MLog::getInstance()->err("Error occurred while executing slack api.");
        MLog::getInstance()->err("Status code: {$statusCode}");
        MLog::getInstance()->err("Response: " . json_encode($response));

        return null;
    }

    /**
     * Get access token from config file.
     *
     * @return mixed returns config value. false is returned if the failed for load.
     */
    public function loadAccessToken()
    {
        $loader = new ConfigLoader(__DIR__ . '/../../../config');
        return $loader->load('slack', 'token');
    }
}