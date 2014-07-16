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
        $notify .= sprintf("Pull request has been created by %s (bow)", $this->data['author']['display_name']);
        $notify .= sprintf("\n[%s] #%d %s", $this->data['state'], $this->data['id'], $this->data['title']);
        if (strlen($this->data['description']) > 0) {
            $notify .= sprintf("\n%s", $this->data['description']);
        }
        $notify .= sprintf("\nhttps://bitbucket.org/GMO-AS_CEO/linestreet-server/pull-request/%d", $this->data['id']);
        $notify .= sprintf("[/info]");
        return $notify;
    }
}