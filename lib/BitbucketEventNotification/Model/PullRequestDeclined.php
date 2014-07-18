<?php
namespace BitbucketEventNotification\Model;

/**
 * This class is a model that represents the pull request hook.
 *
 * @package BitbucketEventNotification\Model
 */
class PullRequestDeclined extends PullRequest
{
    /**
     * {@inheritDoc}
     */
    public function toNotifyString()
    {
        $notify = '';
        $notify .= sprintf("[info]");
        $notify .= sprintf("[title]Pull request has been declined by %s:([/title]", $this->data['author']['display_name']);
        $notify .= sprintf("[%s] %s", $this->data['state'], $this->data['title']);
        $notify .= sprintf("[/info]");
        return $notify;
    }
}