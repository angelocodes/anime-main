<?php

ob_start();
require "../includes/header.php";
require "../config/config.php";



if (isset($_SESSION['username'])) {
    header("location: " . APPURL . "");
}

//Signup Logic
if (isset($_POST['submit'])) {

    if (empty($_POST['email']) or empty($_POST['username']) or empty($_POST['password'])) {

        echo "<script>alert('one or more inputs are not filled')</script>";
    } else {

        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $insert = $conn->prepare("INSERT INTO users (email,
            username, password) 
            VALUES (:email, :username, :password)");

        $insert->execute([
            ":email" => $email,
            ":username" => $username,
            ":password" => $password,

        ]);

        header("location: login.php");
    }
}
ob_end_flush();

?>
<!-- Normal Breadcrumb Begin -->
<section class="normal-breadcrumb set-bg" data-setbg="<?php echo APPURL; ?>img/normal-breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Sign Up</h2>
                    <p>Welcome to Anime</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Normal Breadcrumb End -->

<!-- Signup Section Begin -->
<section class="signup spad">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="login__form">
                    <h3>Sign Up</h3>
                    <form action="signup.php" method="post">
                        <div class="input__item ">
                            <input class="col-md-12" type="text" name="email" placeholder="Email address">
                            <span class="icon_mail"></span>
                        </div>
                        <div class="input__item">
                            <input type="text" name="username" placeholder="Your Name">
                            <span class="icon_profile"></span>
                        </div>
                        <div class="input__item">
                            <input type="password" name="password" placeholder="Password">
                            <span class="icon_lock"></span>
                        </div>
                        <button type="submit" name="submit" class="site-btn">Register Now</button>
                    </form>
                    <h5>Already have an account? <a href="login.php">Log In!</a></h5>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- Signup Section End -->

<?php require "../includes/footer.php"; ?>