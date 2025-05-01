<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin_panel/index.php');
    exit();
}
include '../component-library/connect.php';

// Database connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Include sidebar and navigation
include '../admin_panel/sidebar_nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Checkout - Library Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Quick Checkout</h2>
            
            <!-- Quick Checkout Form -->
            <form id="quickCheckoutForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- User ID Input -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                        <input type="text" id="user_id" name="user_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Book ID Input -->
                    <div>
                        <label for="book_id" class="block text-sm font-medium text-gray-700 mb-1">Book ID</label>
                        <input type="text" id="book_id" name="book_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Process Checkout
                    </button>
                </div>
            </form>

            <!-- Result Message -->
            <div id="resultMessage" class="mt-4 hidden">
                <div class="p-4 rounded-md">
                    <p class="text-sm"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('quickCheckoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const userId = document.getElementById('user_id').value;
            const bookId = document.getElementById('book_id').value;
            const resultMessage = document.getElementById('resultMessage');
            const messageText = resultMessage.querySelector('p');
            
            try {
                const response = await fetch('process_circulation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action_type=quick_checkout&user_id=${encodeURIComponent(userId)}&book_id=${encodeURIComponent(bookId)}`
                });
                
                const data = await response.json();
                
                // Show result message
                resultMessage.classList.remove('hidden');
                if (data.status === 'success') {
                    resultMessage.querySelector('div').classList.remove('bg-red-100', 'text-red-700');
                    resultMessage.querySelector('div').classList.add('bg-green-100', 'text-green-700');
                    messageText.textContent = `${data.message} (Return by: ${data.return_date})`;
                    
                    // Clear form
                    document.getElementById('quickCheckoutForm').reset();
                } else {
                    resultMessage.querySelector('div').classList.remove('bg-green-100', 'text-green-700');
                    resultMessage.querySelector('div').classList.add('bg-red-100', 'text-red-700');
                    messageText.textContent = data.message;
                }
            } catch (error) {
                resultMessage.classList.remove('hidden');
                resultMessage.querySelector('div').classList.remove('bg-green-100', 'text-green-700');
                resultMessage.querySelector('div').classList.add('bg-red-100', 'text-red-700');
                messageText.textContent = 'An error occurred while processing the checkout.';
            }
        });
    </script>
</body>
</html> 