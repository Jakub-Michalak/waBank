<?php
    ini_set("log_errors", 1);
    ini_set("error_log", "/tmp/php-error.log");
    error_log( "Hello, errors!" );

    $host        = "host=localhost";
    $port        = "port=5432";
    $dbname      = "dbname=waBank";
    $credentials = "user=wabank password=";
    $conn = pg_connect( "$host $port $dbname $credentials" )or die("Failed to create connection to database: ". pg_last_error(). "<br/>");
    if (!$conn) {
      echo "Wystąpił błąd podczas łączenia do bazy danych.\n";
      exit;
    }

    $result = pg_query($conn, "select * from Customer");
    if (!$result) {
    echo "Wystąpił błąd podczas przetwarzania kwerendy.\n";
    exit;
    }
?>
