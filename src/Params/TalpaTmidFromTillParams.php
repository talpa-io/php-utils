<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 20.08.19
 * Time: 13:24
 */

namespace Talpa\Utils\Params;


use Phore\Core\Format\PhoreInput;

class TalpaTmidFromTillParams extends TalpaTmidParams
{

    public $from;
    public $till;
    /**
     * TalpaTmidFromTillParams constructor.
     * @param $tmid
     * @param $from
     * @param $till
     */
    public function __construct($tmid, $from, $till)
    {
        parent::__construct($tmid);
        $phoreInput = new PhoreInput();
        $currentTime = time();

        if($till === null){
            $till = $currentTime;
        }
        $this->till = $phoreInput->toTimestampUtc($till);

        if($from === null){
            if($till === $currentTime){
                $from = $currentTime - 31536000;
            }else{
                $from = $till - 3600;
            }
        }
        $this->from = $phoreInput->toTimestampUtc($from);

        if($this->from > $this->till){
            throw new \InvalidArgumentException("from: $from can´t be greater than till: $till");
        }
        if($this->from > $currentTime){
            throw new \InvalidArgumentException("from: $from can´t be in the future");
        }
    }

}
