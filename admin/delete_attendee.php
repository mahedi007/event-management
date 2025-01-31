<?php
require '../config/db_connection.php';

// Check if the attendee ID is provided
if (isset($_GET['attendee_id']) && isset($_GET['event_id'])) {
    $attendee_id = (int)$_GET['attendee_id'];
    $event_id = (int)$_GET['event_id'];

    // Delete attendee from the database
    $sql = "DELETE FROM attendees WHERE id = ? AND event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $attendee_id, $event_id);

    if ($stmt->execute()) {
        header("Location: attendee_list.php?event_id=$event_id&deleted=1");
        exit();
    } else {
        die("Error deleting attendee: " . $conn->error);
    }
} else {
    header("Location: events.php");
    exit();
}
?>