<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 25.11.19
 * Time: 13:02
 */

use Phore\HttpClient\Ex\PhoreHttpRequestException;
use Talpa\Utils\Config\Tmac;
use PHPUnit\Framework\TestCase;
use Talpa\Utils\Params\TalpaTmidParams;

class MachineConfigTest extends TestCase
{
    public function testConstructFromUriFailScheme()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid tmac URI");
        $machineConfig = new Tmac("fail?service");
    }

    public function testConstructFromUriFailService()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Param 'service' not defined in tmac URI");
        $machineConfig = new Tmac("file://test");
        $this->assertEquals("M-tmock1", $machineConfig->listAssets()[0]['tmid']);
    }

    public function testListAssetsFromNonExistingService()
    {
        $this->expectException(PhoreHttpRequestException::class);
        $machineConfig = new Tmac("http://localhost?service=fail");
        $this->assertEquals("M-tmock1", $machineConfig->listAssets()[0]['tmid']);
    }

    public function testListAssetsFromExistingService()
    {
        $machineConfig = new Tmac("http://localhost?service=Test");
        $this->assertEquals("M-tmock1", $machineConfig->listAssets()[0]['tmid']);
        $this->assertEquals("bar", $machineConfig->listAssets()[0]['foo']);
    }

    public function testGetConfigLocal()
    {
        $machineConfig = new Tmac("file:///test?service=Test");
        $config = $machineConfig->getConfig("M-tmock", "ulan");
        $configFile = phore_file(__DIR__ . "/../mock/config.yml")->get_yaml();
        $expected =  phore_pluck(["ulan", "machines", "M-tmock"], $configFile);

        $this->assertEquals($expected, $config);
    }

    public function testGetConfigUrl()
    {
        $machineConfig = new Tmac("http://localhost?service=Test");
        $config = $machineConfig->getConfig("M-tmock1", "ulan");
        $configFile = phore_file(__DIR__ . "/../mock/configUrl.yml")->get_yaml();
        $expected =  phore_pluck(["ulan", "machines", "M-tmock1"], $configFile);

        $this->assertEquals($expected, $config);
    }

}
