<?php 
require_once 'backend.php'; 
$database = new Database();
$db = $database->getConnection();
$userController = new UserController();
$userController->teacherControl();



if(isset($_GET['examid'])){
    $examid = $_GET['examid'];
    $query = "DELETE FROM exams WHERE id = :examid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":examid", $examid);
    $stmt->execute();
    header("Location: teacherpanelexam.php");
    die();
} 

?>