<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli("localhost", "root", "", "user_portal");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$action = $data['action'] ?? '';

if ($action === 'create') {
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');
    $user_type = trim($data['user_type'] ?? 'basic');

    if (!$username || !$email || !$password) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    // Prevent duplicates
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "User already exists"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $username, $email, $password, $user_type);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User created"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to create user"]);
    }

} elseif ($action === 'delete') {
    $id = intval($data['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid user ID"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => "Deletion failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid action"]);
}
?>
