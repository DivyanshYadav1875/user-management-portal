<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "Missing ID"]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "user_portal");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection error"]);
    exit;
}

$id = $data['id'];
$stmt = $conn->prepare("SELECT username, email, user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $user = $res->fetch_assoc();
    echo json_encode(["success" => true, "user_type" => $user['user_type'], "data" => $user]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
