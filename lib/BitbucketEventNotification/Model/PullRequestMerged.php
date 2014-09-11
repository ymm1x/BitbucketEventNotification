<?php
namespace BitbucketEventNotification\Model;

/**
 * This class is factory class of PullRequest model.
 *
 * @package BitbucketEventNotification\Model
 */
class PullRequestMerged extends PullRequest
{
    /**
     * {@inheritDoc}
     */
    public function toNotifyString()
    {
        $notify = '';
        $notify .= sprintf("[info]");
        $notify .= sprintf("[title]Pull request has been merged by %s. Good job8-)[/title]", $this->data['author']['display_name']);
        $notify .= sprintf("[MERGED] %s", $this->data['title']);
        $notify .= sprintf("[/info]");
        return $notify;
    }
}