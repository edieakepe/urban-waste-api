<?php

$host = "containers-us-west-45.railway.app";
$user = "root";
$password = "ujnGbbeqhPLavFRtaSKxcjPVqMBmSSEM";
$database = "railway";
$port = "6543";

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

?>
