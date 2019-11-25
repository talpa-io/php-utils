<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 25.11.19
 * Time: 12:29
 */

namespace Talpa\Utils\Config;

use Talpa\Utils\Params\TalpaTmidParams;

class MachineConfig
{
    private $tmacUrl;
    private $pathToConfigFile;
    private $requestedService;
    private $tmidParams;

    public function __construct(string $tmacUrl, string $pathToConfigFile, string $requestedService, TalpaTmidParams $tmidParams)
    {
        $this->tmacUrl = $tmacUrl;
        $this->pathToConfigFile = $pathToConfigFile;
        $this->requestedService =  $requestedService;
        $this->tmidParams = $tmidParams;
    }

    public function getConfig()
    {
        if(phore_file($this->pathToConfigFile)->exists()){
            $config = phore_file($this->pathToConfigFile)->get_yaml();
            try{
                return phore_pluck(
                    [$this->tmidParams->tmid, "machines", $this->tmidParams->tmid],
                    $config,
                    new \InvalidArgumentException("Machine " . $this->tmidParams->tmid . " is not defined in config")
                );
            } catch (\InvalidArgumentException $exception){

            }
        }
        return phore_http_request($this->tmacUrl)->send()->getBodyJson();
    }
}
