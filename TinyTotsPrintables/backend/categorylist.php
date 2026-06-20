<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');
session_start();

// Initialize variables
$message = "";
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $categoryName = trim($_POST['categoryName']);
        
        if (!empty($categoryName)) {
            // Prepare insert statement
            $sql = "INSERT INTO category_list (cat_name, date_added) VALUES (?, NOW())";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                $message = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("s", $categoryName);

                if ($stmt->execute()) {
                    $message = "Category added successfully!";
                } else {
                    $message = "Error adding category: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $message = "Category name cannot be empty!";
        }
    } 
    // Handle Update Category
    elseif (isset($_POST['update'])) {
        $catId = $_POST['cat_id'];
        $categoryName = trim($_POST['categoryName']);
        
        if (!empty($categoryName)) {
            $sql = "UPDATE category_list SET cat_name = ? WHERE cat_id = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                $message = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("si", $categoryName, $catId);

                if ($stmt->execute()) {
                    $message = "Category updated successfully!";
                } else {
                    $message = "Error updating category: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $message = "Category name cannot be empty!";
        }
    } 
    // Handle Delete Category
    elseif (isset($_POST['delete'])) {
        $catId = $_POST['cat_id'];
        
        $sql = "DELETE FROM category_list WHERE cat_id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $message = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("i", $catId);

            if ($stmt->execute()) {
                $message = "Category deleted successfully!";
            } else {
                $message = "Error deleting category: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch Categories
try {
    // Base query
    $sql = "SELECT * FROM category_list";
    
    // Add search condition if search term exists
    if (!empty($search)) {
        $sql .= " WHERE cat_name LIKE ? OR cat_id LIKE ?";
    }
    
    // Add ordering
    $sql .= " ORDER BY cat_id";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind search parameters if search is present
    if (!empty($search)) {
        $searchParam = "%$search%";
        $stmt->bind_param("ss", $searchParam, $searchParam);
    }

    // Execute the query
    $executeResult = $stmt->execute();
    
    // Check execution
    if (!$executeResult) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    // Get the result
    $result = $stmt->get_result();

    // Get total number of categories
    $category_count = $result->num_rows;

} catch (Exception $e) {
    // Handle any errors
    $message = "Error: " . $e->getMessage();
    $category_count = 0;
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles if needed */
        body {
            font-family: 'Fredoka', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 ml-64">
    <div class="flex">
        <?php include 'admin_sidebar.php'; ?>
        
        <main class=" mt-16 p-6 w-full">
            <?php include 'admin_header.php'; ?>

            <div class="container mx-auto p-4">
                <h1 class="text-2xl font-bold mt-10 mb-4 text-purple-800">Category List</h1>

                <!-- Search and Add Category Section -->
                <div class="flex flex-col sm:flex-row justify-between mb-4 space-y-2 sm:space-y-0">
                    <button id="openModalButton" class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-700">
                        Add Category
                    </button>

                    <form method="GET" class="flex items-center space-x-2">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search categories..." 
                            value="<?= htmlspecialchars($search); ?>"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 w-full sm:w-auto"
                        >
                        <button type="submit" class="bg-purple-800 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                            Search
                        </button>
                    </form>
                </div>

                <!-- Success/Error Message -->
                <?php if(!empty($message)): ?>
                    <div class="mb-4 <?= strpos($message, 'Error') !== false ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?> border p-3 rounded">
                        <?= htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Table Container -->
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse border border-gray-200">
                        <thead class="bg-purple-800 text-white">
                            <tr>
                                <th class="px-2 py-2 text-left text-sm font-medium">Category ID</th>
                                <th class="px-2 py-2 text-left text-sm font-medium">Category Name</th>
                                <th class="px-2 py-2 text-left text-sm font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($category_count > 0):
                                // Reset internal pointer
                                $result->data_seek(0);

                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='hover:bg-gray-100'>";
                                    echo "<td class='px-4 py-2 text- sm text-black'>" . htmlspecialchars($row['cat_id']) . "</td>";
                                    echo "<td class='px-4 py-2 text-sm text-black'>" . htmlspecialchars($row['cat_name']) . "</td>";
                                    echo "<td class='px-4 py-2'>
                                            <button class='text-black hover:underline' onclick='openEditModal({$row['cat_id']}, \"" . htmlspecialchars($row['cat_name']) . "\")'>Edit</button> |
                                            <button class='text-red-600 hover:underline' onclick='openDeleteModal({$row['cat_id']})'>Delete</button>
                                          </td>";
                                    echo "</tr>";
                                }
                            else:
                                echo "<tr><td colspan='3' class='px-4 py-2 text-center text-black'>No categories found.</td></tr>";
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal for Adding Category -->
    <div id="addCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold text-purple-800 mb-4">Add New Category</h2>
            <form action="" method="post">
                <div class="mb-4">
                    <label for="categoryName" class="block text-sm font-medium text-purple-700">Category Name</label>
                    <input type="text" id="categoryName" name="categoryName" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                </div>
                <input type="hidden" name="add" value="1">
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-700">Save</button>
                    <button type="button" id="closeModalButton" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Editing Category -->
    <div id="editCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold text-purple-800 mb-4">Edit Category</h2>
            <form id="editCategoryForm" action="" method="post">
                <div class="mb-4">
                    <label for="editCategoryName" class="block text-sm font-medium text-purple-700">Category Name</label>
                    <input type="text" id="editCategoryName" name="categoryName" class="mt-1 p-2 w-full border border-gray-300 rounded-md" required>
                </div>
                <input type="hidden" name="update" value="1">
                <input type="hidden" id="cat_id" name="cat_id">
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-700">Save Changes</button>
                    <button type="button" id="closeEditModalButton" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Deleting Category -->
    <div id="deleteCategoryModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold text-purple-800 mb-4">Delete Category</h2>
            <p class="text-sm mb-4">Are you sure you want to delete this category?</p>
            <form id="deleteCategoryForm" action="" method="post">
                <input type="hidden" name="delete" value="1">
                <input type="hidden" id="deleteCatId" name="cat_id">
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                    <button type="button" id="closeDeleteModalButton" class="px-4 py-2 bg-gray- 500 text-white rounded hover:bg-gray-600">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Open and close modals
        const openModalButton = document.getElementById('openModalButton');
        const closeModalButton = document.getElementById('closeModalButton');
        const addCategoryModal = document.getElementById('addCategoryModal');
        
        const closeEditModalButton = document.getElementById('closeEditModalButton');
        const editCategoryModal = document.getElementById('editCategoryModal');
        
        const closeDeleteModalButton = document.getElementById('closeDeleteModalButton');
        const deleteCategoryModal = document.getElementById('deleteCategoryModal');

        openModalButton.addEventListener('click', () => {
            addCategoryModal.classList.remove('hidden');
        });

        closeModalButton.addEventListener('click', () => {
            addCategoryModal.classList.add('hidden');
        });

        function openEditModal(catId, catName) {
            document.getElementById('cat_id').value = catId;
            document.getElementById('editCategoryName').value = catName;
            editCategoryModal.classList.remove('hidden');
        }

        closeEditModalButton.addEventListener('click', () => {
            editCategoryModal.classList.add('hidden');
        });

        function openDeleteModal(catId) {
            document.getElementById('deleteCatId').value = catId;
            deleteCategoryModal.classList.remove('hidden');
        }

        closeDeleteModalButton.addEventListener('click', () => {
            deleteCategoryModal.classList.add('hidden');
        });
    </script>
</body>
</html>