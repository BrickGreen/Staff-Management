<?php

$servername = ""; //name of database instance. Look up in mysql workbench
$dbname = ""; //name of database
$dsn = "mysql:host=$servername;dbname=$dbname;";
$username = "";
$password = "";

$conn = new PDO($dsn, $username); //open connection to local db

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>