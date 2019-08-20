<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 12:55
 */

namespace Talpa\Utils\Params;


class TalpaTmidParams
{
    public $tmid;
    /**
     * TalpaTmidParams constructor.
     */
    public function __construct($tmid)
    {
        $this->tmid = $tmid;
    }
}
