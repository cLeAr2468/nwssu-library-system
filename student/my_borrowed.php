<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session
include '../component-library/connect.php';
include '../student/side_navbars.php';
// Database connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
// Get user_id from session or URL
$user_id = isset($_GET['student_id']) ? $_GET['student_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
if (!$user_id) {
    die('User ID not set.');
}
// Fetch student profile data
$stud = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
$stud->execute([$user_id]);
$student = $stud->fetch(PDO::FETCH_ASSOC);
$profile_image = $student['images'] ?? '../images/prof.jpg'; // Fallback if no image

// Get the view type from URL parameter
$view_type = isset($_GET['view']) ? $_GET['view'] : 'borrowed';
$status_filter = ($view_type === 'overdue') ? "bb.status = 'overdue'" : "(bb.status = 'borrowed' OR bb.status = 'overdue')";

$sql = "
    SELECT bb.*, b.title, b.books_image, b.author, b.publisher, b.copyright, b.ISBN, b.call_no
    FROM borrowed_books bb
    JOIN books b ON bb.book_id = b.id
    WHERE bb.user_id = ? AND $status_filter
    ORDER BY bb.borrowed_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total borrowed books count
$totalBorrowedBooks = count($borrowedBooks);
// Check if the logout button is clicked
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $view_type === 'overdue' ? 'Overdue Books' : 'Borrowed Books'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">
                        <?php echo $view_type === 'overdue' ? 'My Overdue Books' : 'My Borrowed Books'; ?>
                    </h2>
                    <div class="flex space-x-4">
                        <a href="?view=borrowed" class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-book mr-2"></i>Borrowed Books
                        </a>
                        <a href="?view=overdue" class="bg-red-50 text-red-700 px-4 py-2 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fas fa-exclamation-circle mr-2"></i>Overdue Books
                        </a>
                        <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg">
                            <span class="font-medium">Student ID:</span> 
                            <span class="font-bold"><?php echo htmlspecialchars($user_id); ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (empty($borrowedBooks)): ?>
                    <div class="text-center py-10">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-book-open text-6xl"></i>
                        </div>
                        <p class="text-gray-500 text-lg">No books found</p>
                    </div>
                <?php else: ?>
                    <!-- Mobile view (card layout) -->
                    <div class="md:hidden space-y-4">
                        <?php foreach ($borrowedBooks as $book): ?>
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden transition-transform duration-200 hover:scale-[1.02] hover:shadow-xl">
                                <div class="flex p-4 gap-4">
                                    <div class="flex-shrink-0">
                                        <?php if (!empty($book['books_image'])): ?>
                                            <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>"
                                                 alt="Book Cover"
                                                 class="w-20 h-28 object-cover rounded-lg shadow-md border border-gray-100">
                                        <?php else: ?>
                                            <div class="w-20 h-28 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs text-center p-2 border border-gray-200">
                                                Missing Cover Photo
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-800 leading-tight mb-1">
                                                <a href="./books?call_no=<?php echo urlencode($book['call_no']); ?>" class="hover:text-blue-600 transition-colors">
                                                    <?php echo htmlspecialchars($book['title']); ?>
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-600 font-medium mb-0.5"><?php echo htmlspecialchars($book['author']); ?></p>
                                            <p class="text-xs text-gray-500 mb-1"><?php echo htmlspecialchars($book['publisher']); ?></p>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                    <?php echo $book['status'] === 'overdue' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200'; ?>">
                                                    <?php echo $book['status'] === 'overdue' ? 'Overdue' : 'Borrowed'; ?>
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700 border border-blue-100">
                                                    <i class="fa-regular fa-calendar mr-1"></i>
                                                    <?php echo date('M d, Y', strtotime($book['borrowed_date'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-2 text-xs text-gray-400">
                                            <span>ISBN: <span class="text-gray-600"><?php echo htmlspecialchars($book['ISBN']); ?></span></span>
                                            <span>Call No: <span class="text-gray-600"><?php echo htmlspecialchars($book['call_no']); ?></span></span>
                                            <span>Copies: <span class="text-gray-600"><?php echo htmlspecialchars($book['copies']); ?></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Desktop view (table layout) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cover
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Authors/Editors
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Publisher
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Copies
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Borrowed Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($borrowedBooks as $book): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (!empty($book['books_image'])): ?>
                                                <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" 
                                                     alt="Book Cover" 
                                                     class="w-16 h-22 object-cover rounded shadow-sm">
                                            <?php else: ?>
                                                <div class="w-16 h-22 bg-gray-200 rounded flex items-center justify-center text-gray-500 text-xs text-center p-2">
                                                    Missing Cover Photo
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="./books?call_no=<?php echo urlencode($book['call_no']); ?>" class="hover:text-blue-600">
                                                    <?php echo htmlspecialchars($book['title']); ?>
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <div>Copyright: <?php echo htmlspecialchars($book['copyright']); ?></div>
                                                <div>ISBN: <?php echo htmlspecialchars($book['ISBN']); ?></div>
                                                <div>Call No: <?php echo htmlspecialchars($book['call_no']); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['author']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['publisher']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php 
                                                if ($book['status'] === 'overdue') {
                                                    echo 'bg-red-100 text-red-800';
                                                } else {
                                                    echo 'bg-green-100 text-green-800';
                                                }
                                                ?>">
                                                <?php 
                                                if ($book['status'] === 'overdue') {
                                                    echo 'Overdue';
                                                } else {
                                                    echo 'Borrowed';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['copies']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['borrowed_date']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include '../student/footer.php'; ?>
</body>
</html>