<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Preloader Styles */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 9999;
        }

        .book-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .book {
            width: 150px;
            height: 30px;
            background: linear-gradient(to right, #007bff, #0056b3);
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: stackBounce 1.5s ease-in-out infinite;
        }

        .book:nth-child(2) {
            background: linear-gradient(to right, #28a745, #1c7a32);
            animation-delay: 0.2s;
        }

        .book:nth-child(3) {
            background: linear-gradient(to right, #ffc107, #e0a800);
            animation-delay: 0.4s;
        }

        @keyframes stackBounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .preloader-text {
            margin-top: 20px;
            font-size: 1.3rem;
            color: #343a40;
            font-weight: 600;
            animation: fadeIn 1s ease-in-out infinite alternate;
        }

        @keyframes fadeIn {
            from {
                opacity: 0.5;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="book-stack">
            <div class="book"></div>
            <div class="book"></div>
            <div class="book"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const preloader = document.getElementById("preloader");
            // Hide preloader after 3.5 seconds
            setTimeout(() => {
                preloader.style.display = "none";

                // Redirect to login page
                window.location.href = "login.php";
            }, 3500);
        });
    </script>
</body>
</html>
