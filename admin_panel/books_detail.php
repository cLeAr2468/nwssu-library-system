<?php
include '../admin_panel/bks_dtls_func.php';
include '../admin_panel/side_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'book-blue': '#1e40af',
                        'book-blue-light': '#60a5fa'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="ml-64 min-h-screen p-8 mt-[4%]">
        <!-- Header with Gradient -->
        <div class="mb-8 bg-gray-100 p-6 rounded-xl text-black shadow-lg">
            <h1 class="text-3xl font-bold">Book Details</h1>
            <p class="text-black mt-2">Manage and view comprehensive book information</p>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- Book Details Section - Move to left -->
                <div class="flex-1 p-8">
                    <div class="max-w-3xl">
                        <h2 class="text-2xl font-bold text-[#156295] mb-6 pb-2 border-b border-gray-200">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h2>
                        <div class="space-y-2">
                            <?php
                            $details = [
                                ['Material Type', $book['material_type']],
                                ['Sub Type', $book['sub_type']],
                                ['Author', $book['author']],
                                ['Publisher', $book['publisher']],
                                ['Copy Right', $book['copyright']],
                                ['ISBN', $book['ISBN']],
                                ['Call Number', $book['id']],
                                ['Status', $book['status']],
                                ['Category', $book['category']],
                                ['Copy', $book['copies']],
                                ['Edition', $book['edition']],
                                ['Page Range', $book['page_number']],
                                ['Subject', $book['subject']],
                                ['Summary', $book['summary']],
                                ['Content', $book['content']]
                            ];

                            foreach ($details as $index => $detail): ?>
                                <div class="flex items-center <?php echo $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?> p-4 rounded-lg">
                                    <div class="w-1/3">
                                        <span class="font-medium text-black"><?php echo $detail[0]; ?></span>
                                    </div>
                                    <div class="w-2/3">
                                        <span class="text-gray-700"><?php echo htmlspecialchars($detail[1]); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Cover Photo Section - Move to right -->
                <div class="lg:w-1/4 p-8 bg-gray-50 flex flex-col items-center justify-start border-l border-gray-200">
                    <div class="sticky top-8">
                        <?php if (!empty($book['books_image'])): ?>
                            <img id="book-cover" 
                                src="../uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" 
                                alt="Book Cover" 
                                class="w-48 h-64 object-cover rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
                                onclick="document.getElementById('file-input').click();">
                        <?php else: ?>
                            <div id="missing-cover" 
                                class="w-48 h-64 bg-gradient-to-b from-gray-100 to-gray-200 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                                onclick="document.getElementById('file-input').click();">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-500 font-medium">Click to add cover</span>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="file-input" accept="image/*" class="hidden" onchange="previewImage(event)">
                        <button onclick="uploadImage()" 
                            class="mt-6 w-full px-6 py-3 bg-book-blue text-white rounded-xl hover:bg-book-blue-light transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-book-blue focus:ring-offset-2 flex items-center justify-center gap-2 font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Update Cover
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let originalImageSrc = ""; // Variable to store the original image source
        let newImageSrc = ""; // Variable to store the new image preview
        let isCoverImageMissing = false; // Flag to check if the cover image is missing

        // Store the original image source on page load
        window.onload = function() {
            const bookCover = document.getElementById("book-cover");
            originalImageSrc = bookCover.src;
            isCoverImageMissing = bookCover.src.includes("missing-cover"); // Check if the cover is missing
        };

        // Preview the selected image
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                newImageSrc = reader.result; // Store the new image source
                document.getElementById("book-cover").src = newImageSrc; // Update the image preview
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        // Upload the image and update the preview
        function uploadImage() {
            const fileInput = document.getElementById('file-input');
            // Check if a file is selected
            if (!fileInput.files[0]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No file selected!',
                    text: 'Please select an image file to upload.',
                });
                return;
            }
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this cover?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('books_image', fileInput.files[0]);
                    formData.append('id', <?php echo json_encode($book['id']); ?>);
                    fetch('../admin_panel/books_detail.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message,
                                });
                                document.getElementById("book-cover").src = "../uploaded_file/" + data.fileName; // Update image preview
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'There was an error processing your request.',
                            });
                        });
                } else {
                    // If canceled, reset the image source back to the original
                    document.getElementById("book-cover").src = originalImageSrc;
                    document.getElementById('file-input').value = ""; // Clear the file input
                }
            });
        }
    </script>
</body>
</html>