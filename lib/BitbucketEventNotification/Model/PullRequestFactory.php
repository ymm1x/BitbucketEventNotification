<?php
namespace BitbucketEventNotification\Model;

/**
 * This class is factory class of PullRequest model.
 *
 * @package BitbucketEventNotification\Model
 */
class PullRequestFactory
{
    /**
     * Return the instance corresponding to the data.
     *
     * @param array $jsonData
     * @return PullRequest|null
     */
    public static function create($jsonData)
    {
        if (!is_array($jsonData) || empty($jsonData)) {
            return null;
        }

        $postType = key($jsonData);
        $jsonData = reset($jsonData);

        $instance = null;
        switch ($postType) {
            case 'pullrequest_comment_created':
                $instance = new PullRequestCommentCreated();
                break;
            case 'pullrequest_created':
                $instance = new PullRequestCreated();
                break;
            case 'pullrequest_declined':
                $instance = new PullRequestDeclined();
                break;
            case 'pullrequest_merged':
                $instance = new PullRequestMerged();
                break;
            default:
                $instance = null;
                break;
        }

        if ($instance instanceof PullRequest) {
            // inject json data
            $instance->setData($jsonData);
        }

        return $instance;
    }
}