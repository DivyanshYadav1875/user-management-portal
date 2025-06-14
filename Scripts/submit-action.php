<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['action'])) {
    echo json_encode(["success" => false, "message" => "Missing action"]);
    exit;
}

$action = $data['action'];

if ($action === 'login') {
    handleLogin($data);
} elseif ($action === 'register') {
    handleRegister($data);
} else {
    echo json_encode(["success" => false, "message" => "Invalid action"]);
    exit;
}

function handleLogin($data) {
    if (!isset($data['name']) || !isset($data['password'])) {
        echo json_encode(["success" => false, "message" => "Missing login credentials"]);
        return;
    }

    $name = trim($data['name']);
    $password = trim($data['password']);

    $conn = new mysqli("localhost", "root", "", "user_portal");
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "DB connection error"]);
        return;
    }

    $stmt = $conn->prepare("SELECT id, username, user_type FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $name, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        setcookie("remembered_user", $user['username'], time() + (86400 * 30), "/");

        echo json_encode([
            "success" => true,
            "message" => "User authenticated",
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "user_type" => $user['user_type']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid username or password"]);
    }
}

function handleRegister($data) {
    if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
        echo json_encode(["success" => false, "message" => "Missing registration fields"]);
        return;
    }

    $username = trim($data['name']);
    $email = trim($data['email']);
    $password = trim($data['password']);

    $conn = new mysqli("localhost", "root", "", "user_portal");
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "DB connection error"]);
        return;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "User already exists"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type, created_at) VALUES (?, ?, ?, 'basic', NOW())");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        setcookie("remembered_user", $username, time() + (86400 * 30), "/");
        echo json_encode(["success" => true, "message" => "User registered"]);
    } else {
        echo json_encode(["success" => false, "message" => "Registration failed"]);
    }
}
?>
