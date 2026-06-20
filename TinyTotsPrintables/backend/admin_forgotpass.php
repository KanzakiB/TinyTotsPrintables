
<?php
    // Connection to database
    include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');
    require 'C:\XAMPP\htdocs\TinyTotsPrintables\phpmailer\class.phpmailer.php';
    require 'C:\XAMPP\htdocs\TinyTotsPrintables\phpmailer\class.smtp.php';

    // For Sending Otp
    function sendOtp($email, $otp) {
        $mail = new PHPMailer;
        $mail->SMTPDebug = 2;  // Enable verbose debug output
        
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Wag na galawin
        $mail->SMTPAuth   = true;
        $mail->Username   = 'aro_katrinaanne@plpasig.edu.ph';  // email to use
        $mail->Password   = 'cmcw pnwe fqxx meih';    // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('aro_katrinaanne@plpasig.edu.ph', 'ADMIN THE HAPPY TAILS ');
        $mail->addAddress($email);    // sino makaka receive $email in the database used

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP code is <b>$otp</b>. Please use this to complete your password reset.";

        if ($mail->send()) {
            return true;
        } else {
            echo "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
            return false;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['send_otp_button'])) {
            $send_email = $_POST['send_email'];

            // Check if the email exists in the database
            $check_email_sql = "SELECT * FROM admin_acc WHERE admin_email='$send_email'";
            $check_email_result = $conn->query($check_email_sql);

            if ($check_email_result->num_rows > 0) 
            {
                // If Email exists, generate OTP
                $otp = rand(100000, 999999); // Generate a random 6-digit OTP

                // Send OTP to the users email
                if (sendOtp($send_email, $otp)) 
                {
                    // If OTP is sent successfully, save it in the session and redirect
                    session_start();
                    $_SESSION['otp'] = $otp;
                    $_SESSION['otp_email'] = $send_email;
                    header("Location: admin_confirmotp.php?email=" . urlencode($send_email));
                    exit();
                } else 
                {
                    echo "<p style='color:red;'>Failed to send OTP. Please try again later.</p>";
                }
            } else 
            {
                // If email does not exist, show an error message
                echo "<p style='color:red;'>Email does not exist in our records.</p>";
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
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/backend/images/bg.png">
    

    <title>Forgot Password</title>
</head>
<body>
    <div class="logo">
        <img src="http://localhost/TinyTotsPrintables/backend/images/logo.png" id="imglogo" alt="logo" >
    </div>
    <br>
    <div class="container">
        <div class="form-container">
            <form id="forgotPasswordForm" action="" method="POST">
                <div class="title-container">
                    <h3>FORGOT PASSWORD</h3>
                </div>
                <div class="inputs-container">
                    <label for="email">Email</label>
                    <div class="email-container">
                        <span class="envelope-icon"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="send_email" placeholder="Enter Email" required> <br>
                    </div>
                    <div class="button-container">
                        <button id="btnbacklogin" onclick="goBack()" type="button" name="back_to_login">Back</button>
                        <button id="btntootp" type="submit" name="send_otp_button">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <br>
    <br>
    <script>
        function goBack() {
            window.location.href = 'admin_login.php' ;
    }

    </script>
</body>
</html>
