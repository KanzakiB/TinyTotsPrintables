<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Fredoka', sans-serif;
        }
    </style>
</head>
        <!-- Main Content -->
        <div class="flex-grow md:ml-64 lg:ml-72">
            <!-- Header -->
            <nav class="bg-white text-purple-800 fixed top-0 left-0 w-full h-20 z-50 shadow-lg flex items-center justify-between px-4">
                <div class="logo">
                    <img src="http://localhost/TinyTotsPrintables/backend/images/logoicon1.png" id="imglogo" alt="Company logo with a stylized icon and company name" class="h-12">
                </div>
                <div class="flex items-center space-x-6">
                    <!-- Inbox Section -->
                    <div class="relative">
                        <button class="text-black hover:text-gray-700 focus:outline-none" id="inbox-button">
                            <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M20 2H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm-1 17H5V6h14v13zM7 10h10v2H7v-2z" />
                            </svg>
                        </button>
                        <!-- Inbox Dropdown -->
                        <div class="absolute right-8 mt-2 w-64 bg-white text-gray-800 rounded-md shadow-lg z-10 hidden" id="inbox-menu">
                            <div class="p-4">
                                <h3 class="text-lg font-bold mb-2">Inbox</h3>
                                <p class="text-center text-sm text-gray-500">No new messages</p>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inboxButton = document.getElementById('inbox-button');
            const inboxMenu = document.getElementById('inbox-menu');

            // Toggle Inbox Menu
            inboxButton.addEventListener('click', () => {
                inboxMenu.classList.toggle('hidden');
            });
        });
    </script>
</body>
</html>