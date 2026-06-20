<?php
    include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');
    session_start(); // Start the session

// Initialize error and success messages
$error_message = '';
$success_message = '';

// Check if user is logged in and allowed to reset password
if (!isset($_SESSION['otp_email'])) {
    // If the session does not have 'otp_email', redirect to login page
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $new_password = $_POST['reset_pw'];
    $confirm_password = $_POST['confirm_reset_pw'];
    $email = $_SESSION['otp_email']; // Use the email from the session

    // Validate password requirements
    $lengthValid = strlen($new_password) >= 8;
    $capitalValid = preg_match('/[A-Z]/', $new_password);
    $numberValid = preg_match('/[0-9]/', $new_password);
    $specialValid = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password);

    // Check if all password requirements are met
    if ($lengthValid && $capitalValid && $numberValid && $specialValid) {
        // Check if passwords match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $sql = "UPDATE admin_acc SET admin_passw='$hashed_password' WHERE admin_email='$email'";

            if (mysqli_query($conn, $sql)) {
                // Clear the OTP session variables after successful reset
                unset($_SESSION['otp']);
                unset($_SESSION['otp_email']);
                $success_message = 'Password reset successfully!';
            } else {
                $error_message = 'Error updating password: ' . mysqli_error($conn);
            }
        } else {
            $error_message = 'Passwords do not match. Please try again.';
        }
    } else {
        $error_message = 'Please make sure your new password meets all the requirements.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/backend/css/admin_resetpass.css">

    <title>Reset Password</title>
    <style>
        .body{
            background-image: url("C:\\xampp\\htdocs\\TinyTotsPrintables\\backend\\images\\bg.png");
        }
        .requirements {
            display: none;
            list-style-type: none;
            padding: 0;
        }
        .requirements li {
            color: red;
        }
        .requirements li.valid {
            color: green;
        }
        .error-message {
            color: red;
            font-weight: bold;
            display: <?= !empty($error_message) ? 'block' : 'none'; ?>;
        }
        .success-message {
            text-align: center;
            color: green;
            font-weight: bold;
            display: <?= !empty($success_message) ? 'block' : 'none'; ?>;
        }
        .additional-message {
            color: #000000;
            font-weight: bold;
            display: <?= !empty($success_message) ? 'block' : 'none'; ?>;
        }
        /* Ensure links are styled to be clickable */
        .additional-message a {
            text-decoration: none;
        }
        .additional-message a:hover {
            text-decoration: none;
        }
        
    </style>
</head>
<body>
    <div class="logo">
        <img src="http://localhost/TinyTotsPrintables/backend/images/logo.png" id="imglogo" alt="logo" >
    </div>
    <br>
    <div class="container">
        <div class="form-container">
            <form action="" method="POST" oninput="checkPasswordMatch()" onsubmit="return checkRequirements()">
                <div class="admin-title-container">
                    <h3>RESET PASSWORD</h3>
                    <?php if (!empty($success_message)): ?>
                        <div id="additional-message" class="additional-message">
                            You can now <a href="admin_login.php" id="LoginA">login</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="inputs-container">
                    <label for="admin-resetpass">New Password</label>
                    <div class="resetpass-container">
                        <span class="password-lock-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="reset_pw" name="reset_pw" placeholder="Enter New Password" onfocus="showRequirements()" onkeyup="validatePassword()" required> 
                        <span class="pw-signup-icon" style="display: none;"><i class="fas fa-eye"></i></span>
                    </div>
                    <label for="admin-confirmreset-pass">Confirm New Password</label>
                    <div class="resetconfirmpass-container">
                        <span class="password-lock-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="confirm_reset_pw" name="confirm_reset_pw" placeholder="Confirm New Password" required> 
                        <span class="cwp-signup-icon" style="display: none;"><i class="fas fa-eye"></i></span>
                    </div>

                    <div id="error-message" class="error-message">
                        <?= htmlspecialchars($error_message); ?>
                    </div>

                    <div id="password-mismatch" style="color: red; display: none;">
                        Passwords do not match.
                    </div>

                    <ul id="requirements" class="requirements">
                        <li id="length"> Must be at least 8 characters long</li>
                        <li id="capital"> Must have at least one capital letter</li>
                        <li id="number"> Must have at least one number</li>
                        <li id="special"> Must have at least one special character</li>
                    </ul>

                    <?php if (!empty($success_message)): ?>
                        <div id="success-message" class="success-message">
                            <?= htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <button id="btnresetpass" type="submit">Reset Password</button>          
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRequirements() {
            document.getElementById("requirements").style.display = "block";
            document.getElementById("error-message").style.display = "none";
        }

        function validatePassword() {
            var password = document.getElementById("reset_pw").value;
            var lengthRequirement = document.getElementById("length");
            var capitalRequirement = document.getElementById("capital");
            var numberRequirement = document.getElementById("number");
            var specialRequirement = document.getElementById("special");

            // Check if password meets requirements
            lengthRequirement.classList.toggle("valid", password.length >= 8);
            capitalRequirement.classList.toggle("valid", /[A-Z]/.test(password));
            numberRequirement.classList.toggle("valid", /[0-9]/.test(password));
            specialRequirement.classList.toggle("valid", /[!@#$%^&*(),.?":{}|<>]/.test(password));
        }

        function checkPasswordMatch() {
            var password = document.getElementById("reset_pw").value;
            var confirmPassword = document.getElementById("confirm_reset_pw").value;
            var mismatchMessage = document.getElementById("password-mismatch");

            if (password !== confirmPassword) {
                mismatchMessage.style.display = "block";
            } else {
                mismatchMessage.style.display = "none";
            }
        }

        function checkRequirements() {
            var password = document.getElementById("reset_pw").value;
            var lengthValid = password.length >= 8;
            var capitalValid = /[A-Z]/.test(password);
            var numberValid = /[0-9]/.test(password);
            var specialValid = /[!@#$%^&*(),.?":{}|<>]/.test(password);

            // Show error message if requirements are not met
            var errorMessage = document.getElementById("error-message");
            if (lengthValid && capitalValid && numberValid && specialValid) {
                errorMessage.style.display = "none";
                return true;
            } else {
                errorMessage.style.display = "block";
                return false;
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const passwordField = document.getElementById("reset_pw");
            const confirmPasswordField = document.getElementById("confirm_reset_pw");

            const passwordToggleIconContainer = document.querySelector(".resetpass-container .pw-signup-icon");
            const confirmPasswordToggleIconContainer = document.querySelector(".resetconfirmpass-container .cwp-signup-icon");

            // Ensure toggle icons display correctly when typing
            passwordField.addEventListener("input", function () {
                if (passwordField.value.length > 0) {
                    passwordToggleIconContainer.style.display = "inline";
                } else {
                    passwordToggleIconContainer.style.display = "none";
                }
            });

            confirmPasswordField.addEventListener("input", function () {
                if (confirmPasswordField.value.length > 0) {
                    confirmPasswordToggleIconContainer.style.display = "inline";
                } else {
                    confirmPasswordToggleIconContainer.style.display = "none";
                }
            });

            // Password field toggle functionality
            passwordToggleIconContainer.addEventListener("click", function () {
                const icon = passwordToggleIconContainer.querySelector("i");
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    passwordField.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            });

            // Confirm Password field toggle functionality
            confirmPasswordToggleIconContainer.addEventListener("click", function () {
                const icon = confirmPasswordToggleIconContainer.querySelector("i");
                if (confirmPasswordField.type === "password") {
                    confirmPasswordField.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    confirmPasswordField.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            });
        });
    </script>
</body>
</html>