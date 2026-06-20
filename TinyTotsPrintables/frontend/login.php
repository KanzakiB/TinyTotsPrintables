<?php
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

$email_error = false;
$password_error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['button_login']) && $_POST['button_login'] == 'signin') {
        $customer_email = $_POST['customer_email'];
        $customer_pw = $_POST['customer_passw'];

        $check_email_sql = "SELECT * FROM customer WHERE email='$customer_email'";
        $check_email_result = $conn->query($check_email_sql);

        if ($check_email_result->num_rows > 0) {
            $row = $check_email_result->fetch_assoc();

            $stored_passw = $row['passw'];

            if (password_verify($customer_pw, $stored_passw)) {
                session_start();
                $_SESSION['customer_id'] = $row['customer_id'];
                $_SESSION['customer_email'] = $customer_email;

                header("Location: shopnow.php"); 
                exit();
            } else {
                $password_error = true;
            }
        } else {
            $email_error = true; 
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Fredoka", sans-serif;
        }
        .sticky-header {
            position: sticky;
            z-index: 1020;
            background-color: #ffffff; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-bottom: 5px solid #8900c9; 
        }
        .nav-link {
            color:rgb(0, 0, 0);
            font-weight: bold;
            margin: 0 10px;
        }
        .nav-link:hover {
            color: #a86dff;
        }
    </style>

    <title>Login</title>
</head>
<body>
    <!-- Header -->
    <header class="sticky-header py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="logo" onclick="window.location.href='homepage.php';" style="cursor: pointer;">
                <img src="http://localhost/TinyTotsPrintables/frontend/images/navbarlogo.png" alt="TinyTots Printables Logo" class="img-fluid" style="max-height: 50px;">
            </h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="homepage.php #home" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="homepage.php #services" class="nav-link">Services</a></li>
                    <li class="nav-item"><a href="homepage.php #learningbenefits" class="nav-link">Learning Benefits</a></li>
                    <li class="nav-item"><a href="homepage.php #whyus" class="nav-link">Why Us</a></li>
                    <li class="nav-item"><a href="homepage.php #contactus" class="nav-link">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <br>
    <div class="container-user">
        <div class="form-container">
            <div class="user-title-container">
                <a id="adminSignup" href="signup.php">Not Yet Registered?<span class="signupA"> Sign up</span></a><br><br>
            </div>
            <form action="" method="POST">
                <div class="inputs-container">

                    <label for="admin-email">Email</label>
                    <div class="email-container">
                        <span class="envelope-icon"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="customer_email" placeholder="Enter Email" required> <br>
                    </div>
                    <p class="error-message1" style="color: red; <?= $email_error ? 'display: block;' : 'display: none;' ?>">Invalid email address</p>
                    <br>
                    <label for="admin-password">Password</label>
                    <div class="password-container">
                        <span class="lock-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="login_password" name="customer_passw" placeholder="Enter Password" required>
                        <span class="password-toggle-icon" style="display:none;"><i class="fas fa-eye"></i></span>
                    </div>
                    <p class="error-message2" style="color: red; <?= $password_error ? 'display: block;' : 'display: none;' ?>">Incorrect password</p>

                    <a id="Forgot_pw" href="admin_forgot_password.php">Forgot password?</a> 
                    <button type="submit" class="btn-admin-login" name="button_login" value="signin">Login</button>

                </div>
            </form>
        </div>
    </div>
    <script>
        const passwordField = document.getElementById("login_password");
        const togglePassword = document.querySelector(".password-toggle-icon i");
        const toggleIconContainer = document.querySelector(".password-toggle-icon");

        // Show the eye icon when the user starts typing 
        passwordField.addEventListener("input", function () {
            if (passwordField.value.length > 0) {
                toggleIconContainer.style.display = "inline";
            } else {
                toggleIconContainer.style.display = "none";
            }
        });

        passwordField.addEventListener("focus", function () {
            if (passwordField.value.length > 0) {
                toggleIconContainer.style.display = "inline";
            }
        });

        passwordField.addEventListener("blur", function () {
            if (passwordField.value.length === 0) {
                toggleIconContainer.style.display = "none";
            }
        });

        togglePassword.addEventListener("click", function () {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.classList.remove("fa-eye");
                togglePassword.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                togglePassword.classList.remove("fa-eye-slash");
                togglePassword.classList.add("fa-eye");
            }
        });
    </script>
</body>
</html>