<?php
session_start();
if ($_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sidebar styles */
        /* Sidebar styles */
.sidebar {
    height: 100%;
    position: fixed;
    top: 20px; /* Adjust this value to move the sidebar down */
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

        /* Navbar styles */
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

        /* Adjusting content area */
       /* Adjusting content area */
.content {
    margin-left: 400px; /* Increase the margin to create more space from the sidebar */
    padding: 20px;
    transition: margin-left 0.3s;
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

        /* Card styles */
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Librarian Dashboard</a>
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Librarian
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
            <a href="librarian_dashboard.php" class="nav-item"><i class="bi bi-house-door"></i> Dashboard</a>
            <a href="approve_requests.php" class="nav-item"><i class="bi bi-book"></i> Borrower Request</a>
            <a href="borrower_records.php" class="nav-item"><i class="bi bi-person-bounding-box"></i> Borrower Records</a>
            <a href="reports.php" class="nav-item"><i class="bi bi-bar-chart-line"></i> View Reports</a>
        </nav>
    </div>

    <!-- Toggle Button for Sidebar (on mobile) -->
    <div class="toggle-btn d-md-none">
        <span class="text-white" id="sidebar-toggle">&#9776; Menu</span>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Welcome, Librarian!</h1>
        <p class="lead">You can manage book inventories and assist borrowers here.</p>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Books</h5>
                        <p class="card-text"><i class="bi bi-book display-4"></i></p>
                        <a href="approve_requests.php" class="btn btn-primary">Request</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Borrowers</h5>
                        <p class="card-text"><i class="bi bi-person-bounding-box display-4"></i></p>
                        <a href="borrower_records.php" class="btn btn-secondary">Records</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Reports</h5>
                        <p class="card-text"><i class="bi bi-bar-chart-line display-4"></i></p>
                        <a href="reports.php" class="btn btn-info">View</a>
                    </div>
                </div>
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
