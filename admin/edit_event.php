<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access. Please log in."]);
    exit;
}

// Database connection
require '../config/db_connection.php';

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all required fields are set
    if (
        isset($_POST['id'], $_POST['name'], $_POST['description'], $_POST['date'], $_POST['capacity']) &&
        !empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['date']) && !empty($_POST['capacity'])
    ) {
        // Get values from POST request
        $id = intval($_POST['id']);
        $user_id = $_SESSION['user_id']; // Logged-in user's ID
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $date = trim($_POST['date']);
        $capacity = intval($_POST['capacity']);

        // Check if the event exists for the logged-in user
        $stmt = $conn->prepare("SELECT id FROM events WHERE id = ? AND created_by = ?");
        $stmt->bind_param('ii', $id, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            echo json_encode(["status" => "error", "message" => "Event not found or unauthorized."]);
            $stmt->close();
            exit;
        }
        $stmt->close();

        // Update event in the database
        $stmt = $conn->prepare("UPDATE events SET name = ?, description = ?, date = ?, capacity = ? WHERE id = ? AND created_by = ?");
        $stmt->bind_param('sssiii', $name, $description, $date, $capacity, $id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Event updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
?>
