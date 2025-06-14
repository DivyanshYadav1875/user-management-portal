<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name'])) {
    echo json_encode(["success" => false, "message" => "Missing username"]);
    exit;
}

$name = trim($data['name']);

$conn = new mysqli("localhost", "root", "", "user_portal");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, username, user_type FROM users WHERE username = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $user['id'],
            "username" => $user['username'],
            "user_type" => $user['user_type']
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "User not found or session expired"]);
}
?>
