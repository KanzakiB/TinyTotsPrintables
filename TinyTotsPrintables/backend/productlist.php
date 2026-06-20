<?php
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');
session_start();

// File type validation functions
function isValidPdfFile($file) {
    $allowed_types = ['application/pdf', 'application/x-pdf'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    
    // Use the temporary file path from $_FILES
    $mime_type = finfo_file($file_info, $file['tmp_name']);
    finfo_close($file_info);
    
    return in_array($mime_type, $allowed_types) && 
           pathinfo($file['name'], PATHINFO_EXTENSION) === 'pdf';
}

function isValidImageFile($file) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $file['tmp_name']);
    finfo_close($file_info);
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    return in_array($mime_type, $allowed_types) && 
           in_array($file_extension, $allowed_extensions);
}

// Clear previous messages
if (isset($_SESSION['message_shown'])) {
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
    unset($_SESSION['message_shown']);
}

// Handle Add Product form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $prod_name = mysqli_real_escape_string($conn, $_POST['prod_name']);
    $cat_id = (int)$_POST['cat_id'];
    $prod_price = (float)$_POST['prod_price'];
    $prod_pages = (int)$_POST['prod_pages'];
    $product_file = null;

    // Validate and handle product file upload
    if (isset($_FILES['product_file']) && $_FILES['product_file']['error'] === UPLOAD_ERR_OK) {
        if (!isValidPdfFile($_FILES['product_file'])) {
            $_SESSION['error_message'] = "Invalid product file. Only PDF files are allowed.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $file_tmp = $_FILES['product_file']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['product_file']['name']);
        $product_file = 'product_pdf/' . $file_name;
        move_uploaded_file($file_tmp, $product_file);
    }

    // Insert into product_list table
    $query = "INSERT INTO product_list (prod_name, cat_id, prod_price, date_added, prod_pages, product_file) 
              VALUES ('$prod_name', '$cat_id', '$prod_price', NOW(), '$prod_pages', '$product_file')";
    
    if (mysqli_query($conn, $query)) {
        $new_prod_id = mysqli_insert_id($conn);

        // Handle image uploads
        for ($i = 1; $i <= 4; $i++) {
            if (isset($_FILES["image_file$i"]) && $_FILES["image_file$i"]['error'] === UPLOAD_ERR_OK) {
                // Validate image file
                if (!isValidImageFile($_FILES["image_file$i"])) {
                    $_SESSION['error_message'] = "Invalid image file. Only JPG and PNG files are allowed.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                $image_tmp = $_FILES["image_file$i"]['tmp_name'];
                $image_name = uniqid() . '_' . basename($_FILES["image_file$i"]['name']);
                $image_url = 'product_image/' . $image_name;
                move_uploaded_file($image_tmp, $image_url);

                $image_query = "INSERT INTO product_images (prod_id, image_url) VALUES ('$new_prod_id', '$image_url')";
                mysqli_query($conn, $image_query);
            }
        }

        $_SESSION['success_message'] = "Product added successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to add product: " . mysqli_error($conn);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $prod_id = (int)$_POST['prod_id'];
    $prod_name = mysqli_real_escape_string($conn, $_POST['prod_name']);
    $cat_id = (int)$_POST['cat_id'];
    $prod_price = (float)$_POST['prod_price'];
    $prod_pages = (int)$_POST['prod_pages'];

    // Update product details
    $query = "UPDATE product_list SET prod_name='$prod_name', cat_id='$cat_id', prod_price='$prod_price', prod_pages='$prod_pages' WHERE prod_id='$prod_id'";
    
    if (mysqli_query($conn, $query)) {
        // Handle image uploads
        for ($i = 1; $i <= 4; $i++) {
            if (isset($_FILES["image_file$i"]) && $_FILES["image_file$i"]['error'] === UPLOAD_ERR_OK) {
                // Validate image file
                if (!isValidImageFile($_FILES["image_file$i"])) {
                    $_SESSION['error_message'] = "Invalid image file. Only JPG and PNG files are allowed.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                $image_tmp = $_FILES["image_file$i"]['tmp_name'];
                $image_name = uniqid() . '_' . basename($_FILES["image_file$i"]['name']);
                $image_url = 'product_image/' . $image_name;
                move_uploaded_file($image_tmp, $image_url);

                $image_query = "INSERT INTO product_images (prod_id, image_url) VALUES ('$prod_id', '$image_url')";
                mysqli_query($conn, $image_query);
            }
        }

        $_SESSION['success_message'] = "Product updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update product: " . mysqli_error($conn);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Delete Product
if (isset($_GET['delete_product'])) {
    $prod_id = (int)$_GET['delete_product'];

    // Delete product images first
    $delete_images = "DELETE FROM product_images WHERE prod_id='$prod_id'";
    mysqli_query($conn, $delete_images);

    // Delete the product
    $delete_product = "DELETE FROM product_list WHERE prod_id='$prod_id'";
    if (mysqli_query($conn, $delete_product)) {
        $_SESSION['success_message'] = "Product deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete product: " . mysqli_error($conn);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Sorting functionality
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'prod_id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column and order
$allowed_sorts = ['prod_id', 'prod_name', 'cat_name', 'prod_price', 'prod_pages', 'date_added'];
$sort = in_array($sort, $allowed_sorts) ? $sort : 'prod_id';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

//Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$start = ($page - 1) * $limit;

$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$category_condition = $category ? "WHERE pl.cat_id = $category" : "";

$cat_query = "SELECT * FROM category_list";
$cat_result = mysqli_query($conn, $cat_query);

// Modify total count query to include category filter
$total_query = "SELECT COUNT(*) as count 
                FROM product_list pl
                LEFT JOIN category_list cl ON pl.cat_id = cl.cat_id
                $category_condition";
$total_result = mysqli_query($conn, $total_query);
$total_rows = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_rows / $limit);

// Update main query to include sorting and category filtering
$query = "SELECT pl.*, cl.cat_name, 
          (SELECT image_url FROM product_images pi WHERE pi.prod_id = pl.prod_id LIMIT 1) as image_url
          FROM product_list pl
          LEFT JOIN category_list cl ON pl.cat_id = cl.cat_id
          $category_condition 
          ORDER BY $sort $order
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
$product_count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Fredoka', sans-serif;
    }
</style>
</head>

<body class="ml-72 bg-gray-100">
    <div class="flex">
        <?php include 'admin_sidebar.php'; ?>
        
        <main class="mt-8 p-6 w-full">
            <?php include 'admin_header.php'; ?>

            <div class="container mx-auto p-4">
                <h1 class="text-2xl font-bold mt-10 mb-4 text-purple-800">Product List</h1>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                        <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    </div>
                <?php elseif (isset($_SESSION['error_message'])): ?>
                    <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                        <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <!--Add Product Section -->
                <div class="flex flex-col sm:flex-row justify-between mb-4 space-y-2 sm:space-y-0">
                    <button id="openAddModal" class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-700">
                        Add Product
                    </button>

                    <form method="GET" class="flex items-center space-x-2">
                <select 
                    name="category" 
                    onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
                    <option value="">All Categories</option>
                    <?php 
                    mysqli_data_seek($cat_result, 0);
                    while ($cat_row = mysqli_fetch_assoc($cat_result)): ?>
                        <option value="<?= $cat_row['cat_id'] ?>" 
                            <?= $category == $cat_row['cat_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat_row['cat_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
            </form>
        </div>

                <!-- Add Product Modal Content -->
                <div id="addProductModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                        <h2 class="text-xl font-bold text-purple-800 mb-4">Add Product</h2>
                        <form id="addProductForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="add_product" value="1">
                            <div class="mb- 4">
                                <label class="block text-sm font-medium text-purple-700">Product Name</label>
                                <input type="text" name="prod_name" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-purple-700">Category</label>
                                <select name="cat_id" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                                    <?php 
                                    mysqli_data_seek($cat_result, 0);
                                    while ($cat_row = mysqli_fetch_assoc($cat_result)): ?>
                                        <option value="<?= $cat_row['cat_id'] ?>"><?= htmlspecialchars($cat_row['cat_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-purple-700">Price</label>
                                <select name="prod_price" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="150">150</option>
                                    <option value="200">200</option>
                                    <option value="250">250</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-purple-700">Pages</label>
                                <select name="prod_pages" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-purple-700">Product File (PDF only)</label>
                                <input type="file" name="product_file" accept=".pdf" class="mt-1 block w-full">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-purple-700">Product Image (JPG/PNG only)</label>
                                <input type="file" name="image_file1" accept=".jpg,.jpeg,.png" class="mt-1 block w-full mb-2">
                            </div>
                            <div class="flex items-center justify-between">
                                <button type="submit" class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-700">
                                    Add Product
                                </button>
                                <button type="button" id="closeAddModal" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-400">
                                    Close
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Product Table -->
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse border border-gray-200">
                        <thead class="bg-purple-800 text-white">
                            <tr>
                                <th class="px-1.5 py-1 text-sm text-left">Images</th>
                                <th class="px-1.5 py-1 text-sm text-left">Product Name</th>
                                <th class="px-1.5 py-1 text-sm text-left">Category</th>
                                <th class="px-1.5 py-1 text-sm text-left">Price</th>
                                <th class="px-1.5 py-1 text-sm text-left">Pages</th>
                                <th class="px-1.5 py-1 text-sm text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($product_count > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="px-2 py-1 text-sm">
                                            <?php if ($row['image_url']): ?>
                                                <img src="<?= $row['image_url'] ?>" alt="Product Image" class="w-12 h-12 object-cover rounded-md">
                                            <?php else: ?>
                                                No Image
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-2 py-1 text-sm"><?= htmlspecialchars($row['prod_name']) ?></td>
                                        <td class="px-2 py-1 text-sm"><?= htmlspecialchars($row['cat_name']) ?></td>
                                        <td class="px-2 py-1 text-sm"><?= number_format($row['prod_price'], 2) ?></td>
                                        <td class="px-2 py-1 text-sm"><?= $row['prod_pages'] ?></td>
                                        <td class="px-2 py-1 text-sm">
                                            <button onclick="openEditModal(<?= $row['prod_id'] ?>, '<?= htmlspecialchars($row['prod_name']) ?>', <?= $row['cat_id'] ?>, <?= $row['prod_price'] ?>, <?= $row['prod_pages'] ?>)" class="text-blue-500 hover:underline">Edit</button> |
                                            <button onclick="openDeleteModal(<?= $row['prod_id'] ?>, '<?= htmlspecialchars($row['prod_name']) ?>')" class="text-red-500 hover:underline">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-sm">No products found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Edit Product Modal -->
<div id="editProductModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold text-purple-800 mb-4">Edit Product</h2>
        <form id="editProductForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_product" value="1">
            <input type="hidden" id="editProdId" name="prod_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-700">Product Name</label>
                <input type="text" id="editProdName" name="prod_name" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-700">Category</label>
                <select id="editCategoryId" name="cat_id" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                    <?php 
                    mysqli_data_seek($cat_result, 0);
                    while ($cat_row = mysqli_fetch_assoc($cat_result)): ?>
                        <option value="<?= $cat_row['cat_id'] ?>"><?= htmlspecialchars($cat_row['cat_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-700">Price</label>
                <select id="editProdPrice" name="prod_price" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="250">250</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-700">Pages</label>
                <select id="editProdPages" name="prod_pages" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-700">Product File (PDF only)</label>
                <input type="file" name="product_file" accept=".pdf" class="mt-1 block w-full">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-700">Product Image (JPG/PNG only)</label>
                <input type="file" name="image_file1" accept=".jpg,.jpeg,.png" class="mt-1 block w-full mb-2">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-700">
                    Update Product
                </button>
                <button type="button" id="closeEditModal" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-400">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Product Modal -->
<div id="deleteProductModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold text-red-800 mb-4">Confirm Delete</h2>
        <input type="hidden" id="deleteProductId">
        <p class="mb-4">Are you sure you want to delete the product: <span id="deleteProductName" class="font-bold"></span>?</p>
        <div class="flex items-center justify-between">
            <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-700">
                Delete
            </button>
            <button id="cancelDeleteBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-400">
                Cancel
            </button>
        </div>
    </div>
</div>

                <!-- Pagination -->
                <div class="mt-4 text-center">
                    <nav class="inline-flex rounded-md shadow-sm">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="px-4 py-2 border border-purple-300 text-sm text-purple-800 rounded-l-md">Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="px-4 py-2 border text-sm <?= $i == $page ? 'bg-purple-800 text-white' : 'border-purple-300 text-purple-800' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="px-4 py-2 border border-purple-300 text-sm text-purple-800 rounded-r-md">Next</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Function to open edit modal with product details
    function openEditModal(prodId, prodName, catId, prodPrice, prodPages) {
        document.getElementById('editProdId').value = prodId;
        document.getElementById('editProdName').value = prodName;
        document.getElementById('editCategoryId').value = catId;
        document.getElementById('editProdPrice').value = prodPrice;
        document.getElementById('editProdPages').value = prodPages;
        document.getElementById('editProductModal').classList.remove('hidden');
    }

    // Modal event listeners
    document.getElementById('openAddModal').addEventListener('click', function() {
        document.getElementById('addProductModal').classList.remove('hidden');
    });

    document.getElementById('closeAddModal').addEventListener('click', function() {
        document.getElementById('addProductModal').classList.add('hidden');
    });

    document.getElementById('closeEditModal').addEventListener('click', function() {
        document.getElementById('editProductModal').classList.add('hidden');
    });

    // Prevent form resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Function to open delete modal
    function openDeleteModal(prodId, prodName) {
        document.getElementById('deleteProductId').value = prodId;
        document.getElementById('deleteProductName').textContent = prodName;
        document.getElementById('deleteProductModal').classList.remove('hidden');
    }

    // Delete modal event listeners
    document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
        document.getElementById('deleteProductModal').classList.add('hidden');
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const prodId = document.getElementById('deleteProductId').value;
        window.location.href = `?delete_product=${prodId}`;
    });

    // Add Product Modal Functionality
    document.getElementById('openAddModal').addEventListener('click', function() {
        document.getElementById('addProductModal').classList.remove('hidden');
    });

    document.getElementById('closeAddModal').addEventListener('click', function() {
        document.getElementById('addProductModal').classList.add('hidden');
    });

    // Optional: Add validation for add product form
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        const prodName = this.querySelector('input[name="prod_name"]');
        const catId = this.querySelector('select[name="cat_id"]');
        const prodPrice = this.querySelector('select[name="prod_price"]');
        const prodPages = this.querySelector('select[name="prod_pages"]');

        // Basic validation
        if (!prodName.value.trim()) {
            e.preventDefault();
            alert('Please enter a product name');
            prodName.focus();
            return;
        }

        if (!catId.value) {
            e.preventDefault();
            alert('Please select a category');
            catId.focus();
            return;
        }

        if (!prodPrice.value) {
            e.preventDefault();
            alert('Please select a price');
            prodPrice.focus();
            return;
        }

        if (!prodPages.value) {
            e.preventDefault();
            alert('Please select number of pages');
            prodPages.focus();
            return;
        }
    });

    // Mark message as shown
    <?php if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])): ?>
        <?php $_SESSION['message_shown'] = true; ?>
    <?php endif; ?>
</script>
</body>
</html>