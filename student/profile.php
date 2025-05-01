<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();
session_start();
// Database connection
include '../component-library/connect.php';
include '../student/side_navbars.php';
// Check if the student is logged in
$user_id = $_SESSION['user_id'] ?? null;
$profile_image = '../images/prof.jpg'; // Default profile image
if ($user_id) {
    // Fetch student profile data from the database
    $stud = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
    $stud->execute([$user_id]);
    $student = $stud->fetch(PDO::FETCH_ASSOC);
    // Fetch count of reserved, borrowed, and overdue books
    $reservedBooksCount = fetchBookCount($conn, $user_id, 'reserved');
    $borrowedBooksCount = fetchBookCount($conn, $user_id, 'borrowed');
    $overdueBooksCount = fetchBookCount($conn, $user_id, 'overdue');
    // Fallback if no image
    $profile_image = $student['images'] ?? '../images/prof.jpg';
} else {
    // Redirect to login if not logged in
    header('Location: ./login');
    exit();
}
// Handle image upload and update
$message = '';
$message_type = '';
// Check for message parameters in URL (for redirect after upload)
if (isset($_GET['message']) && isset($_GET['message_type'])) {
    $message = $_GET['message'];
    $message_type = $_GET['message_type'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $targetDir = "../uploaded_file/";
    $fileName = uniqid() . '-' . basename($_FILES["profile_image"]["name"]); // Unique file name
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    // Allow certain file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileType, $allowedTypes) && $_FILES["profile_image"]["size"] <= 5000000) { // Limit file size to 5MB
        // Upload file to server
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {
            // Update the database with the new image path
            try {
                $update = $conn->prepare("UPDATE user_info SET images = ? WHERE user_id = ?");
                $update->execute([$fileName, $user_id]);
                // Update session profile image
                $_SESSION['profile_image'] = $fileName;
                $message = "Profile picture updated successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Failed to update profile picture: " . $e->getMessage();
                $message_type = "error";
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
            $message_type = "error";
        }
    } else {
        $message = "Sorry, only JPG, JPEG, PNG, and GIF files are allowed, and the file size must be less than 5MB.";
        $message_type = "error";
    }
    // Redirect to avoid resubmission on refresh
    header("Location: ./profile?message=" . urlencode($message) . "&message_type=" . urlencode($message_type));
    exit();
}
function fetchBookCount($conn, $user_id, $status) {
    $countQuery = $conn->prepare("SELECT COUNT(*) as total FROM reserve_books WHERE user_id = ? AND status = ?");
    $countQuery->execute([$user_id, $status]);
    return $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NwSSU : <?php echo htmlspecialchars($student['user_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Main Content Container -->
    <div class="container mx-auto px-4 py-8">
        <!-- Profile Card -->
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Alert Messages -->
            <?php if (!empty($message)): ?>
                <script>
                    Swal.fire({
                        title: '<?php echo $message_type === 'success' ? 'Success!' : 'Error!'; ?>',
                        text: '<?php echo htmlspecialchars($message); ?>',
                        icon: '<?php echo $message_type === 'success' ? 'success' : 'error'; ?>',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '<?php echo $message_type === 'success' ? '#3085d6' : '#d33'; ?>'
                    });
                </script>
            <?php endif; ?>
            <!-- Profile Content -->
            <div class="flex flex-col md:flex-row">
                <!-- Profile Image Section -->
                <div class="w-full md:w-1/3 bg-secondary p-6 flex flex-col items-center">
                    <div class="relative group cursor-pointer" id="imageContainer">
                        <img 
                            src="./uploaded_file/<?php echo htmlspecialchars($profile_image); ?>" 
                            alt="Student Profile" 
                            class="w-[100px] h-[100px] rounded-full object-cover border-4 border-white shadow-lg transition-transform duration-300 group-hover:scale-105"
                            id="studentImage"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-white text-sm font-medium">Click to change</span>
                        </div>
                    </div>
                    <form method="POST" action="./profile" enctype="multipart/form-data" class="w-full flex flex-col items-center mt-4" id="profileImageForm">
                        <input type="file" name="profile_image" id="fileInput" class="hidden" accept="image/*">
                        <button 
                            type="button" 
                            id="changePhotoBtn" 
                            class="mt-3 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-full text-sm transition-colors duration-200 hidden"
                        >
                            <i class="fas fa-sync-alt mr-2"></i>Update Photo
                        </button>
                    </form>
                </div>
                <!-- Student Information Section -->
                <div class="w-full md:w-2/3 p-6">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">
                        <?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Student ID</p>
                            <p class="font-medium"><?php echo htmlspecialchars($student['user_id']); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Patron Type</p>
                            <p class="font-medium"><?php echo htmlspecialchars($student['patron_type']); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium break-all"><?php echo htmlspecialchars($student['email']); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Address</p>
                            <p class="font-medium"><?php echo htmlspecialchars($student['address']); ?></p>
                        </div>
                    </div>
                    <!-- Status Card -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <p class="text-sm text-gray-500 mb-2">Account Status</p>
                        <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $student['account_status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo htmlspecialchars($student['account_status']); ?>
                        </span>
                    </div>
                    <!-- Records Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Reserved Books Card -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-blue-600">Reserved Books</p>
                                    <p class="text-2xl font-bold text-blue-800"><?php echo htmlspecialchars($reservedBooksCount); ?></p>
                                </div>
                                <a href="./reserved?student_id=<?php echo urlencode($user_id); ?>" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Borrowed Books Card -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-green-600">Borrowed Books</p>
                                    <p class="text-2xl font-bold text-green-800"><?php echo htmlspecialchars($borrowedBooksCount); ?></p>
                                </div>
                                <a href="./borrowed?student_id=<?php echo urlencode($user_id); ?>" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Overdue Books Card -->
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-red-600">Overdue Books</p>
                                    <p class="text-2xl font-bold text-red-800"><?php echo htmlspecialchars($overdueBooksCount); ?></p>
                                </div>
                                <a href="overdue.php?student_id=<?php echo urlencode($user_id); ?>" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Transaction History Button -->
                    <div class="mt-6">
                        <a href="history_rec.php?student_id=<?php echo urlencode($user_id); ?>" 
                           class="w-full bg-secondary hover:bg-gray-900 text-white py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200">
                            <i class="fas fa-history mr-2"></i>
                            View Transaction History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../student/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Store the original image source
    const originalImageSrc = document.getElementById("studentImage").src;
    
    // Image upload handling
    document.getElementById("imageContainer").addEventListener("click", function() {
        document.getElementById("fileInput").click();
    });

    document.getElementById("fileInput").addEventListener("change", function(event) {
        var file = event.target.files[0];
        if (file) {
            // Check file size (5MB limit)
            if (file.size > 5000000) {
                Swal.fire({
                    title: 'File Too Large',
                    text: 'Please select an image smaller than 5MB',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            // Check file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    title: 'Invalid File Type',
                    text: 'Please select a valid image file (JPG, PNG, or GIF)',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("studentImage").src = e.target.result;
            }
            reader.readAsDataURL(file);
            document.getElementById("changePhotoBtn").classList.remove("hidden");
        }
    });

    // Handle the change photo button click
    document.getElementById("changePhotoBtn").addEventListener("click", function() {
        // Show confirmation dialog
        Swal.fire({
            title: 'Update Profile Picture',
            text: 'Are you sure you want to update your profile picture?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Updating Profile Picture',
                    text: 'Please wait while we update your profile picture...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the form
                document.getElementById("profileImageForm").submit();
            } else {
                // If user cancels, restore original image and hide button
                document.getElementById("studentImage").src = originalImageSrc;
                document.getElementById("changePhotoBtn").classList.add("hidden");
                document.getElementById("fileInput").value = ""; // Clear the file input
                
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Profile picture update was cancelled',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });
    </script>
</body>
</html>