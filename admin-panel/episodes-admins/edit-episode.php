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

// Fetch episode data
$episode = $conn->prepare("SELECT * FROM episodes WHERE id = :id");
$episode->execute([':id' => $episode_id]);
$episode = $episode->fetch(PDO::FETCH_OBJ);

if (!$episode) {
    header("Location: show-episodes.php");
    exit;
}

// Fetch all shows for dropdown
$shows = $conn->query("SELECT id, title FROM shows");
$shows->execute();
$allShows = $shows->fetchAll(PDO::FETCH_OBJ);

// Handle form submission
if (isset($_POST['submit'])) {
    if (empty($_POST['show_id']) || empty($_POST['name'])) {
        echo "<script>alert('Please fill all required fields')</script>";
    } else {
        $show_id = $_POST['show_id'];
        $name = $_POST['name'];

        // Initialize with existing values
        $video = $episode->video;
        $thumbnail = $episode->thumbnail;

        // Handle video upload if new file provided
        if (!empty($_FILES['video']['name'])) {
            $video = $_FILES['video']['name'];
            $video_tmp = $_FILES['video']['tmp_name'];
            $video_path = $_SERVER['DOCUMENT_ROOT'] . '/anime-main/videos/' . basename($video);

            if (!move_uploaded_file($video_tmp, $video_path)) {
                echo "<script>alert('Error uploading video file')</script>";
                $video = $episode->video; // Revert to existing if upload fails
            }
        }

        // Handle thumbnail upload if new file provided
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumbnail = $_FILES['thumbnail']['name'];
            $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
            $thumbnail_path = $_SERVER['DOCUMENT_ROOT'] . '/anime-main/img/hero/' . basename($thumbnail);

            if (!move_uploaded_file($thumbnail_tmp, $thumbnail_path)) {
                echo "<script>alert('Error uploading thumbnail file')</script>";
                $thumbnail = $episode->thumbnail; // Revert to existing if upload fails
            }
        }

        // Update episode in database
        $update = $conn->prepare("
            UPDATE episodes SET 
                show_id = :show_id, 
                name = :name, 
                video = :video, 
                thumbnail = :thumbnail 
            WHERE id = :id
        ");

        $update->execute([
            ':show_id' => $show_id,
            ':name' => $name,
            ':video' => $video,
            ':thumbnail' => $thumbnail,
            ':id' => $episode_id
        ]);

        header("Location: show-episodes.php");
        exit;
    }
}
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-5 d-inline">Edit Episode</h5>
                <form method="POST" action="edit-episode.php?id=<?php echo $episode_id; ?>" enctype="multipart/form-data">

                    <!-- Show Selection -->
                    <div class="form-outline mb-4">
                        <label>Select Show</label>
                        <select name="show_id" class="form-control" required>
                            <option value="">-- Select Show --</option>
                            <?php foreach ($allShows as $show) : ?>
                                <option value="<?php echo $show->id; ?>" <?php echo ($show->id == $episode->show_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($show->title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Episode Name -->
                    <div class="form-outline mb-4">
                        <input type="text" name="name" class="form-control" placeholder="Episode Name"
                            value="<?php echo htmlspecialchars($episode->name); ?>" required>
                    </div>

                    <!-- Current Video -->
                    <div class="form-outline mb-4">
                        <label>Current Video: <?php echo htmlspecialchars($episode->video); ?></label>
                        <div class="mb-2">
                            <a href="/anime-main/videos/<?php echo htmlspecialchars($episode->video); ?>" target="_blank" class="btn btn-sm btn-info">
                                View Current Video
                            </a>
                        </div>
                        <label>Upload New Video (MP4 - optional)</label>
                        <input type="file" name="video" class="form-control" accept="video/mp4">
                    </div>

                    <!-- Current Thumbnail -->
                    <div class="form-outline mb-4">
                        <label>Current Thumbnail</label>
                        <div class="mb-2">
                            <img src="/anime-main/img/hero/<?php echo htmlspecialchars($episode->thumbnail); ?>"
                                style="max-height: 100px;" class="img-thumbnail">
                        </div>
                        <label>Upload New Thumbnail (optional)</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="submit" class="btn btn-primary mb-4">Update Episode</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../layouts/footer.php'; ?>