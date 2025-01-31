<?php
require dirname(__DIR__) . '../config/db_connection.php';

if (!isset($_GET['event_id']) || !isset($_GET['query'])) {
    die("Invalid parameters.");
}

$event_id = (int)$_GET['event_id'];
$query = "%" . $conn->real_escape_string($_GET['query']) . "%";

$sql = "SELECT id, name, email FROM attendees WHERE event_id = ? AND (name LIKE ? OR email LIKE ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $event_id, $query, $query);
$stmt->execute();
$result = $stmt->get_result();
$attendees = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Generate table rows dynamically
if (empty($attendees)) {
    echo '<tr><td colspan="4" class="text-center">No attendees found.</td></tr>';
} else {
    $serial_number = 1; 
    foreach ($attendees as $attendee) {
        echo '<tr>';
        echo '<td>' . $serial_number . '</td>'; 
        echo '<td>' . htmlspecialchars($attendee['name']) . '</td>';
        echo '<td>' . htmlspecialchars($attendee['email']) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-warning px-3 py-2 me-2 edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-id="' . htmlspecialchars($attendee['id']) . '" data-name="' . htmlspecialchars($attendee['name']) . '" data-email="' . htmlspecialchars($attendee['email']) . '">Edit</button>';
        echo '<a href="../admin/delete_attendee.php?attendee_id=' . $attendee['id'] . '&event_id=' . $event_id . '" class="btn btn-sm btn-danger px-3 py-2 me-2 remove-btn">Remove</a>';
        echo '</td>';
        echo '</tr>';
        $serial_number++; 
    }
}
?>
