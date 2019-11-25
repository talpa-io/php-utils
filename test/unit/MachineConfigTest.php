<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 25.11.19
 * Time: 13:02
 */

use Talpa\Utils\Config\MachineConfig;
use PHPUnit\Framework\TestCase;
use Talpa\Utils\Params\TalpaTmidParams;

class MachineConfigTest extends TestCase
{

    public function testGetConfig()
    {
        $machineConfig = new MachineConfig("http://localhost/fail",
                                           __DIR__ . "/../mock/config.yml",
                                           "ulan",
                                           new TalpaTmidParams("M-tmock"));
        $config = $machineConfig->getConfig();
        $configFile = phore_file(__DIR__ . "/../mock/config.yml")->get_yaml();
        $expected =  phore_pluck(["ulan", "machines", "M-tmock"], $configFile);

        $this->assertEquals($expected, $config);
    }

    public function testGetConfigUrl()
    {
        $machineConfig = new MachineConfig("http://localhost/test/M-tmock1",
                                           __DIR__ . "/../mock/config.yml",
                                           "ulan",
                                           new TalpaTmidParams("M-tmock1"));
        $config = $machineConfig->getConfig();
        $configFile = phore_file(__DIR__ . "/../mock/configUrl.yml")->get_yaml();
        $expected =  phore_pluck(["ulan", "machines", "M-tmock1"], $configFile);

        $this->assertEquals($expected, $config);
    }

    public function testGetConfigUrlException()
    {
        $machineConfig = new MachineConfig("http://localhost/test/M-tmockException", __DIR__ . "/../mock/config.yml", "ulan", new TalpaTmidParams("M-tmock1"));
        $this->expectException(\Phore\HttpClient\Ex\PhoreHttpRequestException::class);
        $this->expectExceptionMessage("Machine M-tmockException is not defined in config");
        $machineConfig->getConfig();
    }

}
