<?php
require_once "db_connect.php";

$sql = "INSERT INTO reports (description, location, image)
VALUES ('Test garbage', 'Buea Market', '')";

if ($conn->query($sql)) {
    echo "Inserted successfully";
} else {
    echo "Error: " . $conn->error;
}
?>