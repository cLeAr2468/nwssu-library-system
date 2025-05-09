<?php
session_start();
include './component-library/connect.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="icon" type="image/png" href="./images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0070f3',
                            foreground: '#ffffff',
                        },
                        muted: {
                            DEFAULT: '#f5f5f5',
                            foreground: '#6b7280',
                        },
                        background: '#ffffff',
                        foreground: '#000000',
                        border: '#e5e7eb',
                    },
                    container: {
                        center: true,
                        padding: '2rem',
                        screens: {
                            '2xl': '1400px',
                        },
                    },
                },
            },
        }

        // Function to check login status and handle register button
        function handleRegisterButton(event) {
            <?php if(isset($_SESSION['user_id'])): ?>
                event.preventDefault();
                window.location.href = './student/home';
            <?php endif; ?>
        }
    </script>

    <style>
        /* Additional styles for mobile menu */
        .mobile-menu {
            display: none;
        }
        .mobile-menu.active {
            display: flex;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full border-b bg-gray-900 md:px-2 text-white">
        <div class="container mx-auto flex h-16 items-center justify-between space-x-6">
            <div class="flex items-center gap-2">
                <!-- BookOpen Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                <span class="md:hidden text-xl font-bold">Library</span>
                <span class="hidden md:block text-xl font-bold">Nwssu Sj Library</span>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center ml-[46%] xl:ml-[46%] lg:ml-[23%] gap-6 lg:gap-5">
                <a href="#home" class="text-lg font-medium py-2 hover:text-white/80">Home</a>
                <a href="#features" class="text-lg font-medium hover:text-white/80">Features</a>
                <a href="#guides" class="text-lg font-medium hover:text-white/80">Guides</a>
                <a href="#contact" class="text-lg font-medium hover:text-white/80">Contact Us</a>
            </nav>

            <div class="flex items-center gap-4">
                <!-- Desktop Buttons -->
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="./login" class="hidden md:flex items-center justify-center rounded-md bg-transparent px-4 py-2 text-sm font-medium text-white hover:bg-white hover:text-black font-bold">
                        Log in
                    </a>
                    <a href="./register" class="hidden md:flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-black hover:bg-gray-900 hover:text-white font-bold">
                        Register
                    </a>
                <?php else: ?>
                    <a href="./home" class="hidden md:flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-black hover:bg-gray-900 hover:text-white font-bold">
                        Go to Dashboard
                    </a>
                <?php endif; ?>
                
                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="md:hidden flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="mobile-menu flex-col w-full py-4 px-4 bg-gray-900 border-t border-gray-800">
            <a href="#home" class="text-lg font-medium py-2 hover:text-white/80">Home</a>
            <a href="#features" class="text-lg font-medium py-2 hover:text-white/80">Features</a>
            <a href="#guides" class="text-lg font-medium py-2 hover:text-white/80">Guides</a>
            <a href="#contact" class="text-lg font-medium py-2 hover:text-white/80">Contact Us</a>
            <div class="flex flex-col gap-2 mt-4">
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="./login" class="flex items-center justify-center rounded-md border border-white bg-transparent px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 font-bold">
                        Log in
                    </a>
                    <a href="./register" class="flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-black hover:bg-gray-200 font-bold">
                        Register
                    </a>
                <?php else: ?>
                    <a href="./home" class="flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-black hover:bg-gray-200 font-bold">
                        Go to Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <section id="home" class="w-full py-[10%] md:py-[10%] lg:py-[7%] xl:py-[7%] md:px-6">
            <div class="container px-4 md:px-6 mx-auto">
                <div class="grid gap-6 lg:grid-cols-2 lg:gap-12 xl:grid-cols-2">
                    <div class="flex flex-col justify-center space-y-4">
                        <div class="space-y-2">
                            <!-- Badge component replaced with span -->
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-gray-900 text-primary-foreground hover:bg-primary/80">
                                New Release
                            </span>
                            <h1 class="text-3xl font-bold tracking-tighter sm:text-5xl xl:text-6xl/none">
                                Welcome to NwSSU Library Management System
                            </h1>
                            <p class="max-w-[600px] text-muted-foreground md:text-xl">
                                Streamline your library operations, engage readers, and gain valuable insights with our all-in-one
                                library management system.
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 min-[400px]:flex-row">
                            <!-- Button component replaced with button element -->
                            <?php if(!isset($_SESSION['user_id'])): ?>
                                <a href="./login" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-primary-foreground hover:bg-gray-900/90 h-11 px-8 gap-1">
                                    Get Started! 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                        <path d="M5 12h14"></path>
                                        <path d="m12 5 7 7-7 7"></path>
                                    </svg>
                                </a>
                            <?php else: ?>
                                <a href="./home" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-primary-foreground hover:bg-gray-900/90 h-11 px-8 gap-1">
                                    Go to Dashboard
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                        <path d="M5 12h14"></path>
                                        <path d="m12 5 7 7-7 7"></path>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center justify-center">
                        <div class="relative w-full aspect-video overflow-hidden rounded-xl">
                            <!-- Image source changed to a direct path -->
                            <img src="./images/illustration.jpg" alt="Library Management System" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="w-full py-12 md:py-12 lg:py-12 xl:py-14 bg-muted/40">
            <div class="container px-4 md:px-6 mx-auto">
                <div class="flex flex-col items-center justify-center space-y-4 text-center">
                    <div class="space-y-2">
                        <h1 class="font-extrabold text-[50px] mb-6">FEATURES</h1>
                        <h2 class="text-3xl font-bold tracking-tighter md:text-4xl/tight">
                            Everything You Need to Run Your Library
                        </h2>
                        <p class="mx-auto max-w-[700px] text-muted-foreground md:text-xl">
                            Our comprehensive solution helps you manage books, patrons, and operations efficiently.
                        </p>
                    </div>
                </div>
                <div class="mx-auto grid max-w-5xl grid-cols-1 gap-6 py-12 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Card 1: Smart Catalog -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <!-- Search Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-900 mb-2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Smart Catalog</h3>
                            <p class="text-sm text-muted-foreground">Powerful search and filtering to find any book in seconds.</p>
                        </div>
                    </div>
        
                    <!-- Card 2: Automated Checkouts -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <!-- Clock Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-900 mb-2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <h3 class="text-lg font-semibold">Automated Checkouts</h3>
                            <p class="text-sm text-muted-foreground">Streamline borrowing and returns with automated processes.</p>
                        </div>
                    </div>
        
                    <!-- Card 3: Insightful Analytics -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <!-- BarChart3 Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-900 mb-2">
                                <path d="M3 3v18h18"></path>
                                <path d="M18 17V9"></path>
                                <path d="M13 17V5"></path>
                                <path d="M8 17v-3"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Insightful Analytics</h3>
                            <p class="text-sm text-muted-foreground">Track usage patterns and make data-driven decisions.</p>
                        </div>
                    </div>
        
                    <!-- Card 4: Smart Notifications -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <!-- Bell Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-900 mb-2">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Smart Notifications</h3>
                            <p class="text-sm text-muted-foreground">Automated reminders for due dates and reservations.</p>
                        </div>
                    </div>
        
                    <!-- Card 5: Patron Management -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <!-- Users Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-900 mb-2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Patron Management</h3>
                            <p class="text-sm text-muted-foreground">Easily manage member accounts, history, and preferences.</p>
                        </div>
                    </div>
        
                    <!-- Card 6: Digital Reading -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="flex flex-col space-y-1.5 p-6">
                            <!-- BookOpen Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-900 mb-2">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Digital Reading</h3>
                            <p class="text-sm text-muted-foreground">Offer e-books and digital resources to your patrons.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="guides" class="w-full py-12 md:py-24 lg:py-32 bg-muted/50">
      <div class="container px-4 md:px-6">
        <div class="flex flex-col items-center justify-center space-y-4 text-center">
          <div class="space-y-2">
            <div class="inline-block rounded-lg bg-gray-900 px-3 py-1 text-sm text-primary-foreground">
              How It Works
            </div>
            <h2 class="text-3xl font-bold tracking-tighter md:text-4xl/tight">
              Simple Three-Step Process
            </h2>
            <p class="max-w-[900px] text-muted-foreground md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed">
              Get up and running in minutes with our straightforward onboarding process.
            </p>
          </div>
        </div>
        <div class="mx-auto grid max-w-5xl gap-6 py-12 md:grid-cols-3">
          <!-- Step 1 -->
          <div class="relative flex flex-col items-center text-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-900 text-lg font-bold text-primary-foreground">
              01
            </div>
            <div class="mt-4 space-y-2">
              <h3 class="text-xl font-bold">Create Account</h3>
              <p class="text-muted-foreground">Sign up for an account and verify your email address.</p>
            </div>
            <div class="absolute left-[calc(50%+3rem)] top-6 hidden h-[2px] w-[calc(100%-6rem)] bg-border md:block"></div>
          </div>

          <!-- Step 2 -->
          <div class="relative flex flex-col items-center text-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-900 text-lg font-bold text-primary-foreground">
              02
            </div>
            <div class="mt-4 space-y-2">
              <h3 class="text-xl font-bold">Configure Settings</h3>
              <p class="text-muted-foreground">Set up your workspace and invite your team members.</p>
            </div>
            <div class="absolute left-[calc(50%+3rem)] top-6 hidden h-[2px] w-[calc(100%-6rem)] bg-border md:block"></div>
          </div>

          <!-- Step 3 -->
          <div class="relative flex flex-col items-center text-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-900 text-lg font-bold text-primary-foreground">
              03
            </div>
            <div class="mt-4 space-y-2">
              <h3 class="text-xl font-bold">Start Working</h3>
              <p class="text-muted-foreground">Begin using the platform and boost your productivity.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    </main>
    <!-- Footer -->
     <div id="contact">
    <?php include './student/footer.php'; ?>

    </div>


    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
            });
        });
    </script>
</body>
</html> 