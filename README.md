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
                "attributes": {
                    "unique": true,
                    "constraint": "",
                    "nullable": "hello",
                    ...
                }
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
The order of attributes is the way that it will be written (unique(true)->constraint()->nullable("hello")...). 
Empty string is the default value. 
