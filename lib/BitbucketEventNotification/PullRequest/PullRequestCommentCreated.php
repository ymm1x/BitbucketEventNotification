<?php
namespace BitbucketEventNotification\PullRequest;

use BitbucketEventNotification\DestinationService\ChatworkService;
use BitbucketEventNotification\DestinationService\SlackService;

/**
 * This class is a model that represents the pull request hook.
 *
 * @package BitbucketEventNotification\PullRequest
 */
class PullRequestCommentCreated extends PullRequest
{
    /**
     * {@inheritDoc}
     */
    public function toNotifyString()
    {
        $notify = '';
        if ($this->destinationService instanceof ChatworkService) {
            $notify .= sprintf("[info]");
            $notify .= sprintf("[title]Comment was posted by %s(*)[/title]", $this->data['user']['display_name']);
            $notify .= sprintf("%s", $this->data['content']['raw']);
            $notify .= sprintf("\n%s", $this->replaceUrlForLink($this->data['links']['html']['href']));
            $notify .= sprintf("[/info]");
        } else if ($this->destinationService instanceof SlackService) {
            $notify .= sprintf("[info]");
            $notify .= sprintf("[title]Comment was posted by %s(*)[/title]", $this->data['user']['display_name']);
            $notify .= sprintf("%s", $this->data['content']['raw']);
            $notify .= sprintf("\n%s", $this->replaceUrlForLink($this->data['links']['html']['href']));
            $notify .= sprintf("[/info]");
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