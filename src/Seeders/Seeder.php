<?php

namespace Hasanweb\Blueprint\Seeders;

use Faker\Factory as FakerFactory;
use Hasanweb\Blueprint\Helpers\Naming;

class Seeder
{
    private static $faker;

    private static function DataBaseSeederTemplate($seeders)
    {
        $fileTemplate = "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \$this->call([
          $seeders
        ]);
    }
}



";

        return $fileTemplate;
    }

    private static function template($seederName, $records)
    {
        $fileTemplate = "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class $seederName extends Seeder
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

    public static function outputAsString($value)
    {
        return "'$value'";
    }

    public static function getRandomName()
    {
        $name = self::$faker->name();

        return self::outputAsString($name);
    }

    public static function getRandomEmail()
    {
        $email = self::$faker->email();

        return self::outputAsString($email);
    }

    public static function getRandomBoolean()
    {
        $boolean = rand(0, 1) == 1;

        return self::outputAsString($boolean);
    }

    public static function getRelationId($relation)
    {
        $tableName = Naming::getTableNameFromForeignKeyName($relation);

        return "DB::table('$tableName')->inRandomOrder()->first()->id";
    }

    public static function getApprobateString($fieldName)
    {
        switch ($fieldName) {
            case 'name':
                return self::getRandomName();
                break;

            case 'email':
                return self::getRandomEmail();
                break;

            default:
                // code...
                break;
        }

    }

    public static function getValue($fieldName, $fieldType)
    {
        switch ($fieldType) {
            case 'string':
                return self::getApprobateString($fieldName);
                break;
            case 'boolean':
                return self::getRandomBoolean();
                break;
            case 'foreignId':
                return self::getRelationId($fieldName);
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
            $fieldValue = self::getValue($fieldName, $attributes['type']);
            $fieldsStr .= "'$fieldName' => $fieldValue,\n";
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

    public static function make($migrations, $amount, $tableOutput)
    {

        $seedersToBeCalled = '';
        self::$faker = FakerFactory::create();

        $tableRows = [];

        foreach ($migrations as $tableName => $fields) {
            $records = '';

            for ($i = 0; $i < $amount; $i++) {
                $records .= self::getRecord($tableName, $fields)."\n";
            }

            $seederName = Naming::getModelNameFromTableName($tableName).'Seeder';
            $seedersToBeCalled .= $seederName.'::class,'."\n";
            $fileTemplate = self::template($seederName, $records);

            array_push($tableRows, [$seederName]);
            $filePath = base_path('database/seeders/'.$seederName.'.php');
            file_put_contents($filePath, $fileTemplate);

        }

        $dataBaseSeederFile = self::DataBaseSeederTemplate($seedersToBeCalled);
        $filePath = base_path('database/seeders/DatabaseSeeder.php');
        file_put_contents($filePath, $dataBaseSeederFile);

        // Set the table headers.
        $tableOutput->setHeaders([
            'Generated Seeders Files',
        ]);
        $tableOutput->setRows($tableRows);
        $tableOutput->render();

    }
}
