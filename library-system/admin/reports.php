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
    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: #343a40;
            padding-top: 20px;
            width: 250px;
        }
        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #575d63;
        }
        /* Adjusting content area */
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        /* Make the sidebar responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                top: 0;
                left: -250px;
                width: 250px;
                transition: 0.3s;
            }
            .sidebar.active {
                left: 0;
            }
            .content {
                margin-left: 0;
            }
            .sidebar a {
                font-size: 16px;
            }
            .toggle-btn {
                display: block;
                font-size: 18px;
                color: white;
                background: #343a40;
                padding: 10px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="text-white text-center">Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_book.php">Add Book</a>
        <a href="edit_book.php">Edit Book</a>
        <a href="delete_book.php">Delete Book</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="reports.php">Reports</a>
        <a href="/php-108/library-system/logout.php" class="btn btn-danger">Logout</a>
    </div>
    
    <!-- Toggle Button for Sidebar (on mobile) -->
    <div class="toggle-btn d-none d-md-block">
        <span class="text-white" id="sidebar-toggle">&#9776; Menu</span>
    </div>

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
