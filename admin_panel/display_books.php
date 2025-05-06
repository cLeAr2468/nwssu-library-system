<?php
include '../admin_panel/ins-dis-function.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NwSSU : Catalogs</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Book Catalog</h1>
                    <p class="text-gray-600">Manage and view all library materials</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center" onclick="openModal()">
                        <i class="lni lni-plus mr-2"></i> Add Book
                    </button>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 mb-6">
                <form id="searchForm" class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4" method="GET" action="">
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <i class="lni lni-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchQuery" name="query" placeholder="Search books..." value="<?php echo htmlspecialchars($search_query); ?>" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
                    <div class="flex items-center space-x-2">
                        <select id="searchCategory" name="category" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="all" <?= $search_category === 'all' ? 'selected' : ''; ?>>All Fields</option>
                        <option value="call_no" <?= $search_category === 'call_no' ? 'selected' : ''; ?>>Call No</option>
                        <option value="title" <?= $search_category === 'title' ? 'selected' : ''; ?>>Title</option>
                        <option value="author" <?= $search_category === 'author' ? 'selected' : ''; ?>>Author</option>
                        <option value="publisher" <?= $search_category === 'publisher' ? 'selected' : ''; ?>>Publisher</option>
                        <option value="publish_date" <?= $search_category === 'publish_date' ? 'selected' : ''; ?>>Publish Date</option>
                        <option value="category" <?= $search_category === 'category' ? 'selected' : ''; ?>>Category</option>
                        <option value="status" <?= $search_category === 'status' ? 'selected' : ''; ?>>Status</option>
                        <option value="ISBN" <?= $search_category === 'ISBN' ? 'selected' : ''; ?>>ISBN</option>
                        <option value="edition" <?= $search_category === 'edition' ? 'selected' : ''; ?>>Edition</option>
                        <option value="subject" <?= $search_category === 'subject' ? 'selected' : ''; ?>>Subject</option>
                        <option value="summary" <?= $search_category === 'summary' ? 'selected' : ''; ?>>Summary</option>
                    </select>
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="lni lni-search"></i>
                    </button>
                    </div>
                </form>
            </div>

            <!-- Books Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-primary-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Book Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Publisher</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Copies</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($books as $book): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-[#156295] font-bold"><?php echo htmlspecialchars($book['title']); ?></div>
                                        <div class="text-sm text-gray-500">
                                            <div>ISBN: <?php echo htmlspecialchars($book['ISBN']); ?></div>
                                            <div>Call No: <?php echo htmlspecialchars($book['call_no']); ?></div>
                                            <div>Copyright: <?php echo htmlspecialchars($book['copyright']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($book['author']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($book['publisher']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $book['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo htmlspecialchars($book['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <?php echo htmlspecialchars($book['copies']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($book['material_type']); ?>
                            </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <a href="books_detail.php?id=<?php echo urlencode($book['id']); ?>" class="text-primary-600 hover:text-primary-900" title="View Details">
                                                <i class="lni lni-eye"></i>
                                            </a>
                                            <button onclick="populateEditForm(<?php echo htmlspecialchars(json_encode($book)); ?>)" class="text-blue-600 hover:text-blue-900" title="Edit Book">
                                                <i class="lni lni-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i; ?>&query=<?= urlencode($search_query); ?>&category=<?= urlencode($search_category); ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?= ($i === $page) ? 'text-primary-600 bg-primary-50 border-primary-500' : 'text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i; ?>
                        </a>
                <?php endfor; ?>
        </nav>
            </div>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div id="addBookModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative min-h-screen md:flex md:items-center md:justify-center p-4">
            <div class="bg-white w-full max-w-4xl mx-auto rounded-lg shadow-lg relative">
                <div class="p-6">
                    <div class="flex justify-between items-center pb-3 border-b">
                        <h3 class="text-xl font-semibold text-gray-900" id="addBookModalLabel">Add New Book</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="lni lni-close text-xl"></i>
                        </button>
                </div> 
                    <div class="mt-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <form id="addBookForm" method="POST" action="process_book.php" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" id="book_id" name="book_id" value="">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Material Type</label>
                                    <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="material_type" name="material_type" required>
                                    <option value="" disabled selected>Select Type</option> 
                                    <option value="Periodical">Periodical</option>
                                    <option value="Book">Book</option>
                                    <option value="E-Book">E-Book</option>
                                    <option value="Journal">Journal</option>
                                    <option value="Unpublished Material">Unpublished Material</option>
                                    <option value="Audio Visual Material">Audio Visual Material</option>
                                </select>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ISSN</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="issn" name="issn" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sub Type</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="sub_type" name="sub_type" required>
                                    <option value="" disabled selected>Select Sub Type</option>
                                    <option value="Reference">Reference</option>
                                    <option value="Fiction">Fiction</option>
                                    <option value="Reserve">Reserve</option>
                                    <option value="Thesis">Thesis</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Call No</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="call_no" name="call_no" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="category" name="category" required>
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="BAT">BAT</option>
                                    <option value="BEED">BEED</option>
                                    <option value="BSCRIM">BSCRIM</option>
                                    <option value="BSA">BSA</option>
                                    <option value="BSA-Animal Science">BSA-Animal Science</option>
                                    <option value="BSA-Horticulture">BSA-Horticulture</option>
                                    <option value="BSABE">BSABE</option>
                                    <option value="BSF">BSF</option>
                                    <option value="BSF-Fishery">BSF-Fishery</option>
                                    <option value="BSIT">BSIT</option>
                                    <option value="BSCS">BSCS</option>
                                    <option value="BSED">BSED</option>
                                    <option value="BSSW">BSSW</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="title" name="title" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Author</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="author" name="author" required>
                        </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Publisher</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="publisher" name="publisher" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="status" name="status" required>
                                    <option value="" disabled selected>Select Status</option>
                                        <option value="available">Available</option>
                                        <option value="not available">Not Available</option>
                                    <option value="Lost">Lost</option>
                                </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Copies</label>
                                    <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="copies" name="copies" min="1" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ISBN</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="ISBN" name="ISBN" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Edition</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="edition" name="edition" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Copyright</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="copyright" name="copyright" required>
                        </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Page Number</label>
                                    <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="page_number" name="page_number" min="1" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="subject" name="subject" required>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date Acquired</label>
                                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="date_acquired" name="date_acquired" required>
                        </div>
                            </div>
                            
                            <div class="space-y-4 mt-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Summary</label>
                                    <textarea class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="summary" name="summary" rows="3"></textarea>
                            </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Content</label>
                                    <textarea class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" id="content" name="content" rows="3"></textarea>
                        </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Book Cover</label>
                                    <input type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" id="books_image" name="books_image" accept="image/*">
                                </div>
                            </div>
                        </form>
                        </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 pt-3 border-t">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" form="addBookForm" id="submitButton" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            Save Book
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let originalValues = {};

        function checkFormChanges() {
            const form = document.getElementById('addBookForm');
            const submitButton = document.getElementById('submitButton');
            const bookId = document.getElementById('book_id').value;

            // Only check for changes if we're in edit mode
            if (bookId) {
                let hasChanges = false;
                
                // Check each form field against original values
                const fields = [
                    'material_type', 'issn', 'sub_type', 'call_no', 'category',
                    'title', 'author', 'publisher', 'status', 'copies', 'ISBN',
                    'edition', 'copyright', 'page_number', 'subject', 'date_acquired',
                    'summary', 'content'
                ];

                for (const field of fields) {
                    const currentValue = form[field] ? form[field].value : '';
                    if (currentValue !== originalValues[field]) {
                        hasChanges = true;
                        break;
                    }
                }

                // Enable/disable submit button based on changes
                submitButton.disabled = !hasChanges;
                submitButton.classList.toggle('opacity-50', !hasChanges);
                submitButton.classList.toggle('cursor-not-allowed', !hasChanges);
            }
        }

        function populateEditForm(book) {
            openModal();
            // Set modal for Edit mode
            document.getElementById('addBookModalLabel').textContent = 'Update Book';
            document.getElementById('submitButton').textContent = 'Update Book';
            
            // Hide image upload field for updates
            document.getElementById('books_image').parentElement.style.display = 'none';
            
            // Set the book ID for update
            document.getElementById('book_id').value = book.id;
            
            // Store original values and populate form fields
            const form = document.getElementById('addBookForm');
            form.material_type.value = book.material_type || '';
            form.issn.value = book.issn || '';
            form.sub_type.value = book.sub_type || '';
            form.call_no.value = book.call_no || '';
            form.category.value = book.category || '';
            form.title.value = book.title || '';
            form.author.value = book.author || '';
            form.publisher.value = book.publisher || '';
            form.status.value = book.status || '';
            form.copies.value = book.copies || '';
            form.ISBN.value = book.ISBN || '';
            form.edition.value = book.edition || '';
            form.copyright.value = book.copyright || '';
            form.page_number.value = book.page_number || '';
            form.subject.value = book.subject || '';
            form.date_acquired.value = book.date_acquired || '';
            form.summary.value = book.summary || '';
            form.content.value = book.content || '';

            // Store original values for comparison
            originalValues = {
                material_type: book.material_type || '',
                issn: book.issn || '',
                sub_type: book.sub_type || '',
                call_no: book.call_no || '',
                category: book.category || '',
                title: book.title || '',
                author: book.author || '',
                publisher: book.publisher || '',
                status: book.status || '',
                copies: book.copies || '',
                ISBN: book.ISBN || '',
                edition: book.edition || '',
                copyright: book.copyright || '',
                page_number: book.page_number || '',
                subject: book.subject || '',
                date_acquired: book.date_acquired || '',
                summary: book.summary || '',
                content: book.content || ''
            };

            // Initially disable update button
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');

            // Add change event listeners to all form fields
            const formFields = form.querySelectorAll('input, select, textarea');
            formFields.forEach(field => {
                field.addEventListener('input', checkFormChanges);
                field.addEventListener('change', checkFormChanges);
            });
        }

        function openModal() {
            const modal = document.getElementById('addBookModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            resetModal();
            
            // Reset original values and enable submit button for new entries
            originalValues = {};
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            
            document.getElementById('addBookModalLabel').textContent = 'Add New Book';
            document.getElementById('submitButton').textContent = 'Save Book';
            document.getElementById('books_image').parentElement.style.display = 'block';
        }

        // Function to close modal
        function closeModal() {
            const modal = document.getElementById('addBookModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            resetModal();
        }

        // Function to reset the modal form
        function resetModal() {
            document.getElementById('addBookForm').reset();
            document.getElementById('book_id').value = '';
        }

        // Handle form submission
        document.getElementById('addBookForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const isUpdate = formData.get('book_id') !== '';
            
            // Show loading state
            const submitButton = document.getElementById('submitButton');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
            
            fetch('display_books.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || `Failed to ${isUpdate ? 'update' : 'save'} book`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: `An error occurred while ${isUpdate ? 'updating' : 'saving'} the book`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Reset button state
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });

        // Close modal when clicking outside
        document.getElementById('addBookModal').addEventListener('click', function(e) {
            if (e.target.id === 'addBookModal') {
                closeModal();
            }
        });

        // Prevent modal close when clicking inside the modal content
        document.querySelector('#addBookModal .bg-white').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Handle escape key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('addBookModal').classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>
</body>
</html>