<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Query the database to check the user's status
$sql = "SELECT status FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

// Fetch the result
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If the user is blocked, redirect them to a different page (e.g., login or restricted access page)
if ($user && $user['status'] == 'blocked') {
    header('Location: /php-108/library-system/borrower/restricted.php');
    die();
}

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

// Use the scalar function to fetch the total number of approved books for the user
$sql_approved_books = "SELECT get_total_approved_books(:borrower_id) AS total_approved";
$stmt_approved_books = $conn->prepare($sql_approved_books);
$stmt_approved_books->bindParam(':borrower_id', $user_id, PDO::PARAM_INT);
$stmt_approved_books->execute();
$approved_books_count = $stmt_approved_books->fetchColumn();

echo "Total Approved Books: " . $approved_books_count;

// Fetch borrower transactions (REGULAR VIEW)
$sql_transactions = "SELECT * FROM BorrowerTransactions WHERE borrower_id = :borrower_id";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

        /* Adjust content when sidebar is visible */
        .content {
            margin-left: 150px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        /* Navbar Styles */
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
        <a class="navbar-brand" href="#">Borrower</a>
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
        <a href="view_books.php" class="nav-item"><i class="bi bi-book"></i> Books</a>
        <a href="borrow_book.php" class="nav-item"><i class="bi bi-cash-stack"></i> Borrow Records</a>
    </nav>
</div>

<!-- Toggle Button -->
<button class="toggle-btn d-lg-none" id="sidebar-toggle">&#9776;</button>

    <!-- Main Content -->
<div class="content">
    <div class="content mt-5">
        <h1>My Borrowed Books</h1>

        <!-- Display total approved borrowed books -->
        <p><strong>Total of Currently Borrowed Books: </strong><?php echo $approved_books_count; ?></p>
     <div class="table-wrapper">
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
