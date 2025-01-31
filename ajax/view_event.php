<?php
require '../config/db_connection.php';

$event_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>";
    echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
    echo "<p><strong>Date:</strong> " . htmlspecialchars($row['date']) . "</p>";
    echo "<p><strong>Capacity:</strong> " . htmlspecialchars($row['capacity']) . "</p>";
} else {
    echo "Event not found.";
}
?>
