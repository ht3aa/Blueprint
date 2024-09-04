<?php

namespace Hasanweb\Blueprint\Models;

class Model
{
    private static function template($modelName, $fields)
    {
        $fileTemplate = "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class $modelName extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected \$fillable = [
      $fields
   ];


}
    ";

        return $fileTemplate;
    }

    private static function isNullable($isNullable)
    {

        return $isNullable ? '->nullable()' : '';
    }

    private static function fillable($fields)
    {
        $fillable = '';

        foreach ($fields as $fieldName => $values) {
            // Wrap each value in double quotes and join them with a comma
            $quotedValues = array_map(function ($value) {
                return '"'.$value.'"';
            }, $values);

            // Join the quoted values with a comma
            $fillable .= implode(', ', $quotedValues);
        }

        return $fillable;

    }

    public static function make($data)
    {

        foreach ($data['models'] as $modelName => $fields) {
            $fileContent = self::template($modelName, self::fillable($fields));
            $filePath = app_path("Models/$modelName.php");
            file_put_contents($filePath, $fileContent);
        }
    }
}
