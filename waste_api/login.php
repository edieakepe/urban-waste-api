<?php
echo password_hash("123456", PASSWORD_DEFAULT);
?>
<?php

include "db.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email == '' || $password == '') {
    echo json_encode([
        "status" => "error",
        "message" => "Email and password required"
    ]);
    exit();
}

$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

    $user = mysqli_fetch_assoc($result);

    if(password_verify($password, $user['password'])){

        echo json_encode([
            "status" => "success",
            "user" => $user
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Invalid password"
        ]);
    }

}else{

    echo json_encode([
        "status" => "error",
        "message" => "User not found"
    ]);
}

