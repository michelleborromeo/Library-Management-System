<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT username AS borrower_name, title AS book_title, borrow_date, transactions.return_date, transactions.status 
        FROM transactions 
        JOIN users ON transactions.borrower_id = users.user_id 
        JOIN books ON transactions.book_id = books.book_id
        ORDER BY transactions.borrow_date DESC";

$stmt = $conn->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Records</title>
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
            transition: 0.3s;
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
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: 0.3s;
        }
        .sidebar.active {
            left: -250px;
        }
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            .content {
                margin-left: 0;
            }
            .sidebar.active {
                left: 0;
            }
        }
        .toggle-btn {
            display: none;
            background-color: #343a40;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .toggle-btn {
                display: block;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 200;
            }
        }
        /* Table responsiveness */
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Toggle Button for Sidebar -->
    <div class="toggle-btn" id="sidebar-toggle">
        &#9776; Menu
    </div>

    <!-- Main Content -->
    <div class="content container mt-5">
        <h1>Borrower Records</h1>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Borrower</th>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['borrower_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($record['borrow_date']); ?></td>
                        <td><?php echo htmlspecialchars($record['return_date'] ?? 'Not Returned'); ?></td>
                        <td><?php echo htmlspecialchars($record['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        // Toggle sidebar visibility
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('#sidebar-toggle');
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    </script>
</body>
</html>