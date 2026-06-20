<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');
require 'C:\XAMPP\htdocs\TinyTotsPrintables\Mail\PHPMailer\class.phpmailer.php';
require 'C:\XAMPP\htdocs\TinyTotsPrintables\Mail\PHPMailer\class.smtp.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); 
    exit();
}

$customer_id = $_SESSION['customer_id'];

$query = "
    SELECT c.first_name, c.profile_pic, c.email, a.house_no, a.street_name, a.barangay, a.city, a.postal_code
    FROM customer c
    LEFT JOIN customer_address a ON c.customer_id = a.customer_id
    WHERE c.customer_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($firstName, $profilePic, $email, $houseNo, $streetName, $barangay, $city, $postalCode);
$stmt->fetch();
$stmt->close();
$profilePicBase64 = base64_encode($profilePic);



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_verification_email'])) {
    $_SESSION['email_verification_sent'] = true; 
    $verificationLink = "http://localhost/TinyTotsPrintables/frontend/customer_pass.php"; 

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'aera.montefalco33@gmail.com';
    $mail->Password = 'cmcw pnwe fqxx meih'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('aera.montefalco33@gmail.com', 'TinyTots Printables');
    $mail->addAddress($email); 

    $mail->isHTML(true);
    $mail->Subject = 'Email Verification for Update';
    $mail->Body = "Please verify your email by clicking the button below:<br><br>
        <a href='$verificationLink' style='display: inline-block; padding: 10px 20px; font-size: 16px; color: white; background-color: #8900c9; text-decoration: none; border-radius: 10px;'>Verify Email</a>";

    if ($mail->send()) {
        echo "<script>alert('Verification email sent successfully! Please check your email.');</script>";
    } else {
        echo "<script>alert('Failed to send verification email. Please try again later.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Security</title>
  <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/security.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Fredoka", sans-serif;
        }
        body {
            background-image: url('images/background.png');
            background-size: cover;
            background-repeat: no-repeat;
        }

        .navbar-custom {
            background-color: #ffffff;
            padding: 10px;
            border-bottom: 5px solid #8900c9;
            height: 82px;
        }

        .navbar-custom .navbar-brand {
            color: black;
            font-weight: bold;
        }

        .navbar-custom .nav-link {
            color: black;
            margin-right: 15px;
        }

        .navbar-custom .bold-link {
            font-weight: bold;
        }

        .navbar-custom .nav-icons i {
            font-size: 1.5rem;
            color: black;
            margin-left: 15px;
            cursor: pointer;
        }

        .search-bar {
            display: flex;
            align-items: center;
        }

        .search-bar input {
            width: 250px;
        }
  </style>
</head>
<body>

        <nav class="navbar navbar-expand-lg navbar-custom sticky" id="myHeader">
            <div class="container-fluid">
                <a class="navbar-brand" href="shopnow.php">
                    <img src="http://localhost/TinyTotsPrintables/frontend/images/navbarlogo.png" alt="TinyTots Logo" class="img-fluid" style="max-height: 50px; margin-left: 87px;">
                </a>
                <h2 style="margin-top: 5px;">Security Check</h2>
                <div class="nav-icons d-flex align-items-center ms-auto">
                    <!-- Link to cart.php for the cart icon -->
                    <a href="profile.php" title="profile" style="margin-left: 20px;">
                        <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                    </a>
                </div>
            </div>
        </nav>

<div class="main-container">
    <div class="profile-info-container">
        <img src="http://localhost/TinyTotsPrintables/frontend/images/security.png" alt="security">
        <p>For security reasons, we require identity verification to update your password.</p>
        <form method="POST">
            <button id="sendEmail" name="send_verification_email">Send Verification Email</button>
        </form>
    </div>
</div>



</body>
</html>
