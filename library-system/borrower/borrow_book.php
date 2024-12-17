<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $borrow_date = $_POST['borrow_date'];
    $return_date = $_POST['return_date'];

    // Insert into transactions table
    $sql = "INSERT INTO transactions (book_id, borrower_id, borrow_date, return_date, status) 
            VALUES (:book_id, :borrower_id, :borrow_date, :return_date, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->bindParam(':borrower_id', $user_id);
    $stmt->bindParam(':borrow_date', $borrow_date);
    $stmt->bindParam(':return_date', $return_date);
    $stmt->execute();

    // Update book availability
    $update_sql = "UPDATE books SET is_available = FALSE WHERE book_id = :book_id";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':book_id', $book_id);
    $update_stmt->execute();

    echo "<script>
            alert('Borrow request submitted successfully!');
            window.location.href = 'borrow_book.php';
          </script>";
    exit();
}

// Fetch borrower transactions
$sql_transactions = "SELECT t.transaction_id, b.title, t.borrow_date, t.return_date, t.status 
                     FROM transactions t
                     JOIN books b ON t.book_id = b.book_id
                     WHERE t.borrower_id = :borrower_id
                     ORDER BY t.borrow_date DESC";
$stmt_transactions = $conn->prepare($sql_transactions);
$stmt_transactions->bindParam(':borrower_id', $user_id);
$stmt_transactions->execute();
$transactions = $stmt_transactions->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
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
                font-size: 18px;
                color: white;
                background: #343a40;
                padding: 10px;
                cursor: pointer;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 101;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="text-white text-center">Borrower</h2>
        <a href="view_books.php">Books</a>
        <a href="borrow_book.php">Borrowed Books</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Toggle Button for Sidebar (on mobile) -->
    <div class="toggle-btn d-md-none" id="sidebar-toggle">
        &#9776;
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>My Borrowed Books</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['title']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['borrow_date']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($transaction['return_date']) ? htmlspecialchars($transaction['return_date']) : 'Not Set'; ?>
                    </td>
                    <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar visibility on mobile screens
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('#sidebar-toggle');
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    </script>
</body>
</html>
