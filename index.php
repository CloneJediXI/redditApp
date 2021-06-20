<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'vendor/autoload.php';

    use App\SQLiteConnection;

    $connection = new SQLiteConnection();
    $pdo = $connection->connect();
    if ($pdo != null){
        echo 'Connected to the SQLite database successfully! <br/>';
        print_r($connection->getTableList());
        //echo $connection->getTableList();
    }else{
        echo 'Whoops, could not connect to the SQLite database! <br/>';
    }
?>