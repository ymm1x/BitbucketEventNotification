<?php
namespace BitbucketEventNotification\Config;

/**
 * This class is config value loader
 *
 * @package BitbucketEventNotification\Config
 */
class ConfigLoader
{
    public $configDir = null;

    /**
     * Constructor.
     *
     * @param $configDir config dir
     */
    public function __construct($configDir)
    {
        $this->configDir = $configDir;
    }

    /**
     * Load config value of config json file.
     *
     * @param $configName config json file name
     * @param $key key
     * @return mixed returns config value. false is returned if the failed for load.
     */
    public function load($configName, $key)
    {
        $configJson = file_get_contents($this->configDir . '/' . $configName . '.json');
        if (!$configJson) {
            return false;
        }
        $config = json_decode($configJson, true);
        if (!array_key_exists($key, $config)) {
            return false;
        }
        return $config[$key];
    }
}