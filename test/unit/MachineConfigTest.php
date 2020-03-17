<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 25.11.19
 * Time: 13:02
 */

use Phore\FileSystem\Exception\FileNotFoundException;
use Phore\HttpClient\Ex\PhoreHttpRequestException;
use Talpa\Utils\Config\Tmac;
use PHPUnit\Framework\TestCase;

class MachineConfigTest extends TestCase
{
    public function testExceptionConstructFromUriFailScheme()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid tmac URI");
        $machineConfig = new Tmac("fail?clientId");
    }

    public function testExceptionConstructFromUriFailNoClient()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Param 'clientId' not defined in tmac URI");
        $machineConfig = new Tmac("file://test");
        $this->assertEquals("M-tmock1", $machineConfig->listAssets()[0]['tmid']);
    }

    public function testExceptionListAssetsFromUrlNonExistingClient()
    {
        $this->expectException(PhoreHttpRequestException::class);
        $machineConfig = new Tmac("http://localhost?clientId=fail");
        $this->assertEquals("M-tmock", $machineConfig->listAssets()[0]['tmid']);
    }

    public function testListAssetsFromUrlExistingClient()
    {
        $machineConfig = new Tmac("http://localhost?clientId=Test");
        $assets = $machineConfig->listAssets();
        $this->assertEquals("M-tmock", $assets[0]['tmid']);
        $this->assertEquals("bar", $assets[0]['foo']);
    }

    public function testExceptionListAssetsFromLocalNonExistingClient()
    {
        $this->expectException(PhoreHttpRequestException::class);
        $machineConfig = new Tmac("file:///opt/test/mock?clientId=fail");
        $machineConfig->listAssets();
    }

    public function testListAssetsFromLocalExistingClient()
    {
        $machineConfig = new Tmac("file:///opt/test/mock?clientId=Test");
        $assets = $machineConfig->listAssets();
        $this->assertEquals("M-tmock", $assets[0]['tmid']);
        $this->assertEquals("bar", $assets[0]['foo']);
    }

    public function testGetConfigLocalClient()
    {
        $machineConfig = new Tmac("file:///opt/test/mock?clientId=Test");
        $config = $machineConfig->getConfig("M-tmock");
        $this->assertArrayHasKey('meta', $config);
        $this->assertArrayNotHasKey('client1', $config);
        $this->assertEquals('bar', $config['foo']);
    }

    public function testGetConfigLocalAllClients()
    {
        $machineConfig = new Tmac("file:///opt/test/mock?clientId=Test");
        $config = $machineConfig->getConfig("M-tmock", "all");
        $this->assertArrayHasKey('meta', $config);
        $this->assertArrayHasKey('Test', $config);
        $this->assertArrayHasKey('client1', $config);
    }

    public function testGetConfigUrlClient()
    {
        $machineConfig = new Tmac("http://localhost?clientId=Test");
        $config = $machineConfig->getConfig("M-tmock");
        $this->assertArrayHasKey('meta', $config);
        $this->assertArrayNotHasKey('client1', $config);
        $this->assertEquals('bar', $config['foo']);
    }

    public function testGetConfigUrlAllClients()
    {
        $machineConfig = new Tmac("http://localhost?clientId=Test");
        $config = $machineConfig->getConfig("M-tmock","all");
        $this->assertArrayHasKey('meta', $config);
        $this->assertArrayHasKey('Test', $config);
        $this->assertArrayHasKey('client1', $config);
    }

    public function testExceptionGetConfigLocalFailClient()
    {
        $machineConfig = new Tmac("file:///opt/test/mock?clientId=fail");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Machine 'M-tmock' has no config for 'fail'.");
        $machineConfig->getConfig("M-tmock");
    }

    public function testExceptionGetConfigLocalFailMachine()
    {
        $machineConfig = new Tmac("file:///opt/test/mock?clientId=Test");
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage("File '/opt/test/mock/fail.yml' not found.");
        $machineConfig->getConfig("fail");
    }

    public function testExceptionGetConfigUrlFailClient()
    {
        $machineConfig = new Tmac("http://localhost?clientId=fail");
        $this->expectException(PhoreHttpRequestException::class);
        $this->expectExceptionMessage("Machine 'M-tmock' has no config for 'fail'.");
        $machineConfig->getConfig("M-tmock");
    }

    public function testExceptionGetConfigUrlFailMachine()
    {
        $machineConfig = new Tmac("http://localhost?clientId=Test");
        $this->expectException(PhoreHttpRequestException::class);
        $this->expectExceptionMessage("File '\/opt\/test\/mock\/fail.yml' not found.");
        $machineConfig->getConfig("fail");
    }

    public function testGetConfigLocalClientNoMeta()
    {
        $machineConfig = new Tmac("file:///opt/test/mock/faultyConfigs?clientId=Test");
        $config = $machineConfig->getConfig("M-nometa");
        $this->assertEquals([], $config['meta']);
        $this->assertEquals('bar', $config['foo']);
    }
}
