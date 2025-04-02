<?php
require '../layouts/header.php';
require '../../config/config.php';
?>
<?php

if (!isset($_SESSION['admin_name'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
}

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $image = $conn->query("SELECT image FROM shows WHERE id = $id");
    $image->execute();
    $getImage = $image->fetch(PDO::FETCH_OBJ);

    unlink("img/" . $getImage->image);


    $deleteShow = $conn->prepare("DELETE FROM shows WHERE id = :id");
    $deleteShow->execute([":id" => $id]);
    //or you can use:
    //$deleteShow = $conn->prepare("DELETE FROM shows WHERE id = :id");
    //$deleteShow->execute()




    header("location: show-shows.php");
}


?>