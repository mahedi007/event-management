<?php
session_start();

require '../config/db_connection.php';

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access! Please <a href='login.php'>login</a> first.");
}

$user_id = $_SESSION['user_id']; 

// Pagination settings
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch attendees for this specific user
$attendeesQuery = "SELECT attendees.name, attendees.email, events.name AS event_name 
                   FROM attendees 
                   JOIN events ON attendees.event_id = events.id 
                   WHERE created_by = ? 
                   ORDER BY events.date DESC 
                   LIMIT ? OFFSET ?";

$stmt = $conn->prepare($attendeesQuery);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get total count for this user
$totalAttendeesQuery = "SELECT COUNT(*) AS total FROM attendees 
                        JOIN events ON attendees.event_id = events.id 
                        WHERE created_by = ?";
$stmtCount = $conn->prepare($totalAttendeesQuery);
$stmtCount->bind_param("i", $user_id);
$stmtCount->execute();
$totalAttendees = $stmtCount->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalAttendees / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendee List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/batendee.css">
</head>
<body>
<div class="container">
    <h2>🎟️ All Event Attendees</h2>
    <div class="text-left py-3 mt-3">
        <a href="dashboard.php" class="btn btn-warning">⬅️ Back to Dashboard</a>
        <a href="export_attendees.php?" class="btn btn-success">Export All Attendee List to CSV</a>
    </div>
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Event</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['event_name']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalAttendees > 10): ?>               
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>    
    <?php else: ?>
        <p class="alert alert-warning text-center">No attendees found for your events.</p>
    <?php endif; ?>
</div>
</body>
</html>

