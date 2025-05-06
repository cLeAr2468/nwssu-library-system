<?php

define('BOOKS_PER_PAGE', 10);
include '../component-library/connect.php';
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * BOOKS_PER_PAGE;
// Get the selected category from URL parameter
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
// Validate category to prevent SQL injection
$selected_category = htmlspecialchars($selected_category);
// Fetch books data based on selected category with sorting and pagination
$query = $conn->prepare("SELECT * FROM books WHERE category = :category ORDER BY title ASC LIMIT :offset, :limit");
$query->bindValue(':category', $selected_category, PDO::PARAM_STR);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->bindValue(':limit', BOOKS_PER_PAGE, PDO::PARAM_INT);
$query->execute();
$books = $query->fetchAll(PDO::FETCH_ASSOC);
// Fetch total number of books for pagination calculation
$total_books_query = $conn->prepare("SELECT COUNT(*) FROM books WHERE category = :category");
$total_books_query->bindValue(':category', $selected_category, PDO::PARAM_STR);
$total_books_query->execute();
$total_books = $total_books_query->fetchColumn();
$total_pages = ceil($total_books / BOOKS_PER_PAGE);
// Fetch distinct categories for display
$categories_query = $conn->query("SELECT DISTINCT category FROM books");
$categories = $categories_query->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books in <?php echo htmlspecialchars($selected_category); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php include '../admin_panel/side_nav.php'; ?>
    <div class="ml-64 min-h-screen p-8 mt-[4%]">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">
                    <?php echo htmlspecialchars($selected_category); ?> Books
                </h1>
                <a href="categories.php" class="flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Categories
                </a>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="relative w-full md:w-96">
                    <input type="text" id="searchInput" 
                        onkeyup="searchBooks()"
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Search books...">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="text-sm text-gray-500">
                    Total Books: <span class="font-medium text-gray-900"><?php echo $total_books; ?></span>
                </div>
            </div>
        </div>

        <!-- Books Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="booksTable">
                    <thead>
                        <tr class="bg-primary-600">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Book Cover</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Author</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Publisher</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Copies</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($books as $book): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if (!empty($book['books_image'])): ?>
                                    <img src="../uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" 
                                         alt="Book Cover" 
                                         class="h-20 w-16 object-cover rounded">
                                <?php else: ?>
                                    <div class="h-20 w-16 bg-gray-100 flex items-center justify-center rounded">
                                        <span class="text-gray-400 text-xs">No Image</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <a href="./books?id=<?php echo urlencode($book['id']); ?>" 
                                       class="font-medium text-[#156295] font-bold hover:text-blue-800">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </a>
                                    <p class="text-gray-500 mt-1">ISBN: <?php echo htmlspecialchars($book['ISBN']); ?></p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo htmlspecialchars($book['author']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo htmlspecialchars($book['publisher']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $book['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo htmlspecialchars($book['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo htmlspecialchars($book['copies']); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="mt-6 flex justify-center">
            <nav class="flex items-center gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i; ?>&category=<?php echo urlencode($selected_category); ?>"
                       class="<?= ($i === $page) ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'; ?> 
                              px-4 py-2 text-sm font-medium rounded border border-gray-200">
                        <?= $i; ?>
                    </a>
                <?php endfor; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function searchBooks() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const table = document.getElementById('booksTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const titleCell = rows[i].getElementsByTagName('td')[1];
            if (titleCell) {
                const title = titleCell.textContent || titleCell.innerText;
                if (title.toLowerCase().indexOf(input) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    }
    </script>
</body>
</html>