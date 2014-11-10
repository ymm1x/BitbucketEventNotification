<?php
namespace BitbucketEventNotification\PullRequest;

/**
 * This class is a model that represents the pull request update hook.
 *
 * @package BitbucketEventNotification\PullRequest
 */
class PullRequestUpdated extends PullRequest
{
    /**
     * {@inheritDoc}
     */
    public function toNotifyString()
    {
        $notify = '';
        $notify .= sprintf("[info]");
        $notify .= sprintf("[title]Pull request has been updated by %s. Please re-review:p[/title]", $this->data['author']['display_name']);
        $notify .= sprintf("[UPDATED] %s", $this->data['title']);
        $notify .= sprintf("[/info]");
        return $notify;
    }
}