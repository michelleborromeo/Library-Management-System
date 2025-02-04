<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Total users
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetchColumn();

// Total books
$total_books = $conn->query("SELECT COUNT(*) AS count FROM books")->fetchColumn();

// Total transactions
$total_transactions = $conn->query("SELECT COUNT(*) AS count FROM transactions")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            position: fixed;
            top: 50px;
            left: 0;
            background-color: #343a40;
            width: 250px;
            padding-top: 20px;
            transition: left 0.3s ease;
        }

        .sidebar a {
            color: white;
            padding: 12px 20px;
            font-size: 18px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar .nav-item {
            margin: 8px 0;
        }

        .content {
            margin-left: 400px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .navbar {
            background-color: #343a40;
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

        /* Card Styles */
        .card {
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: #495057;
            color: white;
            font-weight: bold;
        }

        .card-body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        .card-title {
            font-size: 2rem;
            font-weight: bold;
            color: #495057;
        }

        /* Toggle Button */
        .toggle-btn {
            display: none;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
                transition: left 0.3s ease;
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
        <h1 class="mb-4">Advanced Reports</h1>
        <div class="row">
            <!-- Total Users Card -->
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-header text-center">
                        Total Users
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $total_users; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Total Books Card -->
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-header text-center">
                        Total Books
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $total_books; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Total Transactions Card -->
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-header text-center">
                        Total Transactions
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $total_transactions; ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js (necessary for Bootstrap components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Bookshelf. All rights reserved.</p>
    </div>

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
