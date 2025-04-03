<?php
require '../layouts/header.php';
require '../../config/config.php';

// Check admin login
if (!isset($_SESSION['admin_name'])) {
  header("Location: " . ADMINURL . "/admins/login-admins.php");
  exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
  if (empty($_POST['name'])) {
    echo "<script>alert('Please enter genre name')</script>";
  } else {
    $name = $_POST['name'];

    // Check if genre already exists
    $check = $conn->prepare("SELECT id FROM genres WHERE name = :name");
    $check->execute([':name' => $name]);

    if ($check->rowCount() > 0) {
      echo "<script>alert('Genre already exists')</script>";
    } else {
      // Insert new genre
      $insert = $conn->prepare("INSERT INTO genres (name) VALUES (:name)");
      $insert->execute([':name' => $name]);

      if ($insert) {
        $_SESSION['success_message'] = "Genre created successfully";
        header("Location: show-genres.php"); // Redirect to genres list
        exit;
      } else {
        echo "<script>alert('Failed to create genre')</script>";
      }
    }
  }
}
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Create Genres</h5>

        <?php if (isset($_SESSION['success_message'])): ?>
          <div class="alert alert-success">
            <?php
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="create-genres.php" enctype="multipart/form-data">
          <!-- Genre Name Input -->
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="name" id="form2Example1" class="form-control" placeholder="Genre Name" required>
          </div>

          <!-- Submit Button -->
          <button type="submit" name="submit" class="btn btn-primary mb-4 text-center">Create</button>
        </form>

      </div>
    </div>
  </div>
</div>

<?php
require '../layouts/footer.php';
?>