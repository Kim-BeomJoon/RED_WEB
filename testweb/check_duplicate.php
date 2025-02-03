<?php
require_once('config.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $value = $_POST['value'] ?? '';
    $userId = $_SESSION['user_id'];

    if (empty($type) || empty($value)) {
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }

    $column = ($type === 'email') ? 'email' : 'nickname';
    $stmt = $conn->prepare("SELECT id FROM users WHERE $column = ? AND id != ?");
    $stmt->bind_param("si", $value, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    echo json_encode(['exists' => $result->num_rows > 0]);
    exit;
}

echo json_encode(['error' => 'Invalid request']); 