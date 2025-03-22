<?php
ob_start();
require "../includes/header.php";
require "../config/config.php";

if (isset($_SESSION['username'])) {
    header("Location: http://localhost/anime-main");
}


if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        echo "<script>alert('Please fill in all fields.')</script>";
    } else {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Check if the user exists
        $query = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute([":email" => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            header("Location: ../index.php"); // Redirect to user dashboard or home page
            exit();
        } else {
            echo "<script>alert('Invalid email or password. Please try again.')</script>";
        }
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
                    <h2>Login</h2>
                    <p>Welcome to Anime</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Normal Breadcrumb End -->

<!-- Login Section Begin -->
<section class="login spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="login__form">
                    <h3>Login</h3>
                    <form action="login.php" method="post">
                        <div class="input__item">
                            <input type="text" name="email" placeholder="Email address" required>
                            <span class="icon_mail"></span>
                        </div>
                        <div class="input__item">
                            <input type="password" name="password" placeholder="Password" required>
                            <span class="icon_lock"></span>
                        </div>
                        <button type="submit" name="submit" class="site-btn">Login Now</button>
                    </form>
                    <a href="#" class="forget_pass">Forgot Your Password?</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login__register">
                    <h3>Don’t Have An Account?</h3>
                    <a href="signup.php" class="primary-btn">Register Now</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Login Section End -->

<?php require "../includes/footer.php"; ?>