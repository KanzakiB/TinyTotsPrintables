<?php 
// Start the session BEFORE checking for adminID
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['adminID'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Connection to the database
include ("C:\\xampp\htdocs\TinyTotsPrintables\database\dbconnection.php");

// Fetch dashboard statistics
// Number of Products
$products_query = "SELECT COUNT(*) as product_count FROM product_list";
$products_result = mysqli_query($conn, $products_query);
$total_products = mysqli_fetch_assoc($products_result)['product_count'] ?? 0;

// Number of Customers
$customers_query = "SELECT COUNT(*) as customer_count FROM customer";
$customers_result = mysqli_query($conn, $customers_query);
$total_customers = mysqli_fetch_assoc($customers_result)['customer_count'] ?? 0;

// Successful Orders
$successful_orders_query = "SELECT COUNT(*) as successful_order_count 
                             FROM order_list ol
                             JOIN order_status os ON ol.status_id = os.status_id
                             WHERE os.status_name = 'Order Successful'";
$successful_orders_result = mysqli_query($conn, $successful_orders_query);
$total_successful_orders = mysqli_fetch_assoc($successful_orders_result)['successful_order_count'] ?? 0;

// Pending Orders
$pending_orders_query = "SELECT ol.order_id, os.status_name, ol.order_date 
                         FROM order_list ol 
                         JOIN order_status os ON ol.status_id = os.status_id 
                         WHERE os.status_name = 'Pending'
                         ORDER BY ol.order_date DESC
                         LIMIT 5";
$pending_orders_result = mysqli_query($conn, $pending_orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Fredoka', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Include sidebar and header -->
    <?php include 'admin_sidebar.php'; ?>
    <?php include 'admin_header.php'; ?>

    <main class="ml-64 mt-16 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Number of Products -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-500 text-sm">Total Products</h3>
                        <p class="text-2xl font-bold text-blue-600"><?= $total_products ?></p>
                    </div>
                    <div class="bg-blue-500 text-white p-3 rounded-full">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>

            <!-- Number of Customers -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-500 text-sm">Total Customers</h3>
                        <p class="text-2xl font-bold text-green-600"><?= $total_customers ?></p>
                    </div>
                    <div class="bg-green-500 text-white p-3 rounded-full">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Successful Orders -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-gray-500 text-sm">Successful Orders</h3>
                        <p class="text-2xl font-bold text-purple-600"><?= $total_successful_orders ?></p>
                    </div>
                    <div class="bg-purple-500 text-white p-3 rounded-full">
                        <i class="fas fa -check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sales Chart -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Monthly Sales Overview</h3>
                <canvas id="salesChart" height="190"></canvas>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Pending Orders</h3>
                    <a href="orderlist.php" class="text-blue-500 hover:underline">View All</a>
                </div>
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 text-left">Order ID</th>
                            <th class="py-2 text-left">Date</th>
                            <th class="py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = mysqli_fetch_assoc($pending_orders_result)): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-2"><?= $order['order_id'] ?></td>
                            <td class="py-2"><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                            <td class="py-2">
                                <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs">
                                    <?= $order['status_name'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Sales',
                    data: [12000, 19000, 15000, 22000, 18000, 25000, 22000, 28000, 20000, 23000, 26000, 30000],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return '₱ ' + tooltipItem.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount (₱)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>