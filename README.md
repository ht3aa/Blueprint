# Blueprint
scaffold your app with ease. 

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
        "[tableName]": {
            "[tableColumnName]": {
                "type": "string",
                "nullable": true
            }
        }
    },

    "models": {
        "[modelName]": {
            "fillable": ["name", "email"],
            "relations": {
                "[relationType]": ["relationName"]
            }
        }
    },


    "with-controller-resources" : true,
    "with-filament-resources": true
}
```
Anything inside [] is a placeholder. else is a keyword that should't be changed. 

