<?php

namespace Hasanweb\Blueprint\Helpers;

use Illuminate\Support\Str;

class Naming
{
    public static function getModelNameFromTableName($tableName)
    {
        return ucfirst(Str::singular($tableName));
    }
}
