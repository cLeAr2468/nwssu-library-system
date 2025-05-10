<?php include '../component-library/connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Admin Dashboard</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sidebar: {
                            DEFAULT: '#1a1c23',
                            hover: '#2d2f38',
                            active: '#3b7ddd'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <!-- Top Navigation -->
    <nav class="fixed top-0 z-50 w-full bg-gray-900 text-white border-b border-gray-200">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button id="sidebar-toggle" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <span class="sr-only">Open sidebar</span>
                        <i class="lni lni-menu text-xl"></i>
                    </button>
                    <a href="../admin_panel/admin_dashboard.php" class="flex ml-2 md:mr-24">
                        <img src="../images/logo.png" class="h-8 mr-3" alt="NwSSU Logo" />
                        <span class="self-center text-xl font-semibold sm:text-2xl ">NwSSU Library</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ml-3">
                        <div class="relative">
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300" id="user-menu-button">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full" src="../images/logo.png" alt="user photo">
                            </button>
                            <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1" id="user-dropdown">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <span class="block text-sm text-gray-900">Administrator</span>
                                    <span class="block text-sm text-gray-500 truncate">admin@nwssu.edu.ph</span>
                                </div>
                                <a href="../admin_panel/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-sidebar border-r border-gray-200 lg:translate-x-0" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto">
            <ul class="space-y-2 font-medium">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                $is_catalog_section = in_array($current_page, [
                    'display_books.php', 
                    'categories.php', 
                    'books_detail.php',
                    'categbooks.php'
                ]);
                ?>
                <li>
                    <a href="../admin_panel/admin_dashboard.php" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-sidebar-hover group <?= ($current_page == 'admin_dashboard.php') ? 'bg-sidebar-hover' : '' ?>">
                        <i class="lni lni-dashboard text-xl"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <button type="button" class="flex items-center w-full p-2 text-gray-300 transition duration-75 rounded-lg group hover:bg-sidebar-hover <?= $is_catalog_section ? 'bg-sidebar-hover' : '' ?>" id="catalog-dropdown-button">
                        <i class="bi bi-kanban text-xl"></i>
                        <span class="flex-1 ml-3 text-left whitespace-nowrap">Catalog</span>
                        <i class="lni lni-chevron-down transition-transform duration-200" id="catalog-chevron"></i>
                    </button>
                    <ul id="dropdown-catalog" class="hidden py-2 space-y-2">
                        <li>
                            <a href="../admin_panel/display_books.php" class="flex items-center w-full p-2 text-gray-300 transition duration-75 rounded-lg pl-11 group hover:bg-sidebar-hover <?= (in_array($current_page, ['display_books.php', 'books_detail.php'])) ? 'bg-sidebar-active text-white' : '' ?>">
                                <i class="bi bi-book text-sm mr-2"></i>
                                Catalog Items
                            </a>
                        </li>
                        <li>
                            <a href="categories.php" class="flex items-center w-full p-2 text-gray-300 transition duration-75 rounded-lg pl-11 group hover:bg-sidebar-hover <?= (in_array($current_page, ['categories.php', 'categbooks.php'])) ? 'bg-sidebar-active text-white' : '' ?>">
                                <i class="bi bi-tags text-sm mr-2"></i>
                                Categories
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="../admin_panel/circulation.php" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-sidebar-hover group <?= ($current_page == 'circulation.php') ? 'bg-sidebar-hover' : '' ?>">
                        <i class="lni lni-popup text-xl"></i>
                        <span class="ml-3">Circulations</span>
                    </a>
                </li>
                <li>
                    <a href="../admin_panel/Student_list.php" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-sidebar-hover group <?= ($current_page == 'Student_list.php') ? 'bg-sidebar-hover' : '' ?>">
                        <i class="bi bi-person-add text-xl"></i>
                        <span class="ml-3">Manage Users</span>
                    </a>
                </li>
                <li>
                    <a href="../admin_panel/confirm.php" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-sidebar-hover group <?= ($current_page == 'confirm.php') ? 'bg-sidebar-hover' : '' ?>">
                        <i class="bi bi-person-exclamation text-xl"></i>
                        <span class="ml-3">Pending Accounts</span>
                    </a>
                </li>
                <li>
                    <a href="../admin_panel/fine_rec.php" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-sidebar-hover group <?= ($current_page == 'fine_rec.php') ? 'bg-sidebar-hover' : '' ?>">
                        <i class="lni lni-coin text-xl"></i>
                        <span class="ml-3">Fine Records</span>
                    </a>
                </li>
                <li>
                    <a href="../admin_panel/reports.php" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-sidebar-hover group <?= ($current_page == 'reports.php') ? 'bg-sidebar-hover' : '' ?>">
                        <i class="lni lni-files text-xl"></i>
                        <span class="ml-3">Reports</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>


    <script>
        // Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('logo-sidebar');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // User Dropdown Toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        
        userMenuButton.addEventListener('click', () => {
            userDropdown.classList.toggle('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });

        // Catalog Dropdown Toggle
        const catalogDropdownButton = document.getElementById('catalog-dropdown-button');
        const catalogDropdown = document.getElementById('dropdown-catalog');
        const catalogChevron = document.getElementById('catalog-chevron');
        
        catalogDropdownButton.addEventListener('click', () => {
            catalogDropdown.classList.toggle('hidden');
            catalogChevron.classList.toggle('rotate-180');
        });

        // Keep dropdown open if current page is in the dropdown
        const currentPage = '<?php echo $current_page; ?>';
        if (['display_books.php', 'categories.php', 'books_detail.php', 'categbooks.php'].includes(currentPage)) {
            catalogDropdown.classList.remove('hidden');
            catalogChevron.classList.add('rotate-180');
        }
    </script>
</body>
</html>