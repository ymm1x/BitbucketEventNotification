<?php
namespace BitbucketEventNotification\DestinationService;

use BitbucketEventNotification\Api\SlackApiClient;
use BitbucketEventNotification\PullRequest\PullRequest;
use BitbucketEventNotification\PullRequest\PullRequestCommentCreated;
use BitbucketEventNotification\PullRequest\PullRequestCreated;
use BitbucketEventNotification\PullRequest\PullRequestDeclined;
use BitbucketEventNotification\PullRequest\PullRequestMerged;
use BitbucketEventNotification\PullRequest\PullRequestUpdated;

/**
 * This class is a class that represents the type of slack services.
 *
 * @package BitbucketEventNotification\DestinationService
 */
class SlackService extends DestinationService
{
    /**
     * constructor.
     */
    public function __construct($pullRequest)
    {
        parent::__construct('Slack', $pullRequest);
    }

    /**
     * @inheritdoc
     */
    public function postMessage($extendedParams = array())
    {
        $message = '';
        $message .= $this->getNotifyMessage();
        $message .= $this->getSpecialMessage();

        /**
         * @var SlackApiClient $apiClient
         */
        $apiClient = $this->getApiClient();
        $response = $apiClient->postMessage($extendedParams['room_id'], $message);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getApiClient()
    {
        return new SlackApiClient();
    }

    /**
     * Get special message string for post.
     *
     * @return string|null null if failed
     */
    private function getSpecialMessage()
    {
        // TODO: 設定ファイルに逃がす
        $user_name_dict = array(
            '@ks-tetsuya-mori'       => '@monry',
            '@ks-yoshinori-hirasawa' => '@y-hirasawa',
            '@ks-shogo-maezawa'      => '@ks-shogo-maezawa',
        );

        $data = $this->pullRequest->getData();
        $message = $data['content']['raw'];
        foreach ($user_name_dict as $bitbucket_name => $slack_name) {
            $message = preg_replace("!{$bitbucket_name}!", $slack_name, $message);
        }
        $message = preg_replace('!\!\[[^]]+\]\(([^)]+)\)!', '\1', $message);
        if ($message) {
            $message = "\n{$message}";
        }
        return $message;
    }

    /**
     * Get notify message string for post.
     *
     * @return string|null null if failed
     */
    private function getNotifyMessage()
    {
        $data = $this->pullRequest->getData();

        $notify = '';
        if ($this->pullRequest instanceof PullRequestCreated) {
            $notify .= sprintf("Pull request has been created by %s. Please review.", $data['author']['display_name']);
            $notify .= sprintf("\nhttps://bitbucket.org/%s/pull-request/%d", $data['destination']['repository']['full_name'], $data['id']);
        } else if ($this->pullRequest instanceof PullRequestDeclined) {
            $notify .= sprintf("Pull request has been declined by %s.", $data['author']['display_name']);
        } else if ($this->pullRequest instanceof PullRequestCommentCreated) {
            $notify .= sprintf("Comment was posted by %s.", $data['user']['display_name']);
            $notify .= sprintf("\n%s", PullRequest::replaceUrlForLink($data['links']['html']['href']));
        } else if ($this->pullRequest instanceof PullRequestMerged) {
            $notify .= sprintf("Pull request has been merged by %s. Good job.", $data['author']['display_name']);
        } else if ($this->pullRequest instanceof PullRequestUpdated) {
            $notify .= sprintf("Pull request has been updated by %s. Please re-review.", $data['author']['display_name']);
        } else {
            $notify = null;
        }
        return $notify;
    }


    /**
     * Get attachments data.
     *
     * @return array
     */
    private function getAttachments()
    {
        $data = $this->pullRequest->getData();

        $fields = array();

        if ($this->pullRequest instanceof PullRequestCreated) {
            $fields[] = array(
                'title' => 'Author',
                'value' => $data['author']['display_name'],
                'short' => true,
            );
            $fields[] = array(
                'title' => 'Title',
                'value' => sprintf("#%d %s", $data['id'], $data['title']),
                'short' => true,
            );
            $fields[] = array(
                'title' => 'Source',
                'value' => $data['source']['branch']['name'],
                'short' => true,
            );
            $fields[] = array(
                'title' => 'Destination',
                'value' => $data['destination']['branch']['name'],
                'short' => true,
            );
            if (strlen($data['description']) > 0) {
                $fields[] = array(
                    'title' => 'Description',
                    'value' => sprintf("\n%s", $data['description']),
                    'short' => false,
                );
            }
        } else if ($this->pullRequest instanceof PullRequestDeclined) {
            $fields[] = array(
                'title' => 'Author',
                'value' => $data['author']['display_name'],
                'short' => true,
            );
            $fields[] = array(
                'title' => 'Title',
                'value' => sprintf("%s", $data['title']),
                'short' => true,
            );
        } else if ($this->pullRequest instanceof PullRequestCommentCreated) {
            $fields[] = array(
                'title' => 'Author',
                'value' => $data['user']['display_name'],
                'short' => true,
            );
            $fields[] = array(
                'title' => 'Content',
                'value' => sprintf("%s", $data['content']['raw']),
                'short' => true,
            );
        } else if ($this->pullRequest instanceof PullRequestMerged) {
            $fields[] = array(
                'title' => 'Author',
                'value' => $data['author']['display_name'],
                'short' => true,
            );
            $fields[] = array(
                'title' => 'Title',
                'value' => sprintf("%s", $data['title']),
                'short' => true,
            );
        } else if ($this->pullRequest instanceof PullRequestUpdated) {
            $fields[] = array(
                'title' => 'Author',
                'value' => $data['author']['display_name'],
                'short' => true,
            );
            $fields[] = array(
                'title' => 'Title',
                'value' => sprintf("%s", $data['title']),
                'short' => true,
            );
        } else {
            $notify = null;
        }

        $attachments = array(
            array(
                'fields' => $fields
            )
        );

        return $attachments;
    }

}
