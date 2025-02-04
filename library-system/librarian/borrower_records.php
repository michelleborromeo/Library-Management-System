<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

// Fetch records (REGULAR VIEW)
$sql = "SELECT * FROM TransactionsView";  


$stmt = $conn->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle "Returned" button action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_returned'])) {
    $transaction_id = $_POST['transaction_id'];

    // Step 1: Get the associated book_id for the transaction
    $book_sql = "SELECT book_id FROM transactions WHERE transaction_id = :transaction_id";
    $book_stmt = $conn->prepare($book_sql);
    $book_stmt->execute(['transaction_id' => $transaction_id]);
    $book = $book_stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        $book_id = $book['book_id'];

        // Step 2: Update the transactions table to mark as returned
        $update_sql = "UPDATE transactions SET status = 'Returned', return_date = NOW() WHERE transaction_id = :transaction_id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute(['transaction_id' => $transaction_id]);

        // Step 3: Update the books table to set is_available to TRUE
        $update_book_sql = "UPDATE books SET is_available = TRUE WHERE book_id = :book_id";
        $update_book_stmt = $conn->prepare($update_book_sql);
        $update_book_stmt->execute(['book_id' => $book_id]);
    }

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Records</title>
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
            padding: 50px;
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
        <a class="navbar-brand" href="#">Librarian</a>
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
        <a href="librarian_dashboard.php" class="nav-item"><i class="bi bi-house-door"></i> Dashboard</a>
        <a href="approve_requests.php" class="nav-item"><i class="bi bi-book"></i> Borrower Request</a>
        <a href="borrower_records.php" class="nav-item"><i class="bi bi-person-bounding-box"></i> Borrower Records</a>
        <a href="reports.php" class="nav-item"><i class="bi bi-bar-chart-line"></i> View Reports</a>
    </nav>
</div>

<!-- Toggle Button -->
<button class="toggle-btn d-lg-none" id="sidebar-toggle">&#9776;</button>

    <!-- Main Content -->
    <div class="content container mt-5">
        <h1>Borrower Records</h1>
        <div class="table-wrapper">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Borrower</th>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Action</th>
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
                        <td>
                        <?php if ($record['status'] == 'Approved'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="transaction_id" value="<?php echo $record['transaction_id']; ?>">
                                    <button type="submit" name="mark_returned" class="btn btn-success btn-sm">Mark as Returned</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Already Returned</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
