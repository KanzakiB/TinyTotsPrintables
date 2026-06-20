<?php
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');
session_start();

// Handle Search and Sorting
$searchTerm = '';
$statusFilter = '';
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
}

if (isset($_GET['status_filter'])) {
    $statusFilter = mysqli_real_escape_string($conn, $_GET['status_filter']);
}

// Fetch Orders with Details and Apply Search and Filter
$sqlOrder = "
    SELECT 
        ol.order_id, 
        ol.order_date, 
        ol.status_id,
        os.status_name, 
        ot.type_name, 
        c.first_name, 
        c.last_name, 
        c.email, 
        c.contact_number, 
        ca.house_no, ca.street_name, ca.barangay, ca.city, ca.postal_code, 
        pl.prod_name, pl.prod_price, pl.prod_pages, 
        pi.image_url
    FROM order_list ol
    LEFT JOIN order_status os ON ol.status_id = os.status_id
    LEFT JOIN order_type ot ON ol.type_id = ot.type_id
    LEFT JOIN customer c ON ol.customer_id = c.customer_id
    LEFT JOIN customer_address ca ON c.customer_id = ca.customer_id
    LEFT JOIN product_list pl ON ol.prod_id = pl.prod_id
    LEFT JOIN (
        SELECT prod_id, image_url 
        FROM product_images 
        ORDER BY image_id ASC
    ) pi ON pl.prod_id = pi.prod_id
    WHERE (pl.prod_name LIKE ? OR CONCAT(c.first_name, ' ', c.last_name) LIKE ?)
    " . ($statusFilter ? "AND os.status_id = ?" : "") . "
    ORDER BY os.status_name ASC";

$stmt = $conn->prepare($sqlOrder);
$searchWildcard = "%$searchTerm%";
if ($statusFilter) {
    $stmt->bind_param("ssi", $searchWildcard, $searchWildcard, $statusFilter);
} else {
    $stmt->bind_param("ss", $searchWildcard, $searchWildcard);
}
$stmt->execute();
$resultOrders = $stmt->get_result();

function calculateFees($type_name, $pages) {
    $shipping_fee = ($type_name === "Product and Deliver") ? 50.00 : 0.00;
    $printing_fee = 0.00;

    if ($type_name === "Product and Deliver" || $type_name === "Product and Pickup") {
        if ($pages == 5) $printing_fee = 40.00;
        else if ($pages == 10) $printing_fee = 80.00;
        else if ($pages == 15) $printing_fee = 130.00;
    }
    return [$shipping_fee, $printing_fee];
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status_id = $_POST['status_id'];
    $updateQuery = "UPDATE order_list SET status_id = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $status_id, $order_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Order status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 ml-72">
    <div class="flex">
        <?php include 'admin_sidebar.php'; ?>
        
        <main class="flex-1 p-4 sm:p-6 md:p-8 sm:ml-8 md:ml-16 lg:ml-64 transition-all duration-300">
            <?php include 'admin_header.php'; ?>

            <div class="container mx-auto p-4">
                <h1 class="text-2xl font-bold mt-10 mb-4 text-purple-800">Order List</h1>

                <!-- Display Messages -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php elseif (isset($_SESSION['error'])): ?>
                    <div class="bg-red-200 text-red-800 p-2 rounded mb-4">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Search and Sorting -->
                <div class="flex justify-between items-center mb-4">
                    <form method="GET" action="" class="w-1/3">
                        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm); ?>" class="w-full p-2 border border-gray-300 rounded" placeholder="Search Orders">
                    </form>

                    <div class="flex space-x-4">
                        <!-- Sort by Status -->
                        <form method="GET" action="" class="flex items-center">
                            <select name="status_filter" onchange="this.form.submit()" class="p-2 border border-gray-300 rounded">
                                <option value="">All Statuses</option>
                                <option value="1" <?= ($statusFilter == "1") ? "selected" : ""; ?>>Pending</option>
                                <option value="2" <?= ($statusFilter == "2") ? "selected" : ""; ?>>Out for Delivery</option>
                                <option value="3" <?= ($statusFilter == "3") ? "selected" : ""; ?>>Ready for Pickup</option>
                                <option value="4" <?= ($statusFilter == "4") ? "selected" : ""; ?>>Order Successful</option>
                                <option value="5" <?= ($statusFilter == "5") ? "selected" : ""; ?>>Order Cancelled</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse border border-purple-200">
                        <thead class="bg-purple-800 text-white">
                            <tr>
                                <th class="p-1.5 text-left">Order Date</th>
                                <th class="p-1.5 text-left">Product Name</th>
                                <th class="p-1.5 text-left">Order Type</th>
                                <th class="p-1.5 text-left">Total Amount</th>
                                <th class="p-1.5 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($resultOrders)): ?>
                                <?php 
                                    list($shipping_fee, $printing_fee) = calculateFees($row['type_name'], $row['prod_pages']);
                                    $total_amount = $row['prod_price'] + $shipping_fee + $printing_fee;
                                ?>
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="p-3"><?= $row['order_date']; ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['prod_name']); ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['type_name']); ?></td>
                                    <td class="p-3">₱ <?= number_format($total_amount, 2); ?></td>
                                    <td class="p-3">
                                        <button onclick="openModal('viewModal-<?= $row['order_id']; ?>')" class="bg-green-500 text-white px-2 py-1 rounded">View</button>
                                    </td>
                                </tr>

                                <!-- View Modal -->
                                <div id="viewModal-<?= $row['order_id']; ?>" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
                                    <div class="bg-white p-6 rounded shadow-lg w-full max-w-lg">
                                        <h2 class="text-xl font-bold mb-4">Order Details</h2>
                                        <p><strong>Order Date:</strong> <?= $row['order_date']; ?></p>
                                        <p><strong>Customer:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></p>
                                        <p><strong>Email:<?= htmlspecialchars($row['email']); ?></p>
                                        <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_number']); ?></p>
                                        <p><strong>Address:</strong> <?= htmlspecialchars($row['house_no'] . ', ' . $row['street_name'] . ', ' . $row['barangay'] . ', ' . $row['city'] . ', ' . $row['postal_code']); ?></p>
                                        <hr class="my-4">
                                        <p><strong>Product Name:</strong> <?= htmlspecialchars($row['prod_name']); ?></p>
                                        <p><strong>Pages:</strong> <?= $row['prod_pages']; ?></p>
                                        <p><strong>Unit Price:</strong> ₱<?= number_format($row['prod_price'], 2); ?></p>
                                        <p><strong>Order Type:</strong> <?= htmlspecialchars($row['type_name']); ?></p>
                                        <p><strong>Shipping Fee:</strong> ₱<?= number_format($shipping_fee, 2); ?></p>
                                        <p><strong>Printing Fee:</strong> ₱<?= number_format($printing_fee, 2); ?></p>
                                        <p><strong>Total Amount:</strong> ₱<?= number_format($total_amount, 2); ?></p>
                                        <div class="mt-4 flex justify-end">
                                            <form method="POST">
                                                <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                                                <select name="status_id" class="p-2 border border-gray-300 rounded">
                                                    <option value="1" <?= ($row['status_id'] == 1) ? "selected" : ""; ?>>Pending</option>
                                                    <option value="2" <?= ($row['status_id'] == 2) ? "selected" : ""; ?>>Out for Delivery</option>
                                                    <option value="3" <?= ($row['status_id'] == 3) ? "selected" : ""; ?>>Ready for Pickup</option>
                                                    <option value="4" <?= ($row['status_id'] == 4) ? "selected" : ""; ?>>Order Successful</option>
                                                    <option value="5" <?= ($row['status_id'] == 5) ? "selected" : ""; ?>>Order Cancelled</option>
                                                </select>
                                                <button type="submit" name="update_status" class="bg-blue-500 text-white px-4 py-2 rounded ml-4">Update Status</button>
                                            </form>
                                            <button class="bg-red-500 text-white px-4 py-2 rounded ml-4" onclick="closeModal('viewModal-<?= $row['order_id']; ?>')">Close</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    function openModal(id) {
                        document.getElementById(id).classList.remove('hidden');
                    }
                    function closeModal(id) {
                        document.getElementById(id).classList.add('hidden');
                    }
                </script>
            </div>
        </main>
    </div>
</body>
</html>