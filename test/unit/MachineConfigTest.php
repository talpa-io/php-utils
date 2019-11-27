<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 25.11.19
 * Time: 13:02
 */

use Talpa\Utils\Config\TMac;
use PHPUnit\Framework\TestCase;
use Talpa\Utils\Params\TalpaTmidParams;

class MachineConfigTest extends TestCase
{

    public function testGetConfig()
    {
        $machineConfig = new TMac("localhost", __DIR__ . "/../mock/config.yml");
        $config = $machineConfig->getConfig("M-tmock", "ulan");
        $configFile = phore_file(__DIR__ . "/../mock/config.yml")->get_yaml();
        $expected =  phore_pluck(["ulan", "machines", "M-tmock"], $configFile);

        $this->assertEquals($expected, $config);
    }

    public function testGetConfigUrl()
    {
        $machineConfig = new TMac("localhost", __DIR__ . "/../mock/config.yml");
        $config = $machineConfig->getConfig("M-tmock1", "ulan");
        $configFile = phore_file(__DIR__ . "/../mock/configUrl.yml")->get_yaml();
        $expected =  phore_pluck(["ulan", "machines", "M-tmock1"], $configFile);

        $this->assertEquals($expected, $config);
    }

    public function testGetConfigUrlException()
    {
        $machineConfig = new TMac("localhost", __DIR__ . "/../mock/config.yml");
        $this->expectException(\Phore\HttpClient\Ex\PhoreHttpRequestException::class);
        $this->expectExceptionMessage("Machine M-tmockException is not defined in config");
        $machineConfig->getConfig("M-tmock-Exception");
    }

}
