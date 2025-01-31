<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

require '../config/db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$search = isset($_POST['search']) ? $_POST['search'] : '';
$filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : '';
$search_param = '%' . $search . '%';

$query = "SELECT e.*, 
          (SELECT COUNT(*) FROM attendees WHERE attendees.event_id = e.id) AS registered_attendees 
          FROM events e 
          WHERE e.created_by = ? AND e.name LIKE ?";
$params = ['is', $user_id, $search_param];

// Apply date filter if provided
if (!empty($filter_date)) {
    $query .= " AND e.date = ?";
    $params[0] .= 's';
    $params[] = $filter_date;
}

$query .= " ORDER BY e.id ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param(...$params);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);

$stmt->close();
$conn->close();
?>
