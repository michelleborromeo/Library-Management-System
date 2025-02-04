<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT users.user_id, users.username, users.status, roles.role_name 
        FROM users 
        JOIN roles ON users.role_id = roles.role_id
        WHERE roles.role_name != 'Admin'";
$stmt = $conn->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px; 
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
                transition: 0.3s;
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
        <h1>Manage Users</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                    <td>
                        <?php if ($user['status'] == 'active'): ?>
                            <a href="block_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger">Block</a>
                        <?php else: ?>
                            <a href="unblock_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-success">Unblock</a>
                        <?php endif; ?>
                        <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
