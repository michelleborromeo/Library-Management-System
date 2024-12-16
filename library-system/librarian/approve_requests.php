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
        $sql = "UPDATE transactions SET status = 'Denied' WHERE transaction_id = :transaction_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->execute();

        echo "Request denied!";
    }
}

$sql = "SELECT transactions.transaction_id, books.title AS book_title, CONCAT(users.first_name, ' ', users.last_name) AS borrower_name, transactions.status 
        FROM transactions 
        JOIN books ON transactions.book_id = books.book_id 
        JOIN users ON transactions.borrower_id = users.user_id 
        WHERE transactions.status = 'Pending'";

$stmt = $conn->query($sql);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Borrow Requests</title>
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
        <h2 class="text-white text-center">Librarian Dashboard</h2>
        <a href="add_book.php">Add Book</a>
        <a href="edit_book.php">Edit Book</a>
        <a href="delete_book.php">Delete Book</a>
        <a href="approve_requests.php">Approve Requests</a>
        <a href="reports.php">Reports</a>
        <a href="borrower_records.php">Borrower Records</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Toggle Button for Sidebar (on mobile) -->
    <div class="toggle-btn d-none d-md-block">
        <span class="text-white" id="sidebar-toggle">&#9776; Menu</span>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Approve Borrow Requests</h1>
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
                <?php foreach ($requests as $request): ?>
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
