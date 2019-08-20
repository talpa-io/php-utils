<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 20.08.19
 * Time: 13:58
 */

use Talpa\Utils\Params\TalpaTmidParams;
use PHPUnit\Framework\TestCase;


class TalpaTmidParamsTest extends TestCase
{
    public function testConstruct()
    {
        //Arrange
        $talpaTmidParams = new TalpaTmidParams("M-tmock");
        //Assert
        $this->assertEquals($talpaTmidParams->tmid, "M-tmock");
    }
}
