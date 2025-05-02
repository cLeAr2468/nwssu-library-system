<?php

define('BOOKS_PER_PAGE', 10);
include '../component-library/connect.php';
include '../student/side_navbars.php';
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
$selected_author = isset($_GET['author']) ? $_GET['author'] : '';
// Validate category to prevent SQL injection
$selected_author = htmlspecialchars($selected_author);

// Debug information
echo "<!-- Debug: Selected Author = " . $selected_author . " -->";

// Fetch books data based on selected category with sorting and pagination
$query = $conn->prepare("SELECT * FROM books WHERE author = :author ORDER BY title ASC LIMIT :offset, :limit");
$query->bindValue(':author', $selected_author, PDO::PARAM_STR);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->bindValue(':limit', BOOKS_PER_PAGE, PDO::PARAM_INT);
$query->execute();
$books = $query->fetchAll(PDO::FETCH_ASSOC);

// Debug information
echo "<!-- Debug: Number of books found = " . count($books) . " -->";

// Fetch total number of books for pagination calculation
$total_books_query = $conn->prepare("SELECT COUNT(*) FROM books WHERE author = :author");
$total_books_query->bindValue(':author', $selected_author, PDO::PARAM_STR);
$total_books_query->execute();
$total_books = $total_books_query->fetchColumn();
$total_pages = ceil($total_books / BOOKS_PER_PAGE);
// Fetch distinct categories for display
$categories_query = $conn->query("SELECT DISTINCT author FROM books");
$categories = $categories_query->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50"></body>
<div class="container mx-auto px-[5%] py-8 max-w-7xl">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Search</h2>
            </div>

            <?php if ($selected_author): ?>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 text-blue-600">
                    <span class="mb-2 md:mb-0">
                        Author : <strong><?php echo htmlspecialchars($selected_author); ?></strong>
                        [ <a href="./allauthor" class="hover:underline">All</a> ]
                    </span>
                    <div class="relative w-full md:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="searchInput" placeholder="Search Book"
                            class="pl-10 pr-4 py-2 w-full md:w-72 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            onkeyup="searchBooks()">
                    </div>
                </div>
            <?php endif; ?>

            <div class="hidden md:block overflow-x-auto">
                <table class=" min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 bg-primary text-white text-left text-xs font-medium uppercase tracking-wider w-24"></th>
                            <th class="px-4 py-3 bg-primary text-white text-left text-xs font-medium uppercase tracking-wider">Title</th>
                            <th class="px-4 py-3 bg-primary text-white text-left text-xs font-medium uppercase tracking-wider">Authors/Editors</th>
                            <th class="px-4 py-3 bg-primary text-white text-left text-xs font-medium uppercase tracking-wider">Publisher</th>
                            <th class="px-4 py-3 bg-primary text-white text-left text-xs font-medium uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 bg-primary text-white text-left text-xs font-medium uppercase tracking-wider">Copies</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="bookTable">
                        <?php if (empty($books)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">No books found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex justify-center">
                                            <?php if (!empty($book['books_image'])): ?>
                                                <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" alt="Book Cover" class="w-20 h-28 object-cover border border-gray-200 rounded">
                                            <?php else: ?>
                                                <div class="w-20 h-28 flex items-center justify-center bg-gray-200 text-gray-600 text-xs font-medium border border-gray-300 rounded">
                                                    Book Cover
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="space-y-1">
                                            <a href="./books?id=<?php echo urlencode($book['id']); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium  book-link"
                                                data-title="<?php echo htmlspecialchars($book['title']); ?>">
                                                <?php echo htmlspecialchars($book['title']); ?>
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                <p>Copyright: <?php echo htmlspecialchars($book['copyright']); ?></p>
                                                <p>ISBN: <?php echo htmlspecialchars($book['ISBN']); ?></p>
                                                <p>ID: <?php echo htmlspecialchars($book['id']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="./author?author=<?php echo urlencode($book['author']); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                            <?php echo htmlspecialchars($book['author']); ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="./publisher?publisher=<?php echo urlencode($book['publisher']); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                            <?php echo htmlspecialchars($book['publisher']); ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="<?php echo $book['status'] === 'available' ? 'text-green-600' : 'text-red-600'; ?>">
                                            <?php echo htmlspecialchars($book['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <?php echo htmlspecialchars($book['copies']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Mobile Content -->
            <div class="flex-1 px-4 py-4 md:hidden">
                <div class="space-y-4">
                    <?php if (empty($books)): ?>
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="mt-4 text-gray-500 text-center">No books found</p>
                            <a href="?clear=1" class="mt-2 text-primary hover:text-primary-hover">Clear filters</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($books as $book): ?>
                            <!-- Book Card -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="flex p-4">
                                    <!-- Book Cover -->
                                    <div class="flex-shrink-0">
                                        <!-- Book Cover -->
                                        <div class="relative">
                                            <?php if (!empty($book['books_image'])): ?>
                                                <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>"
                                                    alt="Book Cover"
                                                    class="w-20 h-28 object-cover border border-gray-200 rounded-lg shadow-sm">
                                            <?php else: ?>
                                                <div class="w-20 h-28 bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-medium border border-gray-300 rounded-lg">
                                                    Book Cover
                                                </div>
                                            <?php endif; ?>


                                        </div>

                                        <!-- Material Type (below book cover) -->
                                        <div class="mt-2 text-center">
                                            <span class="text-xs text-gray-500  px-2 py-1 italic">
                                                <?php echo htmlspecialchars($book['material_type']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Book Info -->
                                    <div class="ml-4 flex-1">
                                        <div class="flex flex-col h-full">
                                            <div>
                                                <a href="./books ?id=<?php echo urlencode($book['id']); ?>"
                                                    class="text-base font-semibold text-[#156295] hover:text-primary line-clamp-2 book-link"
                                                    data-title="<?php echo htmlspecialchars($book['title']); ?>">
                                                    <?php echo htmlspecialchars($book['title']); ?>
                                                </a>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    Author : <a href="./author?author=<?php echo urlencode($book['author']); ?>"
                                                        class="text-blue-600 hover:text-primary-hover">
                                                        <?php echo htmlspecialchars($book['author']); ?>
                                                    </a>
                                                </p>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    Publisher : <a href="./publisher?publisher=<?php echo urlencode($book['publisher']); ?>"
                                                        class="text-blue-600 hover:text-primary-hover">
                                                        <?php echo htmlspecialchars($book['publisher']); ?>
                                                    </a>
                                                </p>
                                                <div class="mt-auto">
                                                    <div class="flex flex-wrap gap-y-1 gap-x-4 text-xs text-gray-500 mt-2">
                                                        <div class="flex items-center">
                                                            <?php echo $book['copies'] == 1 ? 'copy' : 'copies'; ?> : <?php echo htmlspecialchars($book['copies']); ?>
                                                        </div>
                                                        <div class="flex items-center">
                                                            Category : <a href="./searchcategory?category=<?php echo urlencode($book['category']); ?>"
                                                                class="text-blue-600 font-medium hover:text-primary-hover mx-auto ml-2">
                                                                <?php echo htmlspecialchars($book['category']); ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="border-t border-gray-100 bg-gray-50/50 px-4 py-3 flex justify-between items-center">
                                    <div class="text-xs text-gray-500">
                                        <span class="<?php echo $book['status'] === 'available' ? 'bg-green-100 text-green-800 ring-green-200' : 'bg-red-100 text-red-800 ring-red-200'; ?> text-xs font-medium px-2 py-1 rounded-full ring-1">
                                            <?php echo htmlspecialchars($book['status']); ?>
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="./books?id=<?php echo urlencode($book['id']); ?>"
                                            class="text-xs bg-primary text-white px-3 py-1.5 rounded-full hover:bg-primary-hover transition-colors book-link"
                                            data-title="<?php echo htmlspecialchars($book['title']); ?>">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Pagination -->
            <div class="flex justify-center py-4">
                <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i; ?>&author=<?php echo urlencode($selected_author); ?>"
                            class="<?= ($i === $page) ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium">
                            <?= $i; ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php include '../student/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function searchBooks() {
        const input = document.getElementById('searchInput').value.toLowerCase();

        // Search in desktop table view
        const tableRows = document.querySelectorAll('#bookTable tr');
        tableRows.forEach(row => {
            if (row.cells && row.cells.length > 1) {
                const title = row.cells[1].textContent.toLowerCase();
                row.style.display = title.includes(input) ? '' : 'none';
            }
        });

        // Search in mobile card view
        const mobileCards = document.querySelectorAll('.md\\:hidden > div');
        mobileCards.forEach(card => {
            const title = card.querySelector('.book-title').textContent.toLowerCase();
            card.style.display = title.includes(input) ? '' : 'none';
        });
    }


    // Add event listeners for book links
    document.querySelectorAll('.book-link').forEach(link => {
        link.addEventListener('click', function(e) {
            const bookTitle = this.dataset.title;
            const formData = new FormData();
            formData.append('activity_detail', bookTitle);

            fetch('./student/record_activity.php', {
                    method: 'POST',
                    body: formData
                })
                .catch(error => {
                    console.error('Error recording activity:', error);
                });
        });
    });
</script>
</body>

</html>