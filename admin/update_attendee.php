<?php
require '../config/db_connection.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the attendee ID, name, and email from the form
    $attendee_id = isset($_POST['attendee_id']) ? (int)$_POST['attendee_id'] : 0;
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    $attendee_name = isset($_POST['attendee_name']) ? trim($_POST['attendee_name']) : '';
    $attendee_email = isset($_POST['attendee_email']) ? trim($_POST['attendee_email']) : '';

    // Validate inputs
    if ($attendee_id === 0 || empty($attendee_name) || empty($attendee_email)) {
        die("Invalid data provided.");
    }

    // Update attendee details in the database
    $sql = "UPDATE attendees SET name = ?, email = ? WHERE id = ? AND event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $attendee_name, $attendee_email, $attendee_id, $event_id);

    if ($stmt->execute()) {
        // Redirect back to the attendee list with a success message
        header("Location: attendee_list.php?event_id=$event_id&success=1");
        exit();
    } else {
        // Handle update error
        die("Error updating attendee: " . $conn->error);
    }
} else {
    // Redirect to home if accessed without POST
    header("Location: events.php");
    exit();
}
?>
