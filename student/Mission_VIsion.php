<?php
session_start();

include '../component-library/connect.php';
include '../student/side_navbars.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vision & Mission</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans">
    <!-- Vision & Mission Section (School) -->
    <section class="mt-16 text-center">
        <div class="max-w-4xl mx-auto border-2 border-white p-8 my-5 rounded-lg shadow-md bg-white">
            <div class="border-t border-gray-200 my-5 w-4/5 mx-auto"></div>
            <div>
                <h2 class="text-3xl font-bold text-black mb-4">Vision</h2>
                <p class="text-gray-700 text-lg leading-relaxed">
                    The San Jorge Campus Library is envisioned to serve and provide both print and non-print materials to foster quality education to society where citizen are competent, skilled dignified and community oriented.
                </p>
                <h2 class="text-3xl font-bold text-black mt-8 mb-4">Mission</h2>
                <p class="text-gray-700 text-lg leading-relaxed">
                    The San Jorge Campus Library aims to continually enrich the print and non-print collection to provide excellent and efficient information service in fostering the community learning and research.
                </p>
            </div>
        </div>
    </section>

    <!-- Learning Commons Vision & Mission Section -->
    <section class="text-center">
        <div class="max-w-4xl mx-auto border-2 border-white p-8 my-5 rounded-lg shadow-md bg-white">
            <h1 class="text-3xl font-bold text-black mb-4">QUALITY POLICY</h1>
            <div class="border-t border-gray-200 my-5 w-4/5 mx-auto"></div>
            <h2 class="text-3xl font-bold text-black mt-8 mb-4">LIBRARY GOALS AND OBJECTIVES</h2>
            <ul class="list-none p-0 text-left mx-auto max-w-2xl my-5">
                <li class="text-gray-700 text-lg mb-3">
                    1. NwSSU library will have reliable and equitable electronic access to information resources through provision of instructional media materials and library AVR meet information needs.
                </li>
                <li class="text-gray-700 text-lg mb-3">
                    2. To provide current, accurate quality print and non-print resources for students, faculty and staff the community as well.
                </li>
                <li class="text-gray-700 text-lg mb-3">
                    3. To select appropriate resources to meet the educational goals and staff and objectives of each college.
                </li>
                <li class="text-gray-700 text-lg mb-3">
                    4. To support the growing and flexible programs of the college by providing updated and relevant resources.
                </li>
                <li class="text-gray-700 text-lg mb-3">
                    5. To deliver service and be responsible to the ever-changing needs of the client.
                </li>
            </ul>
        </div>
    </section>
    <?php include '../student/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>