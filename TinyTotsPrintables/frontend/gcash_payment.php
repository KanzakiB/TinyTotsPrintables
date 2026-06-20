<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');
require 'C:\XAMPP\htdocs\TinyTotsPrintables\Mail\PHPMailer\class.phpmailer.php';
require 'C:\XAMPP\htdocs\TinyTotsPrintables\Mail\PHPMailer\class.smtp.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

$query = "
    SELECT c.profile_pic, c.email
    FROM customer c
    WHERE c.customer_id = ? 
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($profilePic, $customerEmail);
$stmt->fetch();
$stmt->close();

$profilePicBase64 = base64_encode($profilePic);

$order_message = ''; 

if (isset($_POST['confirm_payment'])) {
    if (isset($_GET['order_type'])) {
        $order_type = $_GET['order_type'];  
    } else {
        $order_type = isset($_SESSION['order_type']) ? $_SESSION['order_type'] : '';  
    }

    $payment_method = 'Gcash'; 
    $shipping_fee = 0; 

    $type_id = 0;
    if ($order_type === 'Product Only') {
        $type_id = 1;
    } elseif ($order_type === 'Product and Deliver') {
        $type_id = 2;
        $shipping_fee = 60; 
    } elseif ($order_type === 'Product and Pickup') {
        $type_id = 3;
    }

    if ($type_id == 0) {
        $order_message = "Invalid order type.";
    } else {
        $status_id = 1;

        $prod_id = key($_SESSION['cart']); 
        $product = $_SESSION['cart'][$prod_id];
        $quantity = $_SESSION['order_quantity'];

        $stmt = $conn->prepare("
            INSERT INTO order_list (order_date, status_id, type_id, customer_id, shipping_fee, prod_id, quantity) 
            VALUES (NOW(), ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiidii", $status_id, $type_id, $customer_id, $shipping_fee, $prod_id, $quantity);

        if ($stmt->execute()) {
            unset($_SESSION['cart'][$prod_id]);
            unset($_SESSION['order_quantity']); 

            if ($order_type == 'Product Only') {
                $order_message = "Please check your email for the product download link.";

                $fileQuery = "SELECT prod_name, product_file FROM product_list WHERE prod_id = ?";
                $fileStmt = $conn->prepare($fileQuery);
                $fileStmt->bind_param("i", $prod_id);
                $fileStmt->execute();
                $fileStmt->store_result();

                if ($fileStmt->num_rows > 0) {
                    $fileStmt->bind_result($prod_name, $product_file);
                    $fileStmt->fetch();
                    $fileStmt->close();

                    if (is_readable($product_file)) {
                        $fileContent = file_get_contents($product_file);
                    } else {
                        $fileContent = $product_file;
                    }

                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'aera.montefalco33@gmail.com'; 
                    $mail->Password = 'cmcw pnwe fqxx meih';  
                    $mail->Port = 587;  

                    $mail->setFrom('aera.montefalco33@gmail.com', 'TinyTots Printables');
                    $mail->addAddress($customerEmail); 

                    $mail->isHTML(true); 
                    $mail->Subject = 'Payment Successful - Your Download Link';
                    $mail->Body    = "<div style='width: 100%; padding: 20px; background-color: #f4f4f9; font-family: Arial, sans-serif;'>
                                        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5); '>
                                            <h2 style='text-align: center; color: #333333;'>Thank you for your purchase!</h2>
                                            <p style='font-size: 16px; color: #333333;'>Dear Customer,</p>
                                            <p style='font-size: 16px; color: #333333;'>Thank you for your purchase! Your payment was successful.</p>
                                            <p style='font-size: 16px; color: #333333;'>Attached is your purchased product: $prod_name</p>
                                            <p style='font-size: 16px; color: #333333; margin-top: 20px;'>Thank you for choosing TinyTots Printables!</p>
                                        </div>
                                    </div>";

                    // Attach product file
                    $mail->addStringAttachment($fileContent, $prod_name . ".pdf");

                    if (!$mail->send()) {
                        echo "<script>alert('Failed to send Product file. Please try again later.');</script>";
                    } else {
                    }
                } else {
                    echo "<script>alert('Product not found in the database.');</script>";
                    $fileStmt->close();
                }


            } elseif ($order_type == 'Product and Deliver') {
                $order_message = "Your order will be delivered by " . date('l, F j, Y', strtotime('+2 days')) . ". Thank you for ordering!";
            } elseif ($order_type == 'Product and Pickup') {
                $order_message = "Your order can be picked up by " . date('l, F j, Y', strtotime('+2 days')) . ". Thank you for ordering!";
            }
        } else {
            $order_message = "There was an issue processing your payment. Please try again.";
        }
        $stmt->close();
    }
}

?>


<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <title>GCash Payment</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Fredoka", sans-serif;
        }
        body {
            background-image: url('images/gcashbg.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-x: hidden;
            padding-bottom: 15px;
        }
        .navbar-custom {
            background-color: #ffffff;
            padding: 10px;
            border-bottom: 5px solid #8900c9;
            height: 82px;
        }
        .navbar-custom .nav-icons i {
            font-size: 1.5rem;
            color: black;
            margin-left: 15px;
            cursor: pointer;
        }
        .container {
            padding-bottom: 15px;
            display: flex;
        }
        #logopayment{
            width: 500px;
            height: 120px;
        }
        #confirmGcashPayment {
            padding: 10px;
            border: none;
            background-color: #8900c9;
            color: #ffffff;
            border-radius: 10px;
            font-size: 20px;
            width: 250px;
            margin-left: 70px;
            margin-top: 40px;
        }
        #confirmGcashPayment:hover{
            background-color: #6b21a8;
        }
        #qrcode{
            margin-left: 250px;
            height: 400px;
            width: 400px;
        }
        .modal-content {
            border-radius: 8px;
            padding: 20px;
            font-family: 'Arial', sans-serif;
        }

        .modal-header {
            border-bottom: 2px solid #8900c9;
        }

        .modal-body {
            font-size: 16px;
            line-height: 1.6;
        }

        .modal-footer button {
            background-color: #8900c9;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
        }

        .modal-footer button:hover {
            background-color: #660093;
        }

    </style>
</head>
<body>
    <div class="wrap">
        <nav class="navbar navbar-expand-lg navbar-custom sticky" id="myHeader">
            <div class="container-fluid">
                <a class="navbar-brand" href="shopnow.php">
                    <img src="http://localhost/TinyTotsPrintables/frontend/images/navbarlogo.png" alt="TinyTots Logo" class="img-fluid" style="max-height: 50px; margin-left: 87px;">
                </a>
                <div class="nav-icons d-flex align-items-center ms-auto">
                    <a href="cart.php" title="Cart"><i class="bi bi-cart"></i></a>
                    <a href="profile.php" title="profile" style="margin-left: 20px;">
                        <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                    </a>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <div class="firstdiv">
                <img id="logopayment" src="http://localhost/TinyTotsPrintables/frontend/images/navbarlogo.png" alt="logo">
                <h1 style="margin-top: 30px; font-weight: bold;">Scan QR Code for Payment</h1>
                <h2 style="margin-top: 30px; font-weight: bold;">Mobile Number: 09678803101</h2>
                <form method="POST">
                    <button id="confirmGcashPayment" type="submit" name="confirm_payment">Payment Successful</button>
                </form>
            </div>
            <div class="seconddiv">
                <img id="qrcode" src="http://localhost/TinyTotsPrintables/frontend/images/qr.png" alt="qr">
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <?php if ($order_message): ?>
        <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">Order Status</h5>
                    </div>
                    <div class="modal-body">
                        <?php echo $order_message; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="window.location.href='shopnow.php';">Okay</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.onload = function() {
                var myModal = new bootstrap.Modal(document.getElementById('orderModal'));
                myModal.show();
            };
        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
