<?php
$host = "sql300.infinityfree.com";
$user = "if0_41863641";
$password = "Akdiamond123";
$database = "if0_41863641_waste_report_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>