<?php
require 'config/db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch available events
$sql = "SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Events</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/event_public.css">
</head>
<body>

<div class="container">
    <h2 class="mb-4">🎉 Explore Upcoming Events</h2>
    <p>Select an event and reserve your spot now!</p>
    <a href="login.php" class="btn btn-warning m-3">Back to Login</a>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="event-card">
                        <h4 class="title-text"><?= htmlspecialchars($event['name']) ?></h4>
                        <p>📆 Date: <?= $event['date'] ?></p>
                        <p>📝 Description: <?= $event['description'] ?></p>
                        <p>👥 Capacity: <?= $event['capacity'] ?></p>
                        <a href="public_registration.php?event_id=<?= $event['id'] ?>" class="btn btn-primary">Register Now</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="alert alert-warning">No upcoming events available.</p>
    <?php endif; ?>
</div>

</body>
</html>

