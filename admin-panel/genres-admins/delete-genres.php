<?php
require '../layouts/header.php';
require '../../config/config.php';

// Check admin login
if (!isset($_SESSION['admin_name'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid genre ID";
    header("Location: show-genres.php");
    exit;
}

$genre_id = (int)$_GET['id'];

// Fetch genre details
$genre = $conn->prepare("SELECT * FROM genres WHERE id = :id");
$genre->execute([':id' => $genre_id]);
$genre = $genre->fetch(PDO::FETCH_OBJ);

if (!$genre) {
    $_SESSION['error_message'] = "Genre not found";
    header("Location: show-genres.php");
    exit;
}

// Handle delete confirmation
if (isset($_POST['delete'])) {
    try {
        // Check if any show is using this genre
        $check = $conn->prepare("SELECT COUNT(*) FROM shows WHERE genre = :genre_name");
        $check->execute([':genre_name' => $genre->name]);

        $count = $check->fetchColumn();

        if ($count > 0) {
            $_SESSION['error_message'] = "Cannot delete genre - it's being used by $count show(s)";
            header("Location: show-genres.php");
            exit;
        }

        // If not used, proceed with deletion
        $delete = $conn->prepare("DELETE FROM genres WHERE id = :id");
        $delete->execute([':id' => $genre_id]);

        if ($delete->rowCount() > 0) {
            $_SESSION['success_message'] = "Genre deleted successfully";
        } else {
            $_SESSION['error_message'] = "Genre not found or already deleted";
        }

        header("Location: show-genres.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: show-genres.php");
        exit;
    }
}

// Handle edit/update genre
if (isset($_POST['edit'])) {
    $new_genre_name = trim($_POST['new_genre_name']);

    if (empty($new_genre_name)) {
        $_SESSION['error_message'] = "Genre name cannot be empty.";
    } else {
        try {
            $update = $conn->prepare("UPDATE genres SET name = :new_name WHERE id = :id");
            $update->execute([
                ':new_name' => $new_genre_name,
                ':id' => $genre_id
            ]);

            if ($update->rowCount() > 0) {
                $_SESSION['success_message'] = "Genre updated successfully.";
            } else {
                $_SESSION['error_message'] = "No changes made or genre already exists.";
            }
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        }
    }

    header("Location: show-genres.php");
    exit;
}
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Manage Genre</h5>

                <div class="mb-4">
                    <h6>Genre Details:</h6>
                    <p><strong>ID:</strong> <?php echo $genre->id; ?></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($genre->name); ?></p>
                </div>

                <!-- Edit Genre Form -->
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="new_genre_name" class="form-label">Edit Genre Name:</label>
                        <input type="text" class="form-control" name="new_genre_name" id="new_genre_name"
                            value="<?php echo htmlspecialchars($genre->name); ?>" required>
                    </div>
                    <button type="submit" name="edit" class="btn btn-primary">Update Genre</button>
                </form>

                <!-- Delete Genre Form -->
                <div class="alert alert-danger">
                    <strong>Warning!</strong> Are you sure you want to delete this genre? This action cannot be undone.
                </div>
                <form method="POST">
                    <button type="submit" name="delete" class="btn btn-danger">Confirm Delete</button>
                    <a href="show-genres.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../layouts/footer.php'; ?>