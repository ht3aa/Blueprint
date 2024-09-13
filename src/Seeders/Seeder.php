<?php

namespace Hasanweb\Blueprint\Seeders;

use Faker\Factory as FakerFactory;
use Hasanweb\Blueprint\Helpers\Naming;

class Seeder
{
    private static function template($records)
    {
        $fileTemplate = "

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $records
    }
}
";

        return $fileTemplate;
    }

    public static function getRandomName()
    {
        $faker = FakerFactory::create();

        return $faker->name();
    }

    public static function getRandomBoolean()
    {
        return rand(0, 1) == 1;
    }

    public static function getValue($fieldType)
    {
        switch ($fieldType) {
            case 'string':
                return self::getRandomName();
                break;
            case 'boolean':
                return self::getRandomBoolean();
                break;

            default:
                // code...
                break;
        }
    }

    public static function getFields($fields)
    {
        $fieldsStr = '';
        foreach ($fields as $fieldName => $attributes) {
            $fieldValue = self::getValue($attributes['type']);
            $fieldsStr .= "'$fieldName' => '$fieldValue',\n";
        }

        return $fieldsStr;
    }

    public static function getRecord($tableName, $fields)
    {
        $fields = self::getFields($fields);
        $record = "DB::table('$tableName')->insert([
        $fields
              ]);";

        return $record;

    }

    public static function make($migrations, $amount = 10)
    {

        foreach ($migrations as $tableName => $fields) {
            $records = '';
            for ($i = 0; $i < $amount; $i++) {
                $records .= self::getRecord($tableName, $fields)."\n";
            }

            $fileTemplate = self::template($records);
            $fileName = Naming::getModelNameFromTableName($tableName).'Seeder.php';
            $filePath = base_path('database/seeders/'.$fileName);
            file_put_contents($filePath, $fileTemplate);

        }
    }
}
