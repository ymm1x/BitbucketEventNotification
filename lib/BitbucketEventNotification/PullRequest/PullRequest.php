<?php
namespace BitbucketEventNotification\PullRequest;

/**
 * This class is a base model that represents the pull request hook.
 *
 * @package BitbucketEventNotification\PullRequest
 */
abstract class PullRequest
{
    /**
     * @var array data
     */
    protected $data = null;

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
            case 'pullrequest_updated':
                $instance = new PullRequestUpdated();
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
            /**
             * @var PullRequest $instance
             */
            // inject data
            $instance->setData($jsonData);
        }

        return $instance;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Replace endpoint url to url link.
     *
     * @param string $url
     * @return string
     */
    public static function replaceUrlForLink($url)
    {
        return str_replace('://api.', '://', $url);
    }
}