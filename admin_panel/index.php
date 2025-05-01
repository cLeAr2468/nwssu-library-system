<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../admin_panel/admin_dashboard.php');
    exit();
}

$alert_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars ($_POST['username']);
    $password = htmlspecialchars($_POST['pass']);

    if ($username == 'admin' && $password == 'admin') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ../admin_panel/admin_dashboard.php');
        exit();
    } else {
        $alert_message = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 ml-2">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="../index.php" class="flex items-center">
                        <i class="fas fa-book-open text-blue-600 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-800">NwSSu</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Login Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Card Header with Background -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-8 text-center">
                    <h1 class="text-2xl font-bold text-white">Admin Login</h1>
                    <p class="text-blue-100 mt-1">Access the library management system</p>
                </div>
                
                <!-- Card Body -->
                <div class="p-6">
                    <form action="" method="POST" class="space-y-6">
                        <!-- Username Field -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="username" name="username" required 
                                    class="pl-10 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Enter your username">
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="pass" name="pass" required 
                                    class="pl-10 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-10"
                                    placeholder="Enter your password">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" id="toggle-password" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                        <i class="fas fa-eye-slash" id="toggle-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div>
                            <button type="submit" 
                                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Welcome Text -->
            <div class="mt-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800">Welcome to Our Library</h2>
                <p class="mt-2 text-gray-600">Northwest Samar State University Library Management System</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-4 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
            <p>&copy; <?php echo date('Y'); ?> NwSSu Library Management System. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Toggle password visibility
        const passInput = document.getElementById('pass');
        const togglePassword = document.getElementById('toggle-password');
        const toggleIcon = document.getElementById('toggle-icon');

        togglePassword.addEventListener('click', function() {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);

            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
        });
    </script>

    <!-- Display SweetAlert message if there is an error -->
    <?php if ($alert_message != ''): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '<?php echo $alert_message; ?>',
                confirmButtonColor: '#4F46E5'
            });
        </script>
    <?php endif; ?>

    <?php include '../component-library/alert.php'; ?>
</body>
</html>
