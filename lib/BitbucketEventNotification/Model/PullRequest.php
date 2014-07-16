<?php
namespace BitbucketEventNotification\Model;

/**
 * This class is a base model that represents the pull request hook.
 *
 * @package BitbucketEventNotification\Model
 */
abstract class PullRequest
{
    protected $data = null;

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get notify string for chatwork post.
     *
     * @return string
     */
    abstract public function toNotifyString();
}