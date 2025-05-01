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
</head>
<style>
    body {
    font-family: 'Arial', sans-serif;
}

.vision-mission,
.learning-commons {
    margin-top: 60px;
    text-align: center;
}

.border-box {
    border: 2px solid white;
    padding: 30px;
    margin: 20px auto;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    background-color: #fff;
}

.vision-mission h1,
.learning-commons h1 {
    color: black;
    font-size: 2rem;
    margin-bottom: 20px;
}

.vision-mission h2,
.learning-commons h2 {
    font-size: 1.8rem;
    color: black;
    margin-top: 30px;
    font-weight: bold;
}

.content p,
.learning-commons p,
.content ul li,
.learning-commons ul li {
    color: #333;
    font-size: 18px;
    line-height: 1.6;
}

.divider {
    border-top: 1px solid #ddd;
    margin: 20px auto;
    width: 80%;
}

.content ul,
.learning-commons ul {
    list-style: none;
    padding: 0;
    text-align: left;
    margin: 20px auto;
    max-width: 600px;
}

.content ul li,
.learning-commons ul li {
    font-size: 18px;
    margin-bottom: 10px;
}
</style>
<body>
    <!-- Vision & Mission Section (School) -->
    <section class="vision-mission">
        <div class="container border-box">
            <h1>Vision & Mission</h1>
            <div class="divider"></div>
            <div class="content">
                <h2 class="vision">Vision</h2>
                <p>NwSSU shall lead in providing highly technical and professional education and lifelong learning in the trade, fishery, agriculture, science, education, commerce, engineering, forestry, nautical, and other emerging programs in the digital age. It shall generate cutting-edge technology and undertake sustainable community development in accordance with the university mandates, thrust, and direction.</p>
                <h2 class="mission">Mission</h2>
                <p>The premier technological university in the region providing transformative education where graduates are globally competitive, innovative, and responsive to demands of the changing world.</p>
                <h2 class="mission">Core Values</h2>
                <p>Resilience. Integrity. Service. Excellence.</p>
            </div>
        </div>
    </section>

    <!-- Learning Commons Vision & Mission Section -->
    <section class="learning-commons">
        <div class="container border-box">
            <h1>QUALITY POLICY</h1>
            <div class="divider"></div>
            <p>Northwest Samar State University commits to provide excellent, relevant, and quality instruction, research, extension, and production by adhering to regulatory statutory requirements and pledging to continually improve its Quality Management System, thereby satisfying client needs and producing world-class professionals.</p>
            <h2 class="mission">GOALS AND OBJECTIVES</h2>
            <ul>
                <li>1. Provide instructional media materials and library AVR to meet information needs.</li>
                <li>2. Provide current, accurate quality print and non-print resources to students, faculty and staff and the community as well.</li>
                <li>3. Select appropriate resources to meet the educational goals and objectives of each college.</li>
                <li>4. Support the growing and flexible program offerings of the different colleges by providing updated and relevant resources.</li>
                <li>5. Deliver services and be responsive to the ever-changing needs of the library users.</li>
            </ul>
        </div>
    </section>
    <?php include '../student/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include SweetAlert -->
</body>

</html>