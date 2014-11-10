<?php
namespace BitbucketEventNotification\PullRequest;

/**
 * This class is a model that represents the pull request hook.
 *
 * @package BitbucketEventNotification\PullRequest
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
        $notify .= sprintf("[DECLINED] %s", $this->data['title']);
        $notify .= sprintf("[/info]");
        return $notify;
    }
}