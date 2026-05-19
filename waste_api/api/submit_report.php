<?php

header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $description = $_POST['description'];
    $location = $_POST['location'];

    $imageName = time() . "_" . $_FILES['image']['name'];

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($imageName);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

        $sql = "INSERT INTO reports(description, location, image, status)
                VALUES('$description', '$location', '$imageName', 'pending')";

        if ($conn->query($sql)) {

            echo json_encode([
                "success" => true,
                "message" => "Report submitted successfully"
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "message" => "Database insert failed"
            ]);
        }

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Image upload failed"
        ]);
    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request"
    ]);
}

?>