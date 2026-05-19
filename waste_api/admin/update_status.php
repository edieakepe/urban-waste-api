<?php
session_start();
include "../db_connect.php";

// 🔐 CHECK ADMIN LOGIN
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// ✅ PROCESS UPDATE
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {

        $stmt = $conn->prepare("UPDATE reports SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit;
        } else {
            echo "Error updating status";
        }

    } else {
        echo "Invalid data";
    }
}
?>