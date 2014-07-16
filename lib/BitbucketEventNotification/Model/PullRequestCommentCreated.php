<?php
namespace BitbucketEventNotification\Model;

/**
 * This class is a model that represents the pull request hook.
 *
 * @package BitbucketEventNotification\Model
 */
class PullRequestCommentCreated extends PullRequest
{
    /**
     * {@inheritDoc}
     */
    public function toNotifyString()
    {
        $notify = '';
        $notify .= sprintf("[info]");
        $notify .= sprintf("Comment was posted by %s (*)", $this->data['user']['display_name']);
        $notify .= sprintf("%s", $this->data['content']['raw']);
        $notify .= sprintf("\n%s", $this->data['links']['html']['href']);
        $notify .= sprintf("[/info]");
        return $notify;
    }
}