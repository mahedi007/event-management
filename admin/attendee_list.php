<?php
require '../config/db_connection.php';

// Get event ID from URL
if (!isset($_GET['event_id'])) {
    die("Event ID is required.");
}

$event_id = (int)$_GET['event_id'];

// Fetch event details
$sql_event = "SELECT name AS event_name FROM events WHERE id = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$result_event = $stmt_event->get_result();
$event = $result_event->fetch_assoc();
$stmt_event->close();

// Fetch all attendees for initial page load
$sql_attendees = "SELECT id, name, email FROM attendees WHERE event_id = ?";
$stmt_attendees = $conn->prepare($sql_attendees);
$stmt_attendees->bind_param("i", $event_id);
$stmt_attendees->execute();
$result_attendees = $stmt_attendees->get_result();
$attendees = $result_attendees->fetch_all(MYSQLI_ASSOC);
$stmt_attendees->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendee List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
        <h1 style="
            font-size: 2rem; 
            font-weight: bold; 
            text-align: center; 
            letter-spacing: 1.5px; 
            color: black; 
            padding-bottom:50px;">
            Attendee List for Event: 
            <span style="color: #ff9800; font-weight: bold; text-decoration: underline;">
                <?= htmlspecialchars($event['event_name'] ?? 'Unknown Event') ?>
            </span>
        </h1>

    <a href="events.php" class="btn btn-secondary mb-3">Back to Events</a>
    <a href="export_attendees.php?event_id=<?= $event_id; ?>" class="btn btn-success mb-3">Export Attendee List to CSV</a>

    <!-- Search Input -->
    <input type="text" id="searchAttendees" class="form-control mb-3" placeholder="Search attendees..." data-event-id="<?= $event_id ?>">

    <!-- Attendee Table -->
    <div id="attendeeTableContainer">
        <table class="table table-bordered table-striped">
        <thead style="background-color: #ff9800; color: white;">
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="attendeeTable">
                <?php foreach ($attendees as $attendee): ?>
                    <tr>
                        <td><?= htmlspecialchars($attendee['id']) ?></td>
                        <td><?= htmlspecialchars($attendee['name']) ?></td>
                        <td><?= htmlspecialchars($attendee['email']) ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button 
                                class="btn btn-sm btn-warning edit-btn"
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal"
                                data-id="<?= $attendee['id'] ?>"
                                data-name="<?= htmlspecialchars($attendee['name']) ?>"
                                data-email="<?= htmlspecialchars($attendee['email']) ?>">
                                Edit
                            </button>

                            <!-- Delete Button -->
                            <a href="delete_attendee.php?attendee_id=<?= $attendee['id'] ?>&event_id=<?= $event_id ?>" 
                            class="btn btn-sm btn-danger remove-btn">
                                Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST" action="update_attendee.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Attendee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="attendee_id" id="attendee_id">
                    <input type="hidden" name="event_id" value="<?= $event_id ?>">

                    <div class="mb-3">
                        <label for="attendee_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="attendee_name" name="attendee_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="attendee_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="attendee_email" name="attendee_email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/scripts/attendee_search.js"></script>
<script src="../assets/scripts/modal_script.js"></script>
</body>
</html>
