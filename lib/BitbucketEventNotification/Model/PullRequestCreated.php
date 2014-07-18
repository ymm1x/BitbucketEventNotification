<?php
namespace BitbucketEventNotification\Model;

/**
 * This class is a model that represents the pull request hook.
 *
 * @package BitbucketEventNotification\Model
 */
class PullRequestCreated extends PullRequest
{
    /**
     * {@inheritDoc}
     */
    public function toNotifyString()
    {
        $notify = '';
        $notify .= sprintf("[info]");
        $notify .= sprintf("[title]Pull request has been created by %s. Please review(bow)[/title]", $this->data['author']['display_name']);
        $notify .= sprintf("[%s] #%d %s", $this->data['state'], $this->data['id'], $this->data['title']);
        if (strlen($this->data['description']) > 0) {
            $notify .= sprintf("\n%s", $this->data['description']);
        }
        $notify .= sprintf("\nhttps://bitbucket.org/%s/pull-request/%d", $this->data['destination']['repository']['full_name'], $this->data['id']);
        $notify .= sprintf("[/info]");
        return $notify;
    }
}