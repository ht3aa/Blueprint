<?php

namespace Hasanweb\Blueprint\Migrations;

use Hasanweb\Blueprint\Helpers\Format;

class Migration
{
    private static function template($tableName, $columns)
    {
        $fileTemplate = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
    * Run the migrations.
    */
    public function up(): void
    {
        Schema::create(\"$tableName\", function (Blueprint \$table) {
            \$table->id();
$columns
            \$table->timestamps();
        });
    }

    /**
    * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists(\"$tableName\");
    }
};";

        return $fileTemplate;
    }

    private static function attributesStr($attributes)
    {
        if (empty($attributes)) {
            return '';
        }

        $str = '';
        $formattedValue = '';
        foreach ($attributes as $attribute => $value) {
            if (is_string($value) && ! empty($value)) {
                $formattedValue = "'$value'";
            } elseif (is_bool($value)) {
                $formattedValue = $value ? 'true' : 'false';

            }

            $str .= "->$attribute($formattedValue)";
        }

        return $str;

    }

    private static function columns($fields)
    {
        $columns = '';

        foreach ($fields as $fieldName => $fieldOptions) {
            $attributesStr = self::attributesStr($fieldOptions['attributes'] ?? '');
            $fieldType = $fieldOptions['type'];

            $columns .= Format::addTabs(3)."\$table->$fieldType('$fieldName')$attributesStr;\n";

        }

        return $columns;

    }

    public static function make($data, $tableOutput)
    {
        $incrementBy = 1;
        $randomNumber = rand(100000, 999999);
        $tableRows = [];

        foreach ($data as $tableName => $fields) {
            $columns = self::columns($fields);
            $migration = self::template($tableName, $columns);
            $migrationFileName = date('Y_m_d').'_'.$randomNumber + $incrementBy.'_create_'.$tableName.'_table.php';
            $migrationFilePath = database_path('migrations/'.$migrationFileName);

            array_push($tableRows, [$migrationFileName]);

            file_put_contents($migrationFilePath, $migration);
            $incrementBy += 1;

        }
        // Set the table headers.
        $tableOutput->setHeaders([
            'Generated Migration Files',
        ]);
        $tableOutput->setRows($tableRows);
        $tableOutput->render();

    }
}
