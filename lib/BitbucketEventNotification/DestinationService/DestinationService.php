<?php
namespace BitbucketEventNotification\DestinationService;

use BitbucketEventNotification\PullRequest\PullRequest;

/**
 * This class is a class that represents the type of external services.
 *
 * @package BitbucketEventNotification\Chatwork
 */
abstract class DestinationService
{
    /**
     * @var string ex:Slack
     */
    protected $serviceName = null;

    /**
     * @var PullRequest $pullRequest
     */
    protected $pullRequest = null;

    /**
     * @param string $serviceName
     * @param PullRequest $pullRequest
     */
    public function __construct($serviceName, PullRequest $pullRequest)
    {
        $this->serviceName = $serviceName;
        $this->pullRequest = $pullRequest;
    }

    /**
     * @return BaseAPIClient get api client of this service.
     */
    abstract function getApiClient();

    /**
     * @param array $extendedParams optional
     * @return mixed returns the response array if api successful.
     *               otherwise, returns null.
     */
    abstract function postMessage($extendedParams = array());

    /**
     * Return the destination service instance by service name.
     *
     * @param string $serviceName
     * @param PullRequest $pullRequest
     * @return DestinationService
     */
    public static function create($serviceName, $pullRequest)
    {
        $serviceName = strtolower(trim($serviceName));

        $instance = null;
        switch ($serviceName) {
            case 'chatwork':
                $instance = new ChatworkService($pullRequest);
                break;
            case 'slack':
                $instance = new SlackService($pullRequest);
                break;
            default:
                break;
        }

        return $instance;
    }
}