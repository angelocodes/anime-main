<?php
require '../layouts/header.php';
require '../../config/config.php';

// Check admin login
if (!isset($_SESSION['admin_name'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit;
}

// Check if episode ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: show-episodes.php");
    exit;
}

$episode_id = (int)$_GET['id'];

// Fetch episode data to get file paths
$episode = $conn->prepare("SELECT * FROM episodes WHERE id = :id");
$episode->execute([':id' => $episode_id]);
$episode = $episode->fetch(PDO::FETCH_OBJ);

if (!$episode) {
    header("Location: show-episodes.php");
    exit;
}

// Handle deletion
if (isset($_POST['delete'])) {
    try {
        // Delete files first
        $video_path = $_SERVER['DOCUMENT_ROOT'] . '/anime-main/videos/' . basename($episode->video);
        $thumbnail_path = $_SERVER['DOCUMENT_ROOT'] . '/anime-main/img/hero/' . basename($episode->thumbnail);

        if (file_exists($video_path)) {
            unlink($video_path);
        }
        if (file_exists($thumbnail_path)) {
            unlink($thumbnail_path);
        }

        // Delete from database
        $delete = $conn->prepare("DELETE FROM episodes WHERE id = :id");
        $delete->execute([':id' => $episode_id]);

        $_SESSION['success_message'] = "Episode deleted successfully";
        header("Location: show-episodes.php");
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error deleting episode: " . addslashes($e->getMessage()) . "')</script>";
    }
}
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-5">Delete Episode</h5>

                <div class="alert alert-danger">
                    <strong>Warning!</strong> Are you sure you want to delete this episode? This action cannot be undone.
                </div>

                <div class="mb-4">
                    <h6>Episode Details:</h6>
                    <p><strong>Show:</strong>
                        <?php
                        $show = $conn->prepare("SELECT title FROM shows WHERE id = :id");
                        $show->execute([':id' => $episode->show_id]);
                        $show_title = $show->fetch(PDO::FETCH_OBJ)->title;
                        echo htmlspecialchars($show_title);
                        ?>
                    </p>
                    <p><strong>Episode Name:</strong> <?php echo htmlspecialchars($episode->name); ?></p>
                    <p><strong>Video File:</strong> <?php echo htmlspecialchars($episode->video); ?></p>
                    <p><strong>Thumbnail:</strong> <?php echo htmlspecialchars($episode->thumbnail); ?></p>
                </div>

                <div class="mb-4">
                    <h6>File Previews:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">Video:</p>
                            <video width="100%" controls>
                                <source src="/anime-main/videos/<?php echo htmlspecialchars($episode->video); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">Thumbnail:</p>
                            <img src="/anime-main/img/hero/<?php echo htmlspecialchars($episode->thumbnail); ?>"
                                style="max-width: 100%;" class="img-thumbnail">
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <button type="submit" name="delete" class="btn btn-danger">Confirm Delete</button>
                    <a href="show-episodes.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../layouts/footer.php'; ?>