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
    private $tmacHost;
    private $localConfigPath;

    public function __construct(string $tmacHost, string $localConfigPath)
    {
        $this->tmacHost = $tmacHost;
        $this->localConfigPath = $localConfigPath;
    }

    /**
     * Get list of all available assets
     *
     * @return array
     * @throws
     */
    public function listAssets() : array
    {
        return phore_http_request($this->tmacHost . "/v1/assets")->send()->getBodyJson()["assets"];
    }


    /**
     * Return errors parsing config files in tmac
     *
     * @return array
     * @throws
     */
    public function listErrors() : array
    {
        return phore_http_request($this->tmacHost . "/v1/assets")->send()->getBodyJson()["errors"];
    }


    /**
     * Load the configuration of single asset
     *
     * @param string $tmid
     * @param string|null $serviceId
     * @return array
     * @throws
     */
    public function getConfig(string $tmid, string $serviceId=null) : array
    {
        if(phore_file($this->localConfigPath)->exists()){
            $config = phore_file($this->localConfigPath)->get_yaml();
            try{
                return phore_pluck(
                    [$serviceId, "machines", $tmid],
                    $config,
                    new \InvalidArgumentException("Machine " . $tmid . " is not defined in config")
                );
            } catch (\InvalidArgumentException $exception){

            }
        }
        return phore_http_request($this->tmacHost . "/v1/assets/$tmid/$serviceId")->send()->getBodyJson();
    }
}