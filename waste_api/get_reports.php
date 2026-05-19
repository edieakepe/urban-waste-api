<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "db.php";

$sql = "SELECT * FROM reports ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {

    echo json_encode([
        "status" => "error",
        "message" => "Query failed"
    ]);

    exit();
}

$reports = [];

while ($row = mysqli_fetch_assoc($result)) {

    $reports[] = [
        "id" => $row["id"] ?? "",
        "user_id" => $row["user_id"] ?? null,
        "description" => $row["description"] ?? "",
        "location" => $row["location"] ?? "",
        "image" => $row["image"] ?? "",
        "status" => $row["status"] ?? "",
        "created_at" => $row["created_at"] ?? ""
    ];
}

echo json_encode([
    "status" => "success",
    "reports" => $reports
]);