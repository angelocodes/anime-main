<?php

require '../layouts/header.php';
require '../../config/config.php';
?>
<?php

if (!isset($_SESSION['admin_name'])) {
  header("Location: " . ADMINURL . "/admins/login-admins.php");
}

//adding information to database, this case adding new shows
if (isset($_POST['submit'])) {

  if (
    empty($_POST['title']) or empty($_POST['description']) or empty($_POST['type'])
    or empty($_POST['studios']) or empty($_POST['date_aired']) or empty($_POST['status'])
    or empty($_POST['genre']) or empty($_POST['duration']) or empty($_POST['quality'])
    or empty($_POST['num_available']) or empty($_POST['num_total'])
  ) {

    echo "<script>alert('one or more inputs are not filled')</script>";
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
    $image = $_FILES['image']['name'];

    $dir = "img/" . basename($image);

    $insert = $conn->prepare("INSERT INTO shows (title,
            description, type, studios, date_aired, status, genre, duration, quality, num_available, num_total, image) 
            VALUES (:title, :description, :type, :studios, :date_aired, :status, :genre, :duration, :quality, :num_available, :num_total, :image)");

    $insert->execute([
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
      ":image" => $image
    ]);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $dir)) {

      header("location: show-shows.php");
    }
  }
}



?>


<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Create Shows</h5>
        <form method="POST" action="create-shows.php" enctype="multipart/form-data">
          <!-- Email input -->
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="title" id="form2Example1" class="form-control" placeholder="title" />

          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="file" name="image" id="form2Example1" class="form-control" />

          </div>
          <div class="form-group">
            <label for="exampleFormControlTextarea1">Description</label>
            <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="3"></textarea>
          </div>
          <div class="form-outline mb-4 mt-4">

            <select name="type" class="form-select  form-control" aria-label="Default select example">
              <option selected>Choose Type</option>
              <option value="Tv Series">Tv Series</option>
              <option value="Movie">Movie</option>
            </select>
          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="studios" id="form2Example1" class="form-control" placeholder="studios" />

          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="date_aired" id="form2Example1" class="form-control" placeholder="date_aired" />

          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="status" id="form2Example1" class="form-control" placeholder="status" />

          </div>
          <div class="form-outline mb-4 mt-4">

            <select name="genre" class="form-select  form-control" aria-label="Default select example">
              <option selected>Choose Genre</option>
              <option value="Tv Series">Magic</option>
              <option value="Movie">Action</option>
              <option value="Movie">Adventure</option>
            </select>
          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="duration" id="form2Example1" class="form-control" placeholder="duration" />

          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="quality" id="form2Example1" class="form-control" placeholder="quality" />

          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="num_available" id="form2Example1" class="form-control" placeholder="num_available" />

          </div>
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="num_total" id="form2Example1" class="form-control" placeholder="num_total" />

          </div>


          <br>



          <!-- Submit button -->
          <button type="submit" name="submit" class="btn btn-primary  mb-4 text-center">create</button>


        </form>

      </div>
    </div>
  </div>
</div>

<?php require '../layouts/footer.php'; ?>