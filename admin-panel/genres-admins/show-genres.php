<?php
require '../layouts/header.php';
require '../../config/config.php';

// Check admin login
if (!isset($_SESSION['admin_name'])) {
  header("Location: " . ADMINURL . "/admins/login-admins.php");
  exit;
}

// Fetch all genres from database
$genres = $conn->query("SELECT * FROM genres ORDER BY name");
$genres->execute();
$allGenres = $genres->fetchAll(PDO::FETCH_OBJ);

// Handle delete action
if (isset($_GET['delete_id'])) {
  $delete_id = $_GET['delete_id'];

  // Check if genre is used in any shows before deleting
  $check = $conn->prepare("SELECT COUNT(*) FROM show_genres WHERE genre_id = :genre_id");
  $check->execute([':genre_id' => $delete_id]);

  if ($check->fetchColumn() > 0) {
    $_SESSION['error_message'] = "Cannot delete genre - it's being used by one or more shows";
  } else {
    $delete = $conn->prepare("DELETE FROM genres WHERE id = :id");
    $delete->execute([':id' => $delete_id]);

    $_SESSION['success_message'] = "Genre deleted successfully";
  }

  header("Location: show-genres.php");
  exit;
}
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4 d-inline">Genres</h5>
        <a href="create-genres.php" class="btn btn-primary mb-4 text-center float-right">Create Genre</a>

        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
          <div class="alert alert-success">
            <?php
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
          <div class="alert alert-danger">
            <?php
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
          </div>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($allGenres) > 0): ?>
                <?php foreach ($allGenres as $index => $genre): ?>
                  <tr>
                    <th scope="row"><?php echo $index + 1; ?></th>
                    <td><?php echo htmlspecialchars($genre->name); ?></td>
                    <td>
                      <a href="delete-genres.php?id=<?php echo $genre->id; ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this genre?')">
                        Delete
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-center">No genres found</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require '../layouts/footer.php'; ?>