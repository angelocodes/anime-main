<?php
require "config/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get episode details
    $episode = $conn->prepare("SELECT * FROM episodes WHERE id = :id");
    $episode->execute([':id' => $id]);
    $singleEpisode = $episode->fetch(PDO::FETCH_OBJ);

    if ($singleEpisode) {
        $file_path = __DIR__ . '/videos/' . $singleEpisode->video;

        if (file_exists($file_path)) {
            // Set headers for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));

            // Clear output buffer
            ob_clean();
            flush();

            // Read the file
            readfile($file_path);
            exit;
        } else {
            die("File not found");
        }
    } else {
        die("Episode not found");
    }
} else {
    die("Invalid request");
}
