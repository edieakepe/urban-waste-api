<?php

include "db.php";

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($name == '' || $email == '' || $password == '') {

    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);

    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name,email,password,role)
        VALUES ('$name','$email','$hashedPassword','user')";

if(mysqli_query($conn,$sql)){

    echo json_encode([
        "status" => "success",
        "message" => "User registered successfully"
    ]);

}else{

    echo json_encode([
        "status" => "error",
        "message" => "Registration failed"
    ]);
}