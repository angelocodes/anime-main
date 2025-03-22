<?php

//host
$HOST = "localhost";

//dbname
$DBNAME = "anime";

//user
$USER = "root";

//pass
$PASS = "";

try {
    $conn = new PDO("mysql:host=" . $HOST . ";dbname=" . $DBNAME . "", $USER, $PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error" . $e->getMessage();
}
