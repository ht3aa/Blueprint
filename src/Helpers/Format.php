<?php

namespace Hasanweb\Blueprint\Helpers;

class Format
{
    public static function addTabs($amount)
    {
        return str_repeat("\t", $amount);
    }
}
