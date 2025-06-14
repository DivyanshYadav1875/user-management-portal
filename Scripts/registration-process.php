<?php
function registerUser($username, $email, $password) {
    $conn = new mysqli("localhost", "root", "", "user_portal");
    if ($conn->connect_error) {
        return ["success" => false, "message" => "DB connection error"];
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        return ["success" => false, "message" => "User already exists"];
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type, created_at) VALUES (?, ?, ?, 'basic', NOW())");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        setcookie("remembered_user", $username, time() + (86400 * 30), "/");
        return ["success" => true, "message" => "User registered"];
    } else {
        return ["success" => false, "message" => "Registration failed"];
    }
}
