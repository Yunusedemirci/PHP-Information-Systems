<?php 
require_once 'backend.php'; 
$database = new Database();
$db = $database->getConnection();
$userController = new UserController();
$userController->adminControl();

if(isset($_GET['studentclassid'])){
    $studentclassid = $_GET['studentclassid'];
    $query = "DELETE FROM classes_students WHERE student_id = :studentclassid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":studentclassid", $studentclassid);
    $stmt->execute();
    header("Location: studentaddclass.php");
    die();
}

if(isset($_GET['studentusersid'])){
    $studentusersid = $_GET['studentusersid'];
    $query = "DELETE FROM users WHERE id = :studentusersid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":studentusersid", $studentusersid);
    $stmt->execute();
    header("Location: students.php");
    die();
}

if(isset($_GET['classid'])){
    $classid = $_GET['classid'];
    $query = "DELETE FROM classes WHERE id = :classid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":classid", $classid);
    $stmt->execute();
    header("Location: class.php");
    die();
}

if(isset($_GET['teachersusersid'])){
    $teachersusersid = $_GET['teachersusersid'];
    $query = "DELETE FROM users WHERE id = :teachersusersid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":teachersusersid", $teachersusersid);
    $stmt->execute();
    header("Location: teachers.php");
    die();
}

if(isset($_GET['lessonid'])){
    $lessonid = $_GET['lessonid'];
    $query = "DELETE FROM lessons WHERE id = :lessonid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":lessonid", $lessonid);
    $stmt->execute();
    header("Location: lesson.php");
    die();
}



?>