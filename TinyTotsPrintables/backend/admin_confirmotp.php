<?php
    // Connection to database
    include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

    session_start(); // Start 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['for_otp'])) {
            $entered_otp = $_POST['for_otp'];

            // Check if the entered OTP matches the one in the session
            if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp']) {
                // If OTP is correct, redirect to login.php
                header("Location: admin_resetpass.php");
                exit();
            } else {
                // IF OTP is incorrect, show an error message
                $error_message = "Wrong OTP. Please try again.";
            }
        }
    }
    
    // Display error message if OTP is incorrect
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
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
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/backend/css/admin_confirmotp.css">

    <title>Verify OTP</title>
</head>
<body>
    <div class="logo">
        <img src="http://localhost/TinyTotsPrintables/backend/images/logo.png" id="imglogo" alt="logo" >
    </div>
    <br>
    
    <div class="container">
        <div class="form-container">
            <form action="" method="POST">
                <div class="title-container">
                    <h3>VERIFY CODE</h3>
                </div>
                <div class="inputs-container">
                    <label for="otp">Enter OTP Code</label>
                    <div class="otp-container">
                        <span class="key-icon"><i class="fa-solid fa-key"></i></span>
                        <input type="number" name="for_otp" placeholder="Enter OTP" maxlength="6" required> <br>
                    </div>
                    <div class="button-container">
                        <button id="btnotpback" onclick="goBack()" type="submit">Back</button>
                        <button id="btnverify" type="submit">Verify</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function goBack() {
            window.location.href = 'admin_forgotpass.php' ;
        }

    </script>
</body>
</html>

