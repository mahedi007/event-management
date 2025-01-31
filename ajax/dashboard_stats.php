<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require '../config/db_connection.php';

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$user_id = $_SESSION['user_id'];

// Initialize stats
$stats = [
    'total_events' => 0,
    'total_attendees' => 0,
    'total_capacity' => 0,
    'completed_events' => [],
    'upcoming_events' => [],
    'events_today' => [],
];

// Fetch total events
$totalEventsQuery = "SELECT COUNT(*) AS total_events FROM events WHERE created_by = ?";
$stmt = $conn->prepare($totalEventsQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $stats['total_events'] = $row['total_events'];
}
$stmt->close();

// Fetch total registered attendees
$totalAttendeesQuery = "
    SELECT COUNT(*) AS total_attendees 
    FROM attendees 
    WHERE event_id IN (SELECT id FROM events WHERE created_by = ?)";
$stmt = $conn->prepare($totalAttendeesQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $stats['total_attendees'] = $row['total_attendees'];
}
$stmt->close();

// Fetch total capacity
$totalCapacityQuery = "SELECT SUM(capacity) AS total_capacity FROM events WHERE created_by = ?";
$stmt = $conn->prepare($totalCapacityQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $stats['total_capacity'] = $row['total_capacity'];
}
$stmt->close();

// Fetch completed events
$completedEventsQuery = "SELECT name, date FROM events WHERE created_by = ? AND date < CURDATE()";
$stmt = $conn->prepare($completedEventsQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats['completed_events'][] = $row;
}
$stmt->close();

// Fetch upcoming events
$upcomingEventsQuery = "SELECT name, date FROM events WHERE created_by = ? AND date >= CURDATE()";
$stmt = $conn->prepare($upcomingEventsQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats['upcoming_events'][] = $row;
}
$stmt->close();

// Fetch events today
$eventsTodayQuery = "SELECT name FROM events WHERE created_by = ? AND date = CURDATE()";
$stmt = $conn->prepare($eventsTodayQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats['events_today'][] = $row['name'];
}
$stmt->close();

$conn->close();

echo json_encode($stats);
?>
