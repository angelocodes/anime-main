<?php
require '../layouts/header.php';
require '../../config/config.php';

if (!isset($_SESSION['admin_name'])) {
  header("Location: " . ADMINURL . "/admins/login-admins.php");
  exit;
}

// Fetch all shows for dropdown
$shows = $conn->query("SELECT id, title FROM shows");
$shows->execute();
$allShows = $shows->fetchAll(PDO::FETCH_OBJ);

// Handle form submission
if (isset($_POST['submit'])) {
  if (
    empty($_POST['show_id']) || empty($_POST['name']) ||
    empty($_FILES['video']['name']) || empty($_FILES['thumbnail']['name'])
  ) {
    echo "<script>alert('Please fill all fields')</script>";
  } else {
    $show_id = $_POST['show_id'];
    $name = $_POST['name'];
    $episode_title = $name; // Fix undefined variable issue

    // Handle file uploads
    $video = $_FILES['video']['name'];
    $thumbnail = $_FILES['thumbnail']['name'];
    $video_tmp = $_FILES['video']['tmp_name'];
    $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];

    $video_path = $_SERVER['DOCUMENT_ROOT'] . '/anime-main/videos/' . basename($video);
    $thumbnail_path = $_SERVER['DOCUMENT_ROOT'] . '/anime-main/img/hero/' . basename($thumbnail);

    // Move uploaded files
    if (move_uploaded_file($video_tmp, $video_path) && move_uploaded_file($thumbnail_tmp, $thumbnail_path)) {
      // Insert episode after successful upload
      $insert = $conn->prepare("INSERT INTO episodes (show_id, name, video, thumbnail) 
                                     VALUES (:show_id, :name, :video, :thumbnail)");
      $insert->execute([
        ':show_id' => $show_id,
        ':name' => $name,
        ':video' => $video,
        ':thumbnail' => $thumbnail
      ]);

      // Fetch followers AFTER inserting the episode
      $followers = $conn->prepare("SELECT user_id FROM followings WHERE show_id = :show_id");
      $followers->execute([':show_id' => $show_id]);
      $followers = $followers->fetchAll(PDO::FETCH_OBJ);

      // Send notifications
      foreach ($followers as $follower) {
        $message = "New episode added: " . $episode_title;
        $conn->prepare("INSERT INTO notifications (user_id, show_id, message) 
                        VALUES (:user_id, :show_id, :message)")
          ->execute([
            ':user_id' => $follower->user_id,
            ':show_id' => $show_id,
            ':message' => $message
          ]);
      }

      header("Location: show-episodes.php");
      exit;
    } else {
      echo "<script>alert('Error uploading files')</script>";
    }
  }
}

?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Create Episode</h5>
        <form method="POST" action="create-episodes.php" enctype="multipart/form-data">
          <!-- Show Selection -->
          <div class="form-outline mb-4">
            <label>Select Show</label>
            <select name="show_id" class="form-control" required>
              <option value="">-- Select Show --</option>
              <?php foreach ($allShows as $show) : ?>
                <option value="<?php echo $show->id; ?>"><?php echo htmlspecialchars($show->title); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Episode Name -->
          <div class="form-outline mb-4">
            <input type="text" name="name" class="form-control" placeholder="Episode Name" required>
          </div>

          <!-- Video Upload -->
          <div class="form-outline mb-4">
            <label>Video File (MP4)</label>
            <input type="file" name="video" class="form-control" accept="video/mp4" required>
          </div>

          <!-- Thumbnail Upload -->
          <div class="form-outline mb-4">
            <label>Thumbnail Image</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/*" required>
          </div>

          <!-- Submit Button -->
          <button type="submit" name="submit" class="btn btn-primary mb-4">Create</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require '../layouts/footer.php'; ?>