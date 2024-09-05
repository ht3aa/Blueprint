<?php

namespace Hasanweb\Blueprint\Routes;

class Route
{
    private static function template($routes, $controllersNamespace)
    {
        $fileTemplate = "<?php

use Illuminate\Support\Facades\Route;
    $controllersNamespace

      $routes
    ";

        return $fileTemplate;
    }

    private static function resourcesRoutes($data)
    {
        $routes = '';
        $controllersNamespace = '';
        foreach ($data as $routeName => $className) {
            $routes .= "Route::resource('$routeName', $className::class);\n";
            $controllersNamespace .= "use App\Http\Controllers\\$className;\n";
        }

        return ['routes' => $routes, 'controllersNamespace' => $controllersNamespace];
    }

    public static function make($data)
    {
        $resourcesRoutes = self::resourcesRoutes($data['resources']);
        $fileTemplate = self::template($resourcesRoutes['routes'], $resourcesRoutes['controllersNamespace']);
        $filePath = base_path('routes/web.php');
        file_put_contents($filePath, $fileTemplate);
    }
}
