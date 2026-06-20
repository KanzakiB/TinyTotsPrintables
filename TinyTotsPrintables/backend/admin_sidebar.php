
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.3);
            border-radius: 4px;
        }
        /* Make the profile image circular */
        .profile-img {
            width: 30px;
            height: 30px;
            border-radius: 30%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button id="sidebar-toggle" class="fixed top-4 left-4 z-50 sm:hidden">
        <i class="fas fa-bars text-2xl"></i>
    </button>

    <div class="flex">
        <!-- Sidebar -->
        <div id="sidebar" class="
            fixed top-0 left-0 h-screen 
            mt-12
            sm:w-16 lg:w-64
            bg-purple-800
            text-white
            py-3
            px-2 
            shadow-lg 
            transition-all 
            duration-300 
            ease-in-out 
            z-40 
            overflow-y-auto 
            sidebar-scroll
        ">
            <!-- Navigation Links -->
            <nav class="space-y-2 mt-8">
                <a href="dashboard.php" class="flex items-center gap-3 py-2 px-3 rounded-md text-white hover:bg-purple-700 hover:text-white transition">
                    <i class="fas fa-home text-xl"></i>
                    <span class="hidden lg:inline">Dashboard</span>
                </a>

                <a href="categorylist.php" class="flex items-center gap-3 py-2 px-3 rounded-md text-white hover:bg-purple-700 hover:text-white transition">
                    <i class="fas fa-list text-xl"></i>
                    <span class="hidden lg:inline">Category List</span>
                </a>

                <a href="productlist.php" class="flex items-center gap-3 py-2 px-3 rounded-md text-white hover:bg-purple-700 hover:text-white transition">
                    <i class="fas fa-boxes text-xl"></i>
                    <span class="hidden lg:inline">Product List</span>
                </a>

                <a href="orderlist.php" class="flex items-center gap-3 py-2 px-3 rounded-md text-white hover:bg-purple-700 hover:text-white transition">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span class="hidden lg:inline">Order List</span>
                </a>
            </nav>

<!-- Profile Section -->
<div class="border-t border-purple-800 pt-4 mb-4">
        <div id="admin-profile-details" class="flex items-center px-3 space-x-3 cursor-pointer rounded-md text-white hover:bg-purple-700 hover:text-white transition">
            <!-- Profile Picture Placeholder -->
            <div class="w-12 h-12 rounded-full bg-purple-800 text-white flex items-center justify-center">
                <span class="text-xl font-bold">
                    <?php 
                    echo strtoupper(substr($_SESSION['admin_email'], 0, 1)); 
                    ?>
                </span>
            </div>

            <!-- Admin Details -->
            <div class="flex-1 hidden lg:block">
                <h3 class="text-sm font-semibold">
                    Admin
                </h3>
                <p class="text-xs text-white">
                    <?php 
                    echo htmlspecialchars($_SESSION['admin_email']); 
                    ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Admin Profile Details Modal -->
    <div id="admin-profile-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-purple-800">Admin Profile</h2>
                <button id="close-profile-modal" class="text-gray-500 hover:text-purple-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="text-center mb-4">
                <div class="w-24 h-24 rounded-full bg-purple-700 text-white flex items-center justify-center mx-auto mb-3">
                    <span class="text-4xl font-bold">
                        <?php echo strtoupper(substr($_SESSION['admin_email'], 0, 1)); ?>
                    </span>
                </div>
                <h3 class="text-xl font-semibold text-purple-800">Admin</h3>
                <p class="text-gray-500">Administrator</p>
            </div>

            <div class="space-y-3">
                <div class="border-b pb-2">
                    <span class="text-sm text-gray-500">Email Address</span>
                    <p class="font-medium text-purple-800">
                        <?php echo htmlspecialchars($_SESSION['admin_email']); ?>
                    </p>
                </div>
                <div class="border-b pb-2">
                    <span class="text-sm text-gray-500">Admin ID</span>
                    <p class="font-medium text-purple-800">
                        <?php echo htmlspecialchars($_SESSION['adminID']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

            <!-- Logout Button -->
            <div class="absolute bottom-4 w-full px-3">
                <button id="logout-button" class="flex items-center gap-3 py-2 px-3 rounded-md text-white hover:bg-purple-700 transition w-full text-left">
                    <i class="fas fa-sign-out-alt text-white text-xl"></i>
                    <span class="hidden lg:inline">Log Out</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const updateProfileModal = document.getElementById('update-profile-modal');

            // Sidebar Toggle
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('sm:w-16');
                sidebar.classList.toggle('lg:w-64');
            });

            // Open Profile Picture Update Modal
            document.getElementById('admin-profile-details').addEventListener('click', () => {
                updateProfileModal.classList.remove('hidden');
            });
        });
    </script>
</body>
</html>