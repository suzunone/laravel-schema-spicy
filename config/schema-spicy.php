<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Eloquent Model Name Space
    |--------------------------------------------------------------------------
    |
    | This value is  top-level namespace for the model to be analyzed.
    |
    */
    'model_name_space' => 'App\Models',


    /*
    |--------------------------------------------------------------------------
    | Classes to exclude from the analysis
    |--------------------------------------------------------------------------
    |
    | This value is specify an array of classes to be excluded
    | from parsing when creating XML with SchemaSpicy.
    |
    */
    'exclude_classes' => [],


    /*
    |--------------------------------------------------------------------------
    | Path to generate or reference schemameta XML file
    |--------------------------------------------------------------------------
    |
    | This value is  indicates the path to store the schemameta XML
    | for the schema-spicy:xml command or the path referenced
    | by the schema-spicy:command command.
    |
    */
    'schema_meta_path' => base_path('schemaspy-meta.xml'),


    /*
    |--------------------------------------------------------------------------
    | Path of the schemameta XML file on which schemameta XML is based
    |--------------------------------------------------------------------------
    |
    | This value is specifies the default schemameta XML to be referenced
    | when schema-spicy:xml creates schemameta XML.
    | The schema-spicy:xml command creates the schemameta XML by appending it to the specified XML.
    | If null is specified, an empty schemameta XML will be used.
    |
    */
    'schema_meta_template_path' => null,


    /*
    |--------------------------------------------------------------------------
    | schemaspy.properties file path
    |--------------------------------------------------------------------------
    |
    | This value is the location of the schemaspy.properties file
    | created by the schema-spicy:property command.
    | This is also the path referenced by schema-spicy:command.
    |
    */
    'schemaspy_properties_path' => base_path('schemaspy.properties'),


    /*
    |--------------------------------------------------------------------------
    | Where to save the artifact.
    |--------------------------------------------------------------------------
    |
    | This value is the destination
    | where the schema-spicy:command command will create the document.
    |
    */
    'er_save_path' => 'er',


    /*
    |--------------------------------------------------------------------------
    | schemaspy.jar path
    |--------------------------------------------------------------------------
    |
    | This value is destination path for schemaspy.jar.
    |
    | https://github.com/schemaspy/schemaspy/releases
    |
    */
    'schemaspy_jar_path' => base_path('schemaspy-6.1.0.jar'),


    /*
    |--------------------------------------------------------------------------
    | jdbc_driver jar path
    |--------------------------------------------------------------------------
    |
    | This value is destination path of the JDBC driver.
    | from parsing when creating XML with SchemaSpicy.
    |
    | MySQL https://dev.mysql.com/downloads/connector/j/
    | Oracle https://www.oracle.com/jp/database/technologies/appdev/jdbc.html
    | PostgreSQL https://jdbc.postgresql.org/
    | SQL Server https://docs.microsoft.com/ja-jp/sql/connect/jdbc/download-microsoft-jdbc-driver-for-sql-server?view=sql-server-ver15
    */
    'jdbc_driver_path' => base_path('mysql-connector-java-8.0.28.jar'),


];
