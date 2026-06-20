<?php
// Connection to the database
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if (isset($_POST['button_login']) && $_POST['button_login'] == 'signin') 
    {
        $admin_email = $_POST['admin_email'];
        $admin_pw = $_POST['admin_passw'];

        // Check if the admin email exists in the admin_account table
        $check_email_sql = "SELECT * FROM admin_acc WHERE admin_email='$admin_email'";
        $check_email_result = $conn->query($check_email_sql);

        if ($check_email_result->num_rows > 0) {
            $row = $check_email_result->fetch_assoc();

            // Retrieve the stored password
            $stored_passw = $row['admin_passw'];

            // If the stored password is a bcrypt hash (length 60), use password_verify
            if (strlen($stored_passw) === 60 && password_verify($admin_pw, $stored_passw)) {
                // Start the session and store admin details
                session_start();
                $_SESSION['adminID'] = $row['adminID'];
                $_SESSION['admin_email'] = $admin_email;
                header("Location: dashboard.php?adminID=" . $row['adminID']);
                exit();
            } 
            // If the stored password is plain text (not recommended for production)
            elseif ($admin_pw === $stored_passw) {
                // Start the session and store admin details
                session_start();
                $_SESSION['adminID'] = $row['adminID'];
                $_SESSION['admin_email'] = $login_email;
                header("Location: dashboard.php?adminID=" . $row['adminID']);
                exit();
            } else {
                echo "<p style='color:red;'>Invalid email or password.</p>";
            }
        } else {
            echo "<p style='color:red;'>Admin email does not exist.</p>";
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
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/scrollbar.css">
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/backend/css/admin_login.css">

    <title>Admin Login</title>
</head>
<body>
    <div class="logo">
        <img src="http://localhost/TinyTotsPrintables/backend/images/logo.png" id="imglogo" alt="logo" >
    </div>
    <br>
    <div class="container">
        <div class="form-container">
            <div class="admin-title-container">
                <h3>ADMIN LOGIN</h3>
                <a id="adminSignup" href="admin_signup.php">Not Yet Registered?<span class="signupA"> Sign up</span></a><br><br>
            </div>
            <form action="" method="POST">
                <div class="inputs-container">

                    <label for="admin-email">Email</label>
                    <div class="email-container">
                        <span class="envelope-icon"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="admin_email" placeholder="Enter Admin Email" required> <br>
                    </div>
                    <br>
                    <label for="admin-password">Password</label>
                    <div class="password-container">
                        <span class="lock-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="login_password" name="admin_passw" placeholder="Enter Admin Password" required>
                        <span class="password-toggle-icon" style="display:none;"><i class="fas fa-eye"></i></span>

                    </div>

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