<?php
include '../db.php';
session_start();

// Ensure the user is a Librarian
if ($_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

// Approve or Deny Borrow Requests
if (isset($_POST['action'])) {
    $transaction_id = $_POST['transaction_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $sql = "UPDATE transactions SET status = 'Approved' WHERE transaction_id = :transaction_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->execute();

        echo "Request approved!";
    } elseif ($action == 'deny') {
        // Deny the request and mark the book as available
        $sql = "UPDATE transactions SET status = 'Denied' WHERE transaction_id = :transaction_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->execute();

        // Now update the book's availability status to TRUE (available)
        // Get the book_id associated with the transaction
        $sql_book = "SELECT book_id FROM transactions WHERE transaction_id = :transaction_id";
        $stmt_book = $conn->prepare($sql_book);
        $stmt_book->bindParam(':transaction_id', $transaction_id);
        $stmt_book->execute();
        $book = $stmt_book->fetch(PDO::FETCH_ASSOC);

        // Update the book's availability to true
        if ($book) {
            $sql_update_book = "UPDATE books SET is_available = TRUE WHERE book_id = :book_id";
            $stmt_update_book = $conn->prepare($sql_update_book);
            $stmt_update_book->bindParam(':book_id', $book['book_id']);
            $stmt_update_book->execute();
        }

        echo "Request denied and book returned!";
    }
}

// Define the function call
$sql = "SELECT * FROM PendingTransactions()";  // If using a schema like "public", use "public.PendingTransactions()"

// Use try-catch for error handling
try {
    // Execute the query
    $stmt = $conn->query($sql);

    // Check if the query returned any result
    if ($stmt) {
        // Fetch all the results as an associative array
        $pending_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if any results are returned
        if (count($pending_transactions) > 0) {
            // Iterate through the result set
        } else {
            echo "No pending transactions found.";
        }
    } else {
        echo "Error executing query.";
    }
} catch (PDOException $e) {
    // Handle any database errors
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Borrow Requests</title>
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
        <h1>Borrower Requests</h1>
        <div class="table-wrapper">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Borrower</th>
                        <th>Book Title</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_transactions as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['borrower_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="transaction_id" value="<?php echo $request['transaction_id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                <button type="submit" name="action" value="deny" class="btn btn-danger">Deny</button>
                            </form>
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
