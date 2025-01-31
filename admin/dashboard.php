<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Display user information
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <h3 class="text-center py-3">Dashboard</h3>
            <ul>
                <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="events.php"><i class="fa fa-calendar"></i> Manage Events</a></li>
                <li><a href="battendee_list.php"><i class="fa fa-users"></i> View Attendees</a></li>
                
                <div class="sidebar-bottom-btn">
                    <li><a href="../logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
                </div>
            </ul>
        </nav>

        <!-- Main Content -->
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar shadow-sm">
            <h1 class="title">Welcome <span class="highlight"><?= htmlspecialchars($username) ?>!</span></h1>
            </nav>

            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <div class="card">
                    <div class="card-header">Total Events</div>
                    <div class="card-body">
                        <p id="totalEvents">Loading...</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Total Attendees</div>
                    <div class="card-body">
                        <p id="totalAttendees">Loading...</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Total Capacity</div>
                    <div class="card-body">
                        <p id="totalCapacity">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Completed and Upcoming Events -->
            <div class="dashboard-stats">
                <div class="card">
                    <div class="card-header">Completed Events</div>
                    <ul class="events-list" id="completedEvents">
                        <li>Loading...</li>
                    </ul>
                    <div class="pagination" id="completedPagination"></div>
                </div>
                <div class="card">
                    <div class="card-header">Upcoming Events</div>
                    <ul class="events-list" id="upcomingEvents">
                        <li>Loading...</li>
                    </ul>
                    <div class="pagination" id="upcomingPagination"></div>
                </div>
            </div>

            <!-- Events Today -->
            <div class="dashboard-stats">
                <div class="card">
                    <div class="card-header">Events Today</div>
                    <div class="card-body" id="eventsToday">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/scripts/dashboard.js"></script>
</body>
</html>




