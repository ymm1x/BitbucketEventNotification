<?php
namespace BitbucketEventNotification\Network;

/**
 * This class is utility associated with the access source.
 *
 * @package BitbucketEventNotification\Network
 */
class AccessSource
{
    private $remoteAddr = null;

    private $permitRemoteAddrs = array('131.103.20.165', '131.103.20.166');

    /**
     * Constructor.
     *
     * @param string $remoteAddr
     */
    public function __construct($remoteAddr)
    {
        $this->remoteAddr = $remoteAddr;
    }

    /**
     * Check forbidden access.
     *
     * @return bool return true when forbidden access.
     */
    public function isForbidden()
    {
        return !in_array($this->remoteAddr, $this->permitRemoteAddrs);
    }
}