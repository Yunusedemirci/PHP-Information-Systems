<?php 
require_once "backend.php";
$database = new database();
$db = $database->getConnection();
$userController = new userController();
$userController->studentControl();

if (isset($_POST['logout'])) {
  $userController->logout();
}



?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard - NiceAdmin Bootstrap Template</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>

    <!-- Option 1: Include in HTML -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- Template Main CSS File -->
    <link href="adminpanel.css" rel="stylesheet">

    <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Sep 18 2023 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>



    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="studentpanel.php" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block">YavuzlarStudent</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li><!-- End Search Icon-->
                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="admin.jpg" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2"><?php 
            echo $_SESSION["user"]["name"];
            
            ?></span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <?php 
                echo '<a  class="dropdown-item d-flex align-items-center" href="studentprofile.php?id='.$_SESSION["user"]["id"].'"> <i class="bi bi-key-fill"></i>Change Password </a>' ;
              ?>
                        </li>
                        <form action="#" method="post">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <button type="submit" name="logout"
                                        style="border:none; background-color:transparent;"> <span>Sign Out</span>
                                    </button>
                                </a>
                            </li>
                        </form>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link " href="studentpanel.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-heading">Pages</li>


            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-people-fill"></i><span>My Lessons</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="studentlessonscore.php">
                            <i class="bi bi-circle"></i><span>My Lessons</span>
                        </a>
                    </li>
                </ul>
            </li><!-- End Forms Nav -->






    </aside><!-- End Sidebar-->

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="teacherpanel.php">Home</a></li>
                    <li class="breadcrumb-item">Exam</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section profile">



            <div class="col-xl-12">

                <div class="card">
                    <div class="card-body pt-3">
                        <!-- Bordered Tabs -->
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#profile-overview">Lesson Score</button>
                            </li>


                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <?php 
                    $examController = new examScoreController();
                    $exams = $examController->studentExamScore($_SESSION["user"]["id"]);
                    echo '<table class="table table-striped">
                    <thead>
                    <tr>
                      <th scope="col">Student Name</th>
                      <th scope="col">Class Name</th>
                      <th scope="col">Lesson Name</th>
                      <th scope="col">Exam Score</th>
                    </tr>
                  </thead>
                  <tbody>';
                  foreach ($exams as $exam) {
                    echo '<tr>
                    <th scope="row">' . $exam->student_id . '</th>
                    <td>' . $exam->class_id . '</td>
                    <td>' . $exam->lesson_id . '</td>
                    <td>' . $exam->exam_score . '</td>

                  </tr>';
                  }
                  echo '</tbody>
                  </table>';


                    ?>





                            </div>
                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                <form action="#" method="post">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                                            aria-describedby="button-addon2" name="searchInput">

                                        <button class="btn btn-outline-primary" type="submit" id="button-addon2"
                                            name="search">Search</button>
                                        <button class="btn btn-outline-primary" type="submit" id="button-addon2"
                                            name="showAll">Show All</button>
                                    </div>
                                </form>






                                <!-- Profile Edit Form -->
                                </form>

                                <?php
                if(isset($_POST['search'])){
                  if(!empty($_POST['searchInput'])){
                    $search = $_POST["searchInput"];
                    $examController = new examScoreController();
                    $exams = $examController->getExamScoreByStudent($search);
                    echo '<table class="table table-striped">
                    <thead>
                    <tr>
                      <th scope="col">Student Name</th>
                      <th scope="col">Class Name</th>
                      <th scope="col">Lesson Name</th>
                      <th scope="col">Exam Score</th>
                    </tr>
                  </thead>
                  <tbody>';
                  foreach ($exams as $exam) {
                    echo '<tr>
                    <th scope="row">' . $exam->student_id . '</th>
                    <td>' . $exam->class_id . '</td>
                    <td>' . $exam->lesson_id . '</td>
                    <td>' . $exam->exam_score . '</td>
                    
                   <td>
                    <a href="examedit.php?id=' . $exam->id . '" class="btn btn-sm btn-primary">Edit</a>
                    <a href="deleteexam.php?examid=' . $exam->id . '" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                  </tr>';
                  }
                  echo '</tbody>
                  </table>';
                  }else {
                    echo '<div class="alert alert-danger" role="alert">
                    Please enter a value!
                    </div>';
                }
                }

                if (isset($_POST["showAll"])) {
                  $examController = new examScoreController();
                  $exams = $examController->getExamScores();
                  echo '<table class="table table-striped">
                  <thead>
                  <tr>
                    <th scope="col">Student Name</th>
                    <th scope="col">Class Name</th>
                    <th scope="col">Lesson Name</th>
                    <th scope="col">Exam Score</th>
                  </tr>
                </thead>
                <tbody>';
                foreach ($exams as $exam) {
                  echo '<tr>
                  <th scope="row">' . $exam->student_id . '</th>
                  <td>' . $exam->class_id . '</td>
                  <td>' . $exam->lesson_id . '</td>
                  <td>' . $exam->exam_score . '</td>
                  
                 <td>
                  <a href="examedit.php?id=' . $exam->id . '" class="btn btn-sm btn-primary">Edit</a>
                  <a href="deleteexam.php?examid=' . $exam->id . '" class="btn btn-sm btn-danger">Delete</a>
                  </td>
                </tr>';
                }
                echo '</tbody>
                </table>';
                }

                if (!isset($_POST['search']) && !isset($_POST['showAll'])) {
                  $examController = new examScoreController();
                  $exams = $examController->getExamScores();
                 
                 
                  echo '<table class="table table-striped">
                  <thead>
                  <tr>
                    <th scope="col">Student Name</th>
                    <th scope="col">Class Name</th>
                    <th scope="col">Lesson Name</th>
                    <th scope="col">Exam Score</th>
                  </tr>
                </thead>
                <tbody>';
                foreach ($exams as $exam) {
                  echo '<tr>
                  <th scope="row">' . $exam->student_id . '</th>
                  <td>' . $exam->class_id . '</td>
                  <td>' . $exam->lesson_id . '</td>
                  <td>' . $exam->exam_score . '</td>
                  

                  <td>
                
                 <td>
                  <a href="examedit.php?id=' . $exam->id . '" class="btn btn-sm btn-primary">Edit</a>
                  <a href="deleteexam.php?examid=' . $exam->id . '" class="btn btn-sm btn-danger">Delete</a>
                  </td>
                </tr>';
                }
                echo '</tbody>
                </table>';
                }

              

               




              ?>

                            </div>

                            <div class="tab-pane fade pt-3" id="profile-settings">

                                <!-- Settings Form -->
                                <form>

                            </div>

                            <div class="tab-pane fade pt-3" id="profile-settings">


                                <!-- Settings Form -->
                                <form>

                                    <div class="row mb-3">
                                        <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email
                                            Notifications</label>
                                        <div class="col-md-8 col-lg-9">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="changesMade"
                                                    checked>
                                                <label class="form-check-label" for="changesMade">
                                                    Changes made to your account
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="newProducts"
                                                    checked>
                                                <label class="form-check-label" for="newProducts">
                                                    Information on new products and services
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="proOffers">
                                                <label class="form-check-label" for="proOffers">
                                                    Marketing and promo offers
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="securityNotify"
                                                    checked disabled>
                                                <label class="form-check-label" for="securityNotify">
                                                    Security alerts
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form><!-- End settings Form -->

                            </div>

                            <div class="tab-pane fade pt-3" id="profile-change-password">
                                <!-- Change Password Form -->
                                <form>

                                    <div class="row mb-3">
                                        <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="password" type="password" class="form-control"
                                                id="currentPassword">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="newpassword" type="password" class="form-control"
                                                id="newPassword">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="renewpassword" type="password" class="form-control"
                                                id="renewPassword">
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form><!-- End Change Password Form -->

                            </div>

                        </div><!-- End Bordered Tabs -->

                    </div>
                </div>

            </div>
            </div>
        </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Yavuzlar</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            Designed by <a href="https://github.com/yunusedemirci">Yunusemre Demirci</a>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->

    <!-- Template Main JS File -->
    <script src="adminpanel.js"></script>

</body>

</html>