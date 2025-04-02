<?php
require '../layouts/header.php';
require '../../config/config.php'; ?>

<?php
//ob_start();



if (isset($_SESSION['admin_name'])) {
  header("Location: " . ADMINURL . "");
}


if (isset($_POST['submit'])) {
  if (empty($_POST['email']) || empty($_POST['password'])) {
    echo "<script>
  alert('Please fill in all fields.')
</script>";
  } else {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Check if the user exists
    $query = $conn->prepare("SELECT * FROM admins WHERE email = :email");
    $query->execute([":email" => $email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      // Set session variables
      $_SESSION['admin_id'] = $user['id'];
      $_SESSION['admin_name'] = $user['admin_name'];
      $_SESSION['email'] = $user['email'];
      echo ("<script>location.href='" . ADMINURL . "'</script>");

      //header("Location: ADMINURL"); // Redirect to user dashboard or home page
      //echo "<script> alert('LOGGED IN') </script>";
    } else {
      echo "<script>
  alert('Invalid email or password. Please try again.')
</script>";
    }
  }
}
//ob_end_flush();
?>


<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mt-5">Login</h5>
        <form method="POST" class="p-auto" action="login-admins.php">
          <!-- Email input -->
          <div class="form-outline mb-4">
            <input type="email" name="email" id="form2Example1" class="form-control" placeholder="Email" />

          </div>


          <!-- Password input -->
          <div class="form-outline mb-4">
            <input type="password" name="password" id="form2Example2" placeholder="Password" class="form-control" />

          </div>



          <!-- Submit button -->
          <button type="submit" name="submit" class="btn btn-primary  mb-4 text-center">Login</button>


        </form>

      </div>
    </div>
  </div>
  <?php require '../layouts/footer.php'; ?>