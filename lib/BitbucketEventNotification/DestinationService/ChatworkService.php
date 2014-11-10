<?php
namespace BitbucketEventNotification\DestinationService;

use BitbucketEventNotification\Api\ChatworkApiClient;
use BitbucketEventNotification\PullRequest\PullRequestCommentCreated;
use BitbucketEventNotification\PullRequest\PullRequestCreated;
use BitbucketEventNotification\PullRequest\PullRequestDeclined;
use BitbucketEventNotification\PullRequest\PullRequestMerged;
use BitbucketEventNotification\PullRequest\PullRequestUpdated;

/**
 * This class is a class that represents the type of chatwork services.
 *
 * @package BitbucketEventNotification\DestinationService
 */
class ChatworkService extends DestinationService
{
    /**
     * constructor.
     */
    public function __construct($pullRequest)
    {
        parent::__construct('Chatwork', $pullRequest);
    }

    /**
     * @inheritdoc
     */
    public function getApiClient()
    {
        return new ChatworkApiClient();
    }

    /**
     * @inheritdoc
     */
    public function postMessage($extendedParams = array())
    {
        $notifyMessage = $this->getNotifyMessage();

        /**
         * @var ChatworkApiClient $apiClient
         */
        $apiClient = $this->getApiClient();
        $response = $apiClient->postMessage($extendedParams['room_id'], $notifyMessage);

        return $response;
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
            $notify .= sprintf("[info]");
            $notify .= sprintf("[title]Pull request has been created by %s. Please review(bow)[/title]", $data['author']['display_name']);
            $notify .= sprintf("[CREATED] #%d %s", $data['id'], $data['title']);
            if (strlen($data['description']) > 0) {
                $notify .= sprintf("\n%s", $data['description']);
            }
            $notify .= sprintf("\nhttps://bitbucket.org/%s/pull-request/%d", $data['destination']['repository']['full_name'], $data['id']);
            $notify .= sprintf("[/info]");
        } else if ($this->pullRequest instanceof PullRequestDeclined) {
            $notify .= sprintf("[info]");
            $notify .= sprintf("[title]Pull request has been declined by %s:([/title]", $data['author']['display_name']);
            $notify .= sprintf("[DECLINED] %s", $data['title']);
            $notify .= sprintf("[/info]");
        } else if ($this->pullRequest instanceof PullRequestCommentCreated) {
            $notify .= sprintf("[info]");
            $notify .= sprintf("[title]Comment was posted by %s(*)[/title]", $data['user']['display_name']);
            $notify .= sprintf("%s", $data['content']['raw']);
            $notify .= sprintf("\n%s", $this->replaceUrlForLink($data['links']['html']['href']));
            $notify .= sprintf("[/info]");
        } else if ($this->pullRequest instanceof PullRequestMerged) {
            $notify .= sprintf("[info]");
            $notify .= sprintf("[title]Pull request has been merged by %s. Good job8-)[/title]", $data['author']['display_name']);
            $notify .= sprintf("[MERGED] %s", $data['title']);
            $notify .= sprintf("[/info]");
        } else if ($this->pullRequest instanceof PullRequestUpdated) {
            $notify .= sprintf("[info]");
            $notify .= sprintf("[title]Pull request has been updated by %s. Please re-review:p[/title]", $data['author']['display_name']);
            $notify .= sprintf("[UPDATED] %s", $data['title']);
            $notify .= sprintf("[/info]");
        } else {
            $notify = null;
        }
        return $notify;
    }

    /**
     * Replace endpoint url to url link.
     *
     * @param string $url
     * @return string
     */
    private function replaceUrlForLink($url)
    {
        return str_replace('://api.', '://', $url);
    }
}