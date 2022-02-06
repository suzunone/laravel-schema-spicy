# laravel-schema-spicy
In a laravel framework project, run Schemaspy by guessing a foreign key.

# Set up
## composer package install
```shell
composer require suzunone/laravel-schema-spicy
```

## config file add
```shell
php artisan vendor:publish --provider="Suzunone\LaravelSchemaSpicy\LaravelSchemaSpicyServiceProvider"
```

## schemaspy-6.1.0.jar
wget https://github.com/schemaspy/schemaspy/releases/download/v6.1.0/schemaspy-6.1.0.jar


## get odbc driver

### mysql
```
wget https://dev.mysql.com/get/Downloads/Connector-J/mysql-connector-java-8.0.28.zip
unzip ./mysql-connector-java-8.0.28.zip "*.jar"
mv mysql-connector-java-8.0.28/mysql-connector-java-8.0.28.jar ./
```

