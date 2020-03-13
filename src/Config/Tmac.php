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
use Phore\Core\Helper\PhoreUrl;
use Phore\FileSystem\PhoreUri;
use Phore\HttpClient\Ex\PhoreHttpRequestException;
use Talpa\Utils\Params\TalpaTmidParams;

class Tmac
{
    private $tmacHost;
    private $clientId;
    private $localConfigPath;

    /**
     * @var CacheItemPool
     */
    private $cacheItemPool = null;

    /**
     * Tmac constructor.
     * @param string $tmacHost
     * @param string|null $localConfigPath
     * @param string $uri
     * @throws \Exception
     */
    public function __construct(string $uri)
    {
        $uriParts = phore_parse_url($uri);
        $this->clientId = $uriParts->getQueryVal('clientId', new \InvalidArgumentException("Param 'clientId' not defined in tmac URI '$uri'"));
        switch ($uriParts->scheme) {
            case "file":
                $this->localConfigPath = $uriParts->path;
                break;
            case "http":
            case "https":
                $this->tmacHost = substr($uri, 0, strpos($uri, "?"));
                $this->localConfigPath = null;
                break;
            default:
               throw new \InvalidArgumentException("Invalid tmac URI '$uri'");
        }
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
     * @params string $clientId
     * @return array
     * @throws
     */
    public function listAssets(string $clientId = null) : array
    {
        if($clientId === "meta") {
            return $this->cacheItemPool->getItem("list_assets")->load(function () {
                return phore_http_request($this->tmacHost . "/v1/assets")->send()->getBodyJson()["assets"];
            });
        }
        $clientId = $this->clientId;

        return $this->cacheItemPool->getItem("list_assets_$clientId")->load(function () use ($clientId) {
            return phore_http_request($this->tmacHost . "/v1/assets?clientId={clientId}", ["clientId" => $clientId])->send()->getBodyJson()["assets"];
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
    public function getConfig(string $tmid, string $clientId=null) : array
    {
        //take clientId from URI if not explicitly specified
        if($clientId === null){
            $clientId=$this->clientId;
        }
        //Is a local config path set? then take that (URI = file://...)
        if ($this->localConfigPath !== null) {
            try {
                $configFile = phore_file($this->localConfigPath . "/$tmid.yml")->get_yaml();
            } catch (\Exception $ex) {
                throw $ex;
            }
            //config for all clients? else => config for $this->clientID+Meta
            if($clientId === "all") {
               return $configFile;
            }
            $meta = phore_pluck('meta', $configFile, []);
            $clientConfig = phore_pluck($clientId, $configFile, new \InvalidArgumentException("Machine '$tmid' has no config for '$clientId'."));
            return ["meta" => $meta] + $clientConfig;
        }
        //URI is http
        if($clientId === "all"){
            return $this->cacheItemPool->getItem("assets_$tmid")->load(function () use ($tmid) {
                return phore_http_request($this->tmacHost . "/v1/assets/$tmid")->send()->getBodyJson();
            });
        }
        return $this->cacheItemPool->getItem("assets_{$tmid}_{$clientId}")->load(function () use ($tmid, $clientId) {
            return phore_http_request($this->tmacHost . "/v1/assets/$tmid/$clientId")->send()->getBodyJson();
        });

    }
}
