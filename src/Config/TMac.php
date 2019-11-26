<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 25.11.19
 * Time: 12:29
 */

namespace Talpa\Utils\Config;

use Phore\Cache\Cache;
use Talpa\Utils\Params\TalpaTmidParams;

class TMac
{
    private $tmacUrl;
    private $pathToConfigFile;

    public function __construct(string $tmacHost, string $localConfigPath)
    {
        $this->tmacUrl = $tmacHost;
        $this->pathToConfigFile = $localConfigPath;
    }

    /**
     * Get list of all available assets
     *
     * @return array
     */
    public function listAssets() : array
    {



    }


    /**
     * Return errors parsing config files in tmac
     *
     * @return array
     */
    public function listErrors() : array
    {

    }


    /**
     * Load the configuration of single asset
     *
     * @param string $tmid
     * @param string|null $serviceId
     * @return array
     */
    public function getConfig(string $tmid, string $serviceId=null) : array
    {
        if(phore_file($this->pathToConfigFile)->exists()){
            $config = phore_file($this->pathToConfigFile)->get_yaml();
            try{
                return phore_pluck(
                    [$this->requestedService, "machines", $this->tmidParams->tmid],
                    $config,
                    new \InvalidArgumentException("Machine " . $this->tmidParams->tmid . " is not defined in config")
                );
            } catch (\InvalidArgumentException $exception){

            }
        }
        return phore_http_request($this->tmacUrl)->send()->getBodyJson();
    }
}
