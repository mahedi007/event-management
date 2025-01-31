<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../config/db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the event ID from the query string
if (!isset($_GET['event_id'])) {
    header("Location: events.php");
    exit;
}

$event_id = $_GET['event_id'];

// Fetch the event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param('i', $event_id);
$stmt->execute();
$event_result = $stmt->get_result();
$event = $event_result->fetch_assoc();
$stmt->close();

if (!$event) {
    header("Location: events.php");
    exit;
}

// Handle attendee registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendee_name = $_POST['attendee_name'];
    $attendee_email = $_POST['attendee_email'];

    // Validate inputs
    if (!empty($attendee_name) && !empty($attendee_email)) {
        if (!filter_var($attendee_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address!";
        } else {
            // Check if the email is already registered for this event
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM attendees WHERE event_id = ? AND email = ?");
            $stmt->bind_param('is', $event_id, $attendee_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
        
            if ($row['total'] > 0) {
                $error = "This email is already registered for this event!";
            } else {
                // Check current number of attendees
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM attendees WHERE event_id = ?");
                $stmt->bind_param('i', $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $current_attendees = $row['total'];
                $stmt->close();
        
                if ($current_attendees < $event['capacity']) {
                    // Register the attendee
                    $stmt = $conn->prepare("INSERT INTO attendees (event_id, name, email) VALUES (?, ?, ?)");
                    $stmt->bind_param('iss', $event_id, $attendee_name, $attendee_email);
                    $stmt->execute();
                    $stmt->close();
        
                    // Redirect to avoid form resubmission
                    header("Location: register_attendee.php?event_id=$event_id&success=1");
                    exit;
                } else {
                    $error = "This event has reached its maximum capacity.";
                }
            }
        }
        
    } else {
        $error = "All fields are required!";
    }
}

// Fetch the updated number of attendees
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM attendees WHERE event_id = ?");
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$current_attendees = $row['total'];
$stmt->close();

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Attendee registered successfully!";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Attendee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel ="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Register Attendee for Event:<span class="ev-t"> <?= htmlspecialchars($event['name']) ?></span></h2>
    <p><strong>Event Date:</strong> <?= $event['date'] ?></p>
    <p><strong>Capacity:</strong> <?= $event['capacity'] ?></p>
    <p><strong>Registered Attendees:</strong> <?= $current_attendees ?> / <?= $event['capacity'] ?></p>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="attendee_name" class="form-label">Attendee Name</label>
            <input type="text" id="attendee_name" name="attendee_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="attendee_email" class="form-label">Attendee Email</label>
            <input type="email" id="attendee_email" name="attendee_email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <a href="events.php" class="btn btn-secondary mt-3">Back to Events</a>
</div>
</body>
</html>
