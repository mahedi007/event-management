<?php
require '../config/db_connection.php';

// Check if an event_id is provided
if (isset($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
    $sql = "SELECT name, email FROM attendees WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
} else {
    // If no event_id is provided, get all attendees
    $sql = "SELECT name, email FROM attendees";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Set the headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . (isset($_GET['event_id']) ? "attendee_list_event_$event_id.csv" : "all_attendees.csv"));

    // Open the output stream
    $output = fopen('php://output', 'w');

    // Add the column headers
    fputcsv($output, ['Name', 'Email']);

    // Add rows to the CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['name'], $row['email']]);
    }

    fclose($output);
} else {
    die('No attendees found.');
}
?>
