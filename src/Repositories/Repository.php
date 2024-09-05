<?php

namespace Hasanweb\Blueprint\Repositories;

class Repository
{
    private static function template($className, $modelName)
    {
        $fileTemplate = "<?php

namespace App\Repositories;

use App\Models\\$modelName;

class $className 
{
    public function all()
    {
        return $modelName::all();
    }

    public function find(\$id)
    {
        return $modelName::find(\$id);
    }

    public function create(\$data)
    {
      return $modelName::create(\$data);
    }

    public function update(\$id, \$data)
    {
      return $modelName::find(\$id)->update(\$data);
    }

    public function delete(\$id)
    {
      return $modelName::destroy(\$id);
    }
}
    ";

        return $fileTemplate;
    }

    public static function make($data)
    {

        if (! is_dir(base_path('app/Repositories'))) {
            mkdir(base_path('app/Repositories'));
        }
        foreach ($data as $className => $key) {
            $fileTemplate = self::template($className, $key['model']);
            $filePath = base_path("app/Repositories/$className.php");
            file_put_contents($filePath, $fileTemplate);
        }
    }
}
