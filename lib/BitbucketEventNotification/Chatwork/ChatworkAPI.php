<?php
namespace BitbucketEventNotification\Chatwork;

use BitbucketEventNotification\Config\ConfigLoader;

/**
 * This class is api interface for Chatwork.
 * Require token is setting.
 *
 * @package BitbucketEventNotification\Chatwork
 */
class ChatworkAPI
{

    /**
     * Post message to room.
     *
     * @param int $roomId room id of chatwork
     * @param string $message message for post
     * @return mixed returns the response array if api successful.
     *               otherwise, returns null.
     */
    public function postMessage($roomId, $message)
    {
        $option = array('body' => $message);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.chatwork.com/v1/rooms/' . intval($roomId) . '/messages');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-ChatWorkToken: ' . $this->loadAccessToken()));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($option, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($statusCode === 200) {
            if ($response = json_decode($response)) {
                return $response;
            }
        }

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
        return $loader->load('chatwork', 'token');
    }
}