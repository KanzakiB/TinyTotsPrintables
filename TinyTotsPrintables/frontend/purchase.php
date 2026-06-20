<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

$searchQuery = "";
$priceMin = 0;
$priceMax = 1000;
$categoryFilter = 'all';  

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); 
    exit();
}

$customer_id = $_SESSION['customer_id'];

function fetchProductImage($conn, $prod_id) {
    $stmt = $conn->prepare("SELECT image_url FROM product_images WHERE prod_id = ? LIMIT 1");
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image_url = 'default.jpg'; 
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_url = $row['image_url'];
    }
    return $image_url;
}

$query = "
    SELECT c.first_name, c.profile_pic, a.house_no, a.street_name, a.barangay, a.city, a.postal_code
    FROM customer c
    LEFT JOIN customer_address a ON c.customer_id = a.customer_id
    WHERE c.customer_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($firstName, $profilePic, $houseNo, $streetName, $barangay, $city, $postalCode);
$stmt->fetch();
$stmt->close();
$profilePicBase64 = base64_encode($profilePic);

$order_query_all = "
    SELECT ol.order_id, ol.order_date, ol.status_id, ol.type_id, ol.shipping_fee, ol.prod_id, pl.prod_name, pl.prod_price, os.status_name, ol.quantity
    FROM order_list ol
    JOIN product_list pl ON ol.prod_id = pl.prod_id
    JOIN order_status os ON ol.status_id = os.status_id
    WHERE ol.customer_id = ?
    ORDER BY ol.order_date DESC
";

$order_query_pending = "
    SELECT ol.order_id, ol.order_date, ol.status_id, ol.type_id, ol.shipping_fee, ol.prod_id, pl.prod_name, pl.prod_price, os.status_name, ol.quantity
    FROM order_list ol
    JOIN product_list pl ON ol.prod_id = pl.prod_id
    JOIN order_status os ON ol.status_id = os.status_id
    WHERE ol.customer_id = ? AND ol.status_id = 1
    ORDER BY ol.order_date DESC
";

$order_query_completed = "
    SELECT ol.order_id, ol.order_date, ol.status_id, ol.type_id, ol.shipping_fee, ol.prod_id, pl.prod_name, pl.prod_price, os.status_name, ol.quantity
    FROM order_list ol
    JOIN product_list pl ON ol.prod_id = pl.prod_id
    JOIN order_status os ON ol.status_id = os.status_id
    WHERE ol.customer_id = ? AND ol.status_id = 4
    ORDER BY ol.order_date DESC
";


$stmt_all = $conn->prepare($order_query_all);
$stmt_all->bind_param("i", $customer_id);
$stmt_all->execute();
$stmt_all->store_result();
$stmt_all->bind_result($order_id, $order_date, $status_id, $type_id, $shipping_fee, $prod_id, $prod_name, $prod_price, $status_name, $quantity);
$orders_all = [];
while ($stmt_all->fetch()) {
    $prod_image = fetchProductImage($conn, $prod_id);
    $order_total = ($prod_price * $quantity) + $shipping_fee;
    $orders_all[] = [
        'order_id' => $order_id,
        'order_date' => $order_date,
        'status_name' => $status_name,
        'prod_name' => $prod_name,
        'prod_price' => $prod_price,
        'quantity' => $quantity, 
        'shipping_fee' => $shipping_fee,
        'prod_image' => $prod_image,
        'order_total' => $order_total
    ];
}
$stmt_all->close();

$stmt_pending = $conn->prepare($order_query_pending);
$stmt_pending->bind_param("i", $customer_id);
$stmt_pending->execute();
$stmt_pending->store_result();
$stmt_pending->bind_result($order_id, $order_date, $status_id, $type_id, $shipping_fee, $prod_id, $prod_name, $prod_price, $status_name, $quantity);
$orders_pending = [];
while ($stmt_pending->fetch()) {
    $prod_image = fetchProductImage($conn, $prod_id);
    $order_total = ($prod_price * $quantity) + $shipping_fee;
    $orders_pending[] = [
        'order_id' => $order_id,
        'order_date' => $order_date,
        'status_name' => $status_name,
        'prod_name' => $prod_name,
        'prod_price' => $prod_price,
        'shipping_fee' => $shipping_fee,
        'prod_image' => $prod_image,
        'order_total' => $order_total
    ];
}
$stmt_pending->close();

$stmt_completed = $conn->prepare($order_query_completed);
$stmt_completed->bind_param("i", $customer_id);
$stmt_completed->execute();
$stmt_completed->store_result();
$stmt_completed->bind_result($order_id, $order_date, $status_id, $type_id, $shipping_fee, $prod_id, $prod_name, $prod_price, $status_name, $quantity);
$orders_completed = [];
while ($stmt_completed->fetch()) {
    $prod_image = fetchProductImage($conn, $prod_id);
    $order_total = ($prod_price * $quantity) + $shipping_fee;
    $orders_completed[] = [
        'order_id' => $order_id,
        'order_date' => $order_date,
        'status_name' => $status_name,
        'prod_name' => $prod_name,
        'prod_price' => $prod_price,
        'shipping_fee' => $shipping_fee,
        'prod_image' => $prod_image,
        'order_total' => $order_total
    ];
}
$stmt_completed->close();
?>


<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <title>My Purchase</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/purchase.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
            <div class="nav-icons d-flex align-items-center ms-auto">
                <form class="search-bar" method="GET" action="shopnow.php">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search for Products" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit" class="btn btn-link ms-2" id="searchBtn">
                        <i class="bi bi-search" style="font-size: 1.5rem;"></i>
                    </button>
                </form>
                <a href="cart.php" title="Cart">
                    <i class="bi bi-cart" style="font-size: 1.5rem;"></i>
                </a>
                <a href="profile.php" title="profile" style="margin-left: 20px;">
                    <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="profile-nav-container">
            <div class="profilepic-con">
                <img id="profilenav" src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="profile-picture">              
                <p id="customername"><?php echo htmlspecialchars($firstName); ?></p>
            </div>
            <hr class="linebr">
            <div class="profnav-con">
                <div class="nava"><i class="fa-solid fa-user"></i><a href="profile.php">Profile</a></div>
                <div class="nava"><i class="fa-solid fa-location-dot"></i><a href="address.php">Address</a></div>
                <div class="nava"><i class="fa-solid fa-envelope"></i><a href="email_security.php">Change Email</a></div>
                <div class="nava"><i class="fa-solid fa-lock"></i><a href="password_security.php">Change Password</a></div>
                <div class="nava active"><i class="fa-solid fa-location-dot"></i><a href="purchase.php">My Purchase</a></div>
                <div class="nava"><i class="fa-solid fa-right-from-bracket"></i><a href="logout.php">Logout</a></div>
            </div>
        </div>

        <div class="profile-info-container">
            <!-- Tab Section -->
            <div class="tabs">
                <div class="tab active" data-target="#all">All</div>
                <div class="tab" data-target="#completed">Completed</div>
                <div class="tab" data-target="#pending">Pending</div>
            </div>

             <!-- Tab Content Section -->
        <div class="tab-content active" id="all">
            <?php if (empty($orders_all)): ?>
                <p>No Orders Yet</p>
            <?php else: ?>
                <div class="order-item-container scrollable">
                    <?php foreach ($orders_all as $order): ?>
                        <div class="order-container">
                            <div class="firstorderdetail">
                            <img id="orderimg" src="../backend/<?php echo htmlspecialchars($order['prod_image']); ?>" alt="product image">
                            <p id="orderprdname"><?php echo htmlspecialchars($order['prod_name']); ?></p>
                                <p id="ordertotal">Order Total: <span class="status">₱<?php echo number_format($order['order_total'], 2); ?></span></p>
                                </div>
                            <div class="orderbtn">
                                <p id="orderstatus">Status: <span class="status"><?php echo htmlspecialchars($order['status_name']); ?></span></p>
                                <?php if ($order['status_name'] == 'Pending'): ?>
                                    <button class="receiveorder" data-order-id="<?php echo $order['order_id']; ?>">Receive</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-content" id="completed">
            <?php if (empty($orders_completed)): ?>
                <p>No Completed Orders Yet</p>
            <?php else: ?>
                <div class="order-item-container scrollable">
                    <?php foreach ($orders_completed as $order): ?>
                        <div class="order-container">
                            <div class="firstorderdetail">
                                <img id="orderimg" src="../backend/<?php echo htmlspecialchars($order['prod_image']); ?>" alt="product image">
                                <p id="orderprdname"><?php echo htmlspecialchars($order['prod_name']); ?></p>
                                <p id="ordertotal">Order Total: <span class="status">₱<?php echo number_format($order['order_total'], 2); ?></span></p>
                            </div>
                            <div class="orderbtn">
                                <p id="orderstatus">Status: <span class="status"><?php echo htmlspecialchars($order['status_name']); ?></span></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-content" id="pending">
            <?php if (empty($orders_pending)): ?>
                <p>No Pending Orders Yet</p>
            <?php else: ?>
                <div class="order-item-container scrollable">
                    <?php foreach ($orders_pending as $order): ?>
                        <div class="order-container">
                            <div class="firstorderdetail">
                                <img id="orderimg" src="../backend/<?php echo htmlspecialchars($order['prod_image']); ?>" alt="product image">
                                <p id="orderprdname"><?php echo htmlspecialchars($order['prod_name']); ?></p>
                                <p id="ordertotal">Order Total: <span class="status">₱<?php echo number_format($order['order_total'], 2); ?></span></p>
                            </div>
                            <div class="orderbtn">
                                <p id="orderstatus">Status: <span class="status"><?php echo htmlspecialchars($order['status_name']); ?></span></p>
                                <?php if ($order['status_name'] == 'Pending'): ?>
                                    <button class="receiveorder" data-order-id="<?php echo $order['order_id']; ?>">Receive</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="receiveOrderModal" tabindex="-1" aria-labelledby="receiveOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiveOrderModalLabel">Order Received</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Your order has been successfully marked as received!
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      $(document).ready(function () {
        $('.tab').click(function () {
            $('.tab').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').removeClass('active');
            $($(this).data('target')).addClass('active');
        });

        $('.receiveorder').click(function () {
            var orderId = $(this).data('order-id');

            $.ajax({
                url: 'update_status.php',
                method: 'POST',
                data: { order_id: orderId, status_id: 4 },
                success: function(response) {
                    $('#order-' + orderId + ' #orderstatus').text('Status: Order Successful');
                    $('#order-' + orderId + ' .receiveorder').remove();

                    $('#completed').append($('#order-' + orderId));

                    $('#receiveOrderModal').modal('show');

                    setTimeout(function () {
                        $('#receiveOrderModal').modal('hide');
                        location.reload(); 
                    }, 3000);
                },
                error: function() {
                    alert('Error updating status');
                }
            });
        });
    });

    </script>


</body>
</html>
