<?php
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

$error_message = '';
$success_message = '';
$signup_email = '';

$default_profile_pic_path = 'C:/XAMPP/htdocs/TinyTotsPrintables/frontend/images/default_profile_pic.png';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $signup_email = $_POST['signup_email'];
    $signup_pw = $_POST['signup_pw'];
    $confirm_signup_pw = $_POST['confirm_signup_pw'];

    if ($signup_pw !== $confirm_signup_pw) {
        $error_message = 'Passwords do not match';
    } else {
        $lengthValid = strlen($signup_pw) >= 8;
        $capitalValid = preg_match('/[A-Z]/', $signup_pw);
        $numberValid = preg_match('/[0-9]/', $signup_pw);
        $specialValid = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $signup_pw);

        if ($lengthValid && $capitalValid && $numberValid && $specialValid) {
            $email_check_sql = "SELECT * FROM customer WHERE email='$signup_email'";
            $email_check_result = mysqli_query($conn, $email_check_sql);

            if (mysqli_num_rows($email_check_result) > 0) {
                $error_message = 'Email already exists';
            } else {
                $hashed_pw = password_hash($signup_pw, PASSWORD_DEFAULT);

                $profile_pic_data = file_get_contents($default_profile_pic_path);

                $sql = "INSERT INTO customer (email, passw, profile_pic) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssb", $signup_email, $hashed_pw, $profile_pic_data);
                
                $blob = fopen($default_profile_pic_path, 'rb');
                mysqli_stmt_send_long_data($stmt, 2, fread($blob, filesize($default_profile_pic_path)));
                fclose($blob);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = 'Signup successful!';

                    header("Location: login.php");
                    exit(); 
                } else {
                    $error_message = 'Error: ' . mysqli_error($conn);
                }
            }
        } else {
            $error_message = 'Please meet all the requirements.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/signup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <title>Sign up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Fredoka", sans-serif;
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
            color: green;
            font-weight: bold;
            display: <?= !empty($success_message) ? 'block' : 'none'; ?>;
        }
        #password-match-status span {
            font-weight: bold;
            font-size: 0.9em;
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

    <div class="container-user">
        <div class="form-container">
            <div class="user-title-container">
                <a id="ToLogin" href="login.php">Already have an account? <Span id="loginA">Login</Span></a> <br>
            </div>
            <form action="" method="POST" oninput="checkPasswordMatch()">
                <div class="inputs-container">
                    <!-- Email field -->
                    <label for="admin-email">Email</label>
                    <div class="email-container">
                        <span class="email-envelope-icon"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="signup_email" placeholder="Enter Email" value="<?= htmlspecialchars($signup_email); ?>" required> <br>
                    </div>
                    
                    <!-- Password fields -->
                    <label for="admin-password">Password</label>
                    <div class="password-container">
                        <span class="password-lock-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="signup_pw" name="signup_pw" placeholder="Enter Password" onfocus="showRequirements()" onkeyup="validatePassword(); checkPasswordMatch()" required> 
                        <span class="password-toggle" onclick="togglePassword('signup_pw')"><i class="fas fa-eye" id="eye-icon"></i></span><br>
                    </div>

                    <!-- Password requirements -->
                    <ul id="requirements" class="requirements">
                        <li id="length"> Must be at least 8 characters long</li>
                        <li id="capital"> Must have at least one capital letter</li>
                        <li id="number"> Must have at least one number</li>
                        <li id="special"> Must have at least one special character</li>
                    </ul>

                    <label for="admin-confirm-password">Confirm Password</label>
                    <div class="confirmpassword-container">
                        <span class="password-lock-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="confirm_signup_pw" name="confirm_signup_pw" placeholder="Confirm Password" oninput="checkPasswordMatch()" required>
                        <span class="password-toggle" onclick="togglePassword('confirm_signup_pw')"><i class="fas fa-eye" id="confirm-eye-icon"></i></span><br>
                    </div>

                    <!-- Password match/mismatch message -->
                    <div id="password-match-status">
                        <span id="password-mismatch" style="color: red; display: none;">Passwords do not match.</span>
                        <span id="password-match" style="color: green; display: none;">Passwords matched!</span>
                    </div>

                     <!-- Error and Success messages -->
                    <div id="error-message" class="error-message"><?= htmlspecialchars($error_message); ?></div>
                    <div id="success-message" class="success-message"><?= htmlspecialchars($success_message); ?></div>


                    <!-- Submit button -->
                    <button class="btn-admin-signup" type="submit">Register</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showRequirements() {
            document.getElementById("requirements").style.display = "block";
        }

        function validatePassword() {
            var password = document.getElementById("signup_pw").value;
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
            var password = document.getElementById("signup_pw").value;
            var confirmPassword = document.getElementById("confirm_signup_pw").value;
            var mismatchMessage = document.getElementById("password-mismatch");
            var matchMessage = document.getElementById("password-match");

            // Only show messages if both fields have content
            if (password && confirmPassword) {
                if (password !== confirmPassword) {
                    mismatchMessage.style.display = "block";
                    matchMessage.style.display = "none";
                } else {
                    mismatchMessage.style.display = "none";
                    matchMessage.style.display = "block";
                }
            } else {
                // Hide both messages if either field is empty
                mismatchMessage.style.display = "none";
                matchMessage.style.display = "none";
            }
        }

        function togglePassword(id) {
            var passwordField = document.getElementById(id);
            var icon = id === 'signup_pw' ? document.getElementById('eye-icon') : document.getElementById('confirm-eye-icon');
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>

