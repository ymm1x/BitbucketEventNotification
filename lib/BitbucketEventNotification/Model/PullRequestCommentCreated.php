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
        $notify .= sprintf("Comment was posted by %s(*)", $this->data['user']['display_name']);
        $notify .= sprintf("\n%s", $this->data['content']['raw']);
        $notify .= sprintf("\n%s", $this->replaceUrlForLink($this->data['links']['html']['href']));
        $notify .= sprintf("[/info]");
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