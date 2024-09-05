# Blueprint
scaffold your app with ease. (for now it generate migrations,models and filament resources for you)

## Installation
first install the package 
```
composer require hasanweb/blueprint --dev
```
then run this command
```
php artisan blueprint:make path/to/your/json/file
```

## Json file Syntax
```
{
    "migrations": {
        "tableName": {
            "tableColumnName": {
                "type": "string",
                "nullable": true
            },
            "tableColumnName": {
                "type": "string",
                "nullable": false
            }
        }
    },

    "models": {
        "modelName": {
            "fillable": ["name", "email"],
            "relations": {
                "relationType": "relationName"
            }
        }
    },

    "repositories": {
        "repositoryName": {
            "model": "modelName"
        }
    },

    "controllers": {
        "controllerName": {
            "repository": "repositoryName"
        }
    },

    "routes" : {
        "resources": {
            "resourceName": "controllerName"
        }
    },

    "with-filament-resources": true
}
```

