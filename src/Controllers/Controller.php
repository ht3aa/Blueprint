<?php

namespace Hasanweb\Blueprint\Controllers;

class Controller
{
    private static function template($className, $repositoryName)
    {
        $repositoryVariable = '_'.lcfirst($repositoryName);
        $fileTemplate = "<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\\$repositoryName;

class $className extends Controller
{

    private $repositoryName \$$repositoryVariable;


    public function __construct($repositoryName \$$repositoryVariable)
    {
        \$this->$repositoryVariable = \$$repositoryVariable;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      
      return view();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request \$request)
    {
        return \$this->$repositoryVariable ->create(\$request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string \$id)
    {
      
       return \$this->$repositoryVariable ->find(\$id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string \$id)
    {
      return view();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request, string \$id)
    {
      return \$this->$repositoryVariable ->update(\$id, \$request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string \$id)
    {
      return \$this->$repositoryVariable ->delete(\$id);
    }
}
    ";

        return $fileTemplate;
    }

    public static function make($data)
    {

        foreach ($data as $className => $key) {
            $fileTemplate = self::template($className, $key['repository']);
            $filePath = base_path("app/Http/Controllers/$className.php");
            file_put_contents($filePath, $fileTemplate);
        }

    }
}
