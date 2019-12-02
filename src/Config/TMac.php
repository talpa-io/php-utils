<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 25.11.19
 * Time: 12:29
 */

namespace Talpa\Utils\Config;

use Phore\Cache\Cache;
use Phore\Cache\CacheItemPool;
use Talpa\Utils\Params\TalpaTmidParams;

class TMac
{
    private $tmacHost;
    private $localConfigPath;

    /**
     * @var CacheItemPool
     */
    private $cacheItemPool = null;

    public function __construct(string $tmacHost, string $localConfigPath=null)
    {
        $this->tmacHost = $tmacHost;
        $this->localConfigPath = $localConfigPath;
        $this->cacheItemPool = new CacheItemPool("null://null");
    }

    public function setCache(CacheItemPool $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->cacheItemPool->setDefaultExpiresAfter(3600);
        $this->cacheItemPool->setDefaultRetryAfter(15);
    }


    /**
     * Get list of all available assets
     *
     * @params string $service
     * @return array
     * @throws
     */
    public function listAssets(string $service = null) : array
    {
        if($service === null) {
            return $this->cacheItemPool->getItem("list_assets")->load(function () {
                return phore_http_request($this->tmacHost . "/v1/assets")->send()->getBodyJson()["assets"];
            });
        }

        return $this->cacheItemPool->getItem("list_assets_$service")->load(function () use ($service) {
            return phore_http_request($this->tmacHost . "/v1/assets?service={service}", ["service" => $service])->send()->getBodyJson()["assets"];
        });
    }


    /**
     * Return errors parsing config files in tmac
     *
     * @return array
     * @throws
     */
    public function listErrors() : array
    {
        return $this->cacheItemPool->getItem("list_errors")->load(function () {
            return phore_http_request($this->tmacHost . "/v1/assets")->send()->getBodyJson()["errors"];
        });
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
        if ($this->localConfigPath !== null) {
            if (phore_file($this->localConfigPath . "/$tmid.yml")->exists()) {
                $config = phore_file($this->localConfigPath . "/$tmid.yml")->get_yaml();
                return ["meta" => $config["meta"]] + $config[$serviceId];
            }
        }
        if($serviceId === null){
            return $this->cacheItemPool->getItem("assets_$tmid")->load(function () use ($tmid) {
                return phore_http_request($this->tmacHost . "/v1/assets/$tmid")->send()->getBodyJson();
            });

        }
        return $this->cacheItemPool->getItem("assets_{$tmid}_{$serviceId}")->load(function () use ($tmid, $serviceId) {
            return phore_http_request($this->tmacHost . "/v1/assets/$tmid/$serviceId")->send()->getBodyJson();
        });

    }
}
