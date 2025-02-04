<?php
include '../db.php';
session_start();

// Check if user is an admin
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Retrieve activity logs with optional filtering
$filter = '';
if (!empty($_GET['action'])) {
    $filter = "WHERE logs.action = :action";
}
$sql = "SELECT logs.log_id, users.username, logs.action, logs.table_name, logs.timestamp 
        FROM logs 
        JOIN users ON logs.user_id = users.user_id 
        $filter
        ORDER BY logs.timestamp DESC";

$stmt = $conn->prepare($sql);
if (!empty($_GET['action'])) {
    $stmt->bindParam(':action', $_GET['action'], PDO::PARAM_STR);
}
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            position: fixed;
            top: 50px; /* Adjust this value to move the sidebar lower */
            left: 0;
            background-color: #343a40;
            width: 250px;
            padding-top: 20px;
            transition: 0.3s;
        }

        .sidebar a {
            color: white;
            padding: 10px 15px;
            font-size: 18px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar .nav-item {
            margin: 5px 0;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .navbar {
            background-color: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar .dropdown-menu {
            background-color: #343a40;
        }

        .navbar .dropdown-item {
            color: white;
        }

        .navbar .dropdown-item:hover {
            background-color: #495057;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Table Styles */
        .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #ddd;
        }

        .table thead {
            background-color: #343a40;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .table td {
            word-wrap: break-word;
        }

        .table .text-center {
            text-align: center;
        }

        .table .text-danger {
            color: #dc3545;
        }

        /* Scrollable Table Wrapper */
        .table-wrapper {
            max-height: 400px; /* Adjust height as needed */
            overflow-y: auto;
            border: 1px solid #ddd;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.active {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            .toggle-btn {
                display: block;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1050;
                background-color: #343a40;
                color: white;
                border: none;
                padding: 10px 15px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin</a>
        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item text-white" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div class="sidebar">
    <nav>
        <a href="dashboard.php" class="nav-item"><i class="bi bi-house-door"></i> Dashboard</a>
        <a href="manage_books.php" class="nav-item"><i class="bi bi-book"></i> Manage Books</a>
        <a href="activity_logs.php" class="nav-item"><i class="bi bi-file-earmark-text"></i> View Logs</a>
        <a href="reports.php" class="nav-item"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
</div>

<!-- Toggle Button -->
<button class="toggle-btn d-lg-none" id="sidebar-toggle">&#9776;</button>

<!-- Main Content -->
<div class="content">
    <div class="container mt-5">
        <h1>Activity Logs</h1>
        <form method="GET" class="mb-3">
            <label for="action" class="form-label">Filter by Action:</label>
            <select name="action" id="action" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Insert" <?php echo isset($_GET['action']) && $_GET['action'] == 'Insert' ? 'selected' : ''; ?>>Insert</option>
                <option value="Update" <?php echo isset($_GET['action']) && $_GET['action'] == 'Update' ? 'selected' : ''; ?>>Update</option>
                <option value="Delete" <?php echo isset($_GET['action']) && $_GET['action'] == 'Delete' ? 'selected' : ''; ?>>Delete</option>
            </select>
        </form>

        <!-- Table Wrapper -->
        <div class="table-wrapper">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['username']); ?></td>
                                <td><?php echo htmlspecialchars($log['action']); ?></td>
                                <td><?php echo htmlspecialchars($log['table_name']); ?></td>
                                <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No logs found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2024 Bookshelf. All rights reserved.</p>
</div>

<!-- Bootstrap JS and Popper.js (necessary for Bootstrap components) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
    // Toggle sidebar visibility on mobile screens
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('#sidebar-toggle');
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });
</script>

</body>
</html>
