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

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];


// Handle Add Event Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $capacity = $_POST['capacity'];

    if (!empty($name) && !empty($date) && !empty($capacity)) {
        $stmt = $conn->prepare("INSERT INTO events (name, description, date, capacity, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssii', $name, $description, $date, $capacity, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: events.php?status=success&message=Event added successfully!");
            exit;
        } else {
            $stmt->close();
            header("Location: events.php?status=error&message=Failed to add event!");
            exit;
        }
    } else {
        header("Location: events.php?status=error&message=All fields are required!");
        exit;
    }
}

// Handle Fetching Events for Display
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM events WHERE created_by = ?";

if (!empty($filter_date)) {
    $query .= " AND date = '$filter_date'";
}

if (!empty($search_query)) {
    $query .= " AND name LIKE '%$search_query%'";
}

$query .= " ORDER BY $sort_by LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Get total events count for pagination
$count_query = "SELECT COUNT(*) AS total FROM events WHERE created_by = ?";
if (!empty($filter_date)) {
    $count_query .= " AND date = '$filter_date'";
}
if (!empty($search_query)) {
    $count_query .= " AND name LIKE '%$search_query%'";
}
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param('i', $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_events = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_events / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/events.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="d-flex" id="wrapper">
        <div class="bg-dark text-white sidebar">
            <h2 class="text-center py-3">Events Dashboard</h2>
            <ul class="list-unstyled px-3">
                <li><a href="dashboard.php" class="sidebar-link"><i class="fas fa-home me-2"></i> Dashboard</a></li>
                <li><a href="events.php" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#addEventModal"><i class="fas fa-calendar me-2"></i> Add New Event</a></li>
            </ul>

            <div class="logout-container">
                <a href="dashboard.php" class="btn btn-info w-100">Back</a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container mt-5 content">
            <h1 class="title"><?= htmlspecialchars($username) ?>'s <span class="highlight">Events</span></h1> <br>

            <!-- Search & Filter -->
            <div class="d-flex align-items-center gap-2 mb-4">
                <label for="filterDate" class="me-2">Filter by Date -</label>
                <input type="date" id="filterDate" class="form-control" style="width: 200px;">
                <input type="text" id="searchInput" class="form-control" placeholder="Search events by name" style="width: 250px;">
                <a href="events.php" class="btn btn-danger">Reset</a>
            </div>


            <!-- Search Results Table -->
            <div class="table-responsive" id="searchResults">
                <!-- Results will be loaded here -->
            </div>

            <!-- Add Event Modal -->
            <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Event Name</label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="date" class="form-label">Event Date</label>
                                    <input type="date" id="date" name="date" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity</label>
                                    <input type="number" id="capacity" name="capacity" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="add_event" class="btn btn-primary">Add Event</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Event Modal -->
            <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content bg-dark text-white">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                        </div>
                        <div class="modal-body" id="eventModalBody">
                            <!-- Event details will be dynamically loaded here -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Event Modal -->
            <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="editEventForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="editEventId" name="id"> 
                                <div class="mb-3">
                                    <label for="editName" class="form-label">Event Name</label>
                                    <input type="text" class="form-control" id="editName" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="editDate" class="form-label">Event Date</label>
                                    <input type="date" class="form-control" id="editDate" name="date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editCapacity" class="form-label">Capacity</label>
                                    <input type="number" class="form-control" id="editCapacity" name="capacity" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="update_event" class="btn btn-primary">Update Event</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/scripts/event_search.js"></script>
<script>
    // Fetch initial events on page load
    document.addEventListener("DOMContentLoaded", () => {
        loadEvents(""); 
    });
</script>

</body>
</html>
