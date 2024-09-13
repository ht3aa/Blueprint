<?php

namespace Hasanweb\Blueprint\Models;

class Model
{
    private static function template($modelName, $fields, $relations, $relationsTypeNamespaces)
    {
        $fileTemplate = "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
$relationsTypeNamespaces

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

    $relations
}
    ";

        return $fileTemplate;
    }

    private static function relationTemplate($relationType, $relationName)
    {
        $relationTypeLower = lcfirst($relationType);
        $relationNameLower = lcfirst($relationName);
        $relationTemplate = "
    public function $relationNameLower(): $relationType
    {
        return \$this->$relationTypeLower($relationName::class);
    }

";

        return $relationTemplate;

    }

    private static function fillable($fields)
    {
        $fillable = '';

        $quotedValues = array_map(function ($value) {
            return '"'.$value.'"';
        }, $fields);

        // Join the quoted values with a comma
        $fillable .= implode(', ', $quotedValues);

        return $fillable;
    }

    private static function relations($relations)
    {

        $relationsTemplate = '';
        $relationTypeNamespaces = '';

        foreach ($relations as $relationName => $relationValues) {

            $relationTypeNamespaces .= "use Illuminate\Database\Eloquent\Relations\\$relationName;\n";

            foreach ($relationValues as $relationValue) {
                $relationsTemplate .= self::relationTemplate($relationName, $relationValue)."\n\n";
            }
        }

        return ['template' => $relationsTemplate, 'namespaces' => $relationTypeNamespaces];
    }

    public static function make($data, $tableOutput)
    {

        $tableRows = [];
        foreach ($data as $modelName => $fields) {
            $fillables = self::fillable($fields['fillable']);
            $relations = self::relations($fields['relations'] ?? []);

            $fileContent = self::template($modelName, $fillables, $relations['template'], $relations['namespaces']);
            $fileName = $modelName.'.php';
            $filePath = app_path("Models/$fileName");

            array_push($tableRows, [$fileName]);

            file_put_contents($filePath, $fileContent);
        }

        // Set the table headers.
        $tableOutput->setHeaders([
            'Generated Models Files',
        ]);
        $tableOutput->setRows($tableRows);
        $tableOutput->render();
    }
}
