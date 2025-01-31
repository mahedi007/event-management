<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

require '../config/db_connection.php';

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = intval($_POST['id']);
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
    $stmt->bind_param('ii', $eventId, $userId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Event deleted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete event."]);
    }

    $stmt->close();
}

$conn->close();
?>
