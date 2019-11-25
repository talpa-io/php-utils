<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 20.08.19
 * Time: 13:59
 */

use Talpa\Utils\Params\TalpaTmidFromTillParams;
use PHPUnit\Framework\TestCase;


class TalpaTmidFromTillParamsTest extends TestCase
{

    public function testConstruct()
    {
        //Arrange
        $talpaTmidFromTillParams = new TalpaTmidFromTillParams("M-tmock",15, 16);
        //Assert
        $this->assertEquals(15, $talpaTmidFromTillParams->from);
        $this->assertEquals(16, $talpaTmidFromTillParams->till);
    }
    public function test__constructOnlyFrom()
    {
        //Arrange
        $currentTime = time();
        $talpaTmidFromTillParams = new TalpaTmidFromTillParams("M-tmock",15, null);
        //Assert
        $this->assertEquals(15, $talpaTmidFromTillParams->from);
        $this->assertEqualsWithDelta($currentTime, $talpaTmidFromTillParams->till,1);
    }
    public function testConstructOnlyFromAsDateFormat()
    {
        //Arrange
        $currentTime = time();
        $talpaTmidFromTillParams = new TalpaTmidFromTillParams("M-tmock","2019-08-19T00:00:00", null);
        //Assert
        $this->assertEquals(1566172800, $talpaTmidFromTillParams->from);
        $this->assertEqualsWithDelta($currentTime, $talpaTmidFromTillParams->till,1);
    }
    public function testConstructOnlyTill()
    {
        //Arrange
        $talpaTmidFromTillParams = new TalpaTmidFromTillParams("M-tmock",null, 3616);
        //Assert
        $this->assertEquals(16, $talpaTmidFromTillParams->from);
        $this->assertEquals(3616, $talpaTmidFromTillParams->till);
    }
    public function testConstructOnlyTillAsDateFormat()
    {
        //Arrange
        $talpaTmidFromTillParams = new TalpaTmidFromTillParams("M-tmock",null, "2019-08-19T00:00:00");
        //Assert
        $this->assertEquals(1566172800-3600, $talpaTmidFromTillParams->from);
        $this->assertEquals(1566172800, $talpaTmidFromTillParams->till);
    }
    public function testConstructFromAndTillNotSet()
    {
        //Arrange
        $currentTime = time();
        $talpaTmidFromTillParams = new TalpaTmidFromTillParams("M-tmock",null, null);
        //Assert
        $this->assertEquals( $talpaTmidFromTillParams->till-31536000,$talpaTmidFromTillParams->from);
        $this->assertEqualsWithDelta($currentTime, $talpaTmidFromTillParams->till,1);
    }

    public function testConstructExceptionFromGreaterTill()
    {
        //Arrange
        $from = 17;
        $till = 16;

        //Assert
        $this->expectExceptionMessage("from: $from can´t be greater than till: $till");
        $this->expectException(InvalidArgumentException::class);

        //Act
        new TalpaTmidFromTillParams("M-tmock",$from, $till);
    }

}
