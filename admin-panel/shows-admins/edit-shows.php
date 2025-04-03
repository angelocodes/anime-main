<?php
require '../layouts/header.php';
require '../../config/config.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit;
}

// Fetch existing show data
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM shows WHERE id = ?");
$stmt->execute([$id]);
$show = $stmt->fetch(PDO::FETCH_OBJ);

if (!$show) {
    header("Location: show-shows.php");
    exit;
}

// Update logic
if (isset($_POST['submit'])) {
    if (
        empty($_POST['title']) || empty($_POST['description']) || empty($_POST['type']) ||
        empty($_POST['studios']) || empty($_POST['date_aired']) || empty($_POST['status']) ||
        empty($_POST['genre']) || empty($_POST['duration']) || empty($_POST['quality']) ||
        empty($_POST['num_available']) || empty($_POST['num_total'])
    ) {
        echo "<script>alert('One or more inputs are not filled')</script>";
    } else {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $studios = $_POST['studios'];
        $date_aired = $_POST['date_aired'];
        $status = $_POST['status'];
        $genre = $_POST['genre'];
        $duration = $_POST['duration'];
        $quality = $_POST['quality'];
        $num_available = $_POST['num_available'];
        $num_total = $_POST['num_total'];

        // Handle image upload if new image was provided
        $image = $show->image; // Keep existing image by default
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $base_dir = dirname(__DIR__, 2); // C:\xampp\htdocs\anime-main
            $dir = $base_dir . '/img/hero/' . basename($image);

            // Delete old image
            $oldImagePath = $base_dir . '/img/hero/' . basename($show->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Upload new image
            move_uploaded_file($_FILES['image']['tmp_name'], $dir);
        }

        $update = $conn->prepare("UPDATE shows SET 
      title = :title,
      description = :description, 
      type = :type, 
      studios = :studios, 
      date_aired = :date_aired, 
      status = :status, 
      genre = :genre, 
      duration = :duration, 
      quality = :quality, 
      num_available = :num_available, 
      num_total = :num_total,
      image = :image
      WHERE id = :id");

        $update->execute([
            ":title" => $title,
            ":description" => $description,
            ":type" => $type,
            ":studios" => $studios,
            ":date_aired" => $date_aired,
            ":status" => $status,
            ":genre" => $genre,
            ":duration" => $duration,
            ":quality" => $quality,
            ":num_available" => $num_available,
            ":num_total" => $num_total,
            ":image" => $image,
            ":id" => $id
        ]);

        header("location: show-shows.php");
        exit;
    }
}
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-5 d-inline">Edit Show</h5>
                <form method="POST" action="edit-shows.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <!-- Title -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="title" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->title); ?>" placeholder="title" />
                    </div>

                    <!-- Image -->
                    <div class="form-outline mb-4 mt-4">
                        <label>Current Image:</label>
                        <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/anime-main/img/hero/<?php echo $show->image; ?>"
                            style="height: 100px; display: block;" class="mb-2">
                        <input type="file" name="image" id="form2Example1" class="form-control" />
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Description</label>
                        <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="3"><?php echo htmlspecialchars($show->description); ?>
            </textarea>
                    </div>

                    <!-- Type -->
                    <div class="form-outline mb-4 mt-4">
                        <select name="type" class="form-select form-control" aria-label="Default select example">
                            <option value="Tv Series" <?php echo ($show->type == 'Tv Series') ? 'selected' : ''; ?>>Tv Series</option>
                            <option value="Movie" <?php echo ($show->type == 'Movie') ? 'selected' : ''; ?>>Movie</option>
                        </select>
                    </div>

                    <!-- Studios -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="studios" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->studios); ?>" placeholder="studios" />
                    </div>

                    <!-- Date Aired -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="date_aired" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->date_aired); ?>" placeholder="date_aired" />
                    </div>

                    <!-- Status -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="status" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->status); ?>" placeholder="status" />
                    </div>

                    <!-- Genre -->
                    <div class="form-outline mb-4 mt-4">
                        <select name="genre" class="form-select form-control" aria-label="Default select example">
                            <option value="Magic" <?php echo ($show->genre == 'Magic') ? 'selected' : ''; ?>>Magic</option>
                            <option value="Action" <?php echo ($show->genre == 'Action') ? 'selected' : ''; ?>>Action</option>
                            <option value="Adventure" <?php echo ($show->genre == 'Adventure') ? 'selected' : ''; ?>>Adventure</option>
                        </select>
                    </div>

                    <!-- Duration -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="duration" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->duration); ?>" placeholder="duration" />
                    </div>

                    <!-- Quality -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="quality" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->quality); ?>" placeholder="quality" />
                    </div>

                    <!-- Num Available -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="num_available" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->num_available); ?>" placeholder="num_available" />
                    </div>

                    <!-- Num Total -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" name="num_total" id="form2Example1" class="form-control"
                            value="<?php echo htmlspecialchars($show->num_total); ?>" placeholder="num_total" />
                    </div>

                    <!-- Submit button -->
                    <button type="submit" name="submit" class="btn btn-primary mb-4 text-center">Update Show</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../layouts/footer.php'; ?>