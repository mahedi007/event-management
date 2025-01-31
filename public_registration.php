<?php
require 'config/db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get event ID from query string
if (!isset($_GET['event_id'])) {
    die("Invalid event request.");
}

$event_id = $_GET['event_id'];

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param('i', $event_id);
$stmt->execute();
$event_result = $stmt->get_result();
$event = $event_result->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendee_name = $_POST['attendee_name'];
    $attendee_email = $_POST['attendee_email'];

    if (!empty($attendee_name) && !empty($attendee_email)) {
        if (!filter_var($attendee_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email!";
        } else {
            // Check if the email is already registered
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM attendees WHERE event_id = ? AND email = ?");
            $stmt->bind_param('is', $event_id, $attendee_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if ($row['total'] > 0) {
                $error = "You are already registered for this event!";
            } else {
                // Check event capacity
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM attendees WHERE event_id = ?");
                $stmt->bind_param('i', $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $current_attendees = $row['total'];
                $stmt->close();

                if ($current_attendees < $event['capacity']) {
                    // Register attendee
                    $stmt = $conn->prepare("INSERT INTO attendees (event_id, name, email) VALUES (?, ?, ?)");
                    $stmt->bind_param('iss', $event_id, $attendee_name, $attendee_email);
                    $stmt->execute();
                    $stmt->close();

                    // Send confirmation email
                    $subject = "Event Registration Confirmation";
                    $message = "Dear $attendee_name,\n\nYou have successfully registered for the event: " . $event['name'] . ".\n\nEvent Date: " . $event['date'] . "\n\nThank you for registering!";
                    $headers = "From: no-reply@yourevent.com";
                    mail($attendee_email, $subject, $message, $headers);

                    // Redirect to avoid form resubmission
                    header("Location: public_registration.php?event_id=$event_id&success=1");
                    exit;
                } else {
                    $error = "Event is full.";
                }
            }
        }
    } else {
        $error = "All fields are required!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel ="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="registration-card">
        <h2 class="text-center">Register for Event</h2>
        <p class="text-center ev-t"><strong><?= htmlspecialchars($event['name']) ?></strong></p>
        <hr>
        <p><strong>📅 Date:</strong> <?= $event['date'] ?></p>
        <p><strong>🧑‍🤝‍🧑 Capacity:</strong> <?= $event['capacity'] ?></p>
        <p><strong>📝 Description:</strong> <?= $event['description'] ?></p>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success text-center">✅ Registration successful! A confirmation email has been sent.</div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="attendee_name" class="form-label">Your Name</label>
                <input type="text" id="attendee_name" name="attendee_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="attendee_email" class="form-label">Your Email</label>
                <input type="email" id="attendee_email" name="attendee_email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register Now</button>
        </form>

        <a href="events_public.php" class="btn btn-secondary w-100 mt-3">⬅ Back to Events</a>
    </div>
</div>

</body>
</html>

