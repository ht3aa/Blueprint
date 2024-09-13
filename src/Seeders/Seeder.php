<?php

namespace Hasanweb\Blueprint\Seeders;

use Faker\Factory as FakerFactory;
use Hasanweb\Blueprint\Helpers\Format;
use Hasanweb\Blueprint\Helpers\Name;

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
        return '"'.$value.'"';
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

        return rand(0, 1);
    }

    public static function getRelationId($relation)
    {
        $tableName = Name::getTableNameFromForeignKeyName($relation);

        return "DB::table('$tableName')->inRandomOrder()->first()->id";
    }

    public static function getRandomStatus()
    {
        $status = ['active', 'inactive', 'pending', 'rejected', 'approved', 'suspended', 'blocked', 'deleted'];

        return self::outputAsString($status[rand(0, count($status))]);
    }

    public static function getApprobateString($fieldName)
    {
        switch ($fieldName) {
            case 'name':
                return self::getRandomName();
                break;

            case 'description':
                return self::getRandomText();
                break;

            case 'email':
                return self::getRandomEmail();
                break;

            case 'status':
                return self::getRandomEmail();
                break;
            default:
                return self::getRandomName();
                break;
        }

    }

    public static function getRandomText()
    {
        $description = self::$faker->text();

        return self::outputAsString($description);
    }

    public static function getRandomDecimal()
    {
        $decimal = self::$faker->randomFloat(2, 0, 1000);

        return self::outputAsString($decimal);
    }

    public static function getRandomInteger()
    {
        $integer = rand(1, 1000);

        return self::outputAsString($integer);
    }

    public static function getRandomTime()
    {
        $time = self::$faker->time();

        return self::outputAsString($time);
    }

    public static function getRandomDate()
    {
        $date = self::$faker->date();

        return self::outputAsString($date);
    }

    public static function getValue($fieldName, $fieldType)
    {
        switch ($fieldType) {
            case 'string':
                return self::getApprobateString($fieldName);
                break;
            case 'text':
                return self::getRandomText();
                break;
            case 'decimal':
                return self::getRandomDecimal();
                break;
            case 'integer':
                return self::getRandomInteger();
                break;
            case 'boolean':
                return self::getRandomBoolean();
                break;

            case 'time':
                return self::getRandomTime();
                break;

            case 'date':
                return self::getRandomDate();
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
            $fieldsStr .= Format::addTabs(3)."'$fieldName' => $fieldValue,\n";
        }

        return $fieldsStr;
    }

    public static function getRecord($tableName, $fields)
    {
        $fields = self::getFields($fields);
        $record = "
        DB::table('$tableName')->insert([
$fields
        ]);
";

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

            $seederName = Name::getModelNameFromTableName($tableName).'Seeder';
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
