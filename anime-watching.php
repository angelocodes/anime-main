<?php
require "includes/header.php";
require "config/config.php";

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_GET['id']) && isset($_GET['ep'])) {
    $id = $_GET['id'];
    $ep = $_GET['ep'];

    // Get all episodes for the show (using prepared statement)
    $episodes = $conn->prepare("SELECT * FROM episodes WHERE show_id = :show_id");
    $episodes->execute([':show_id' => $id]);
    $allEpisodes = $episodes->fetchAll(PDO::FETCH_OBJ);

    // Get specific episode (using prepared statement)
    $episode = $conn->prepare("SELECT * FROM episodes WHERE show_id = :show_id AND name = :ep_name");
    $episode->execute([':show_id' => $id, ':ep_name' => $ep]);
    $singleEpisode = $episode->fetch(PDO::FETCH_OBJ);

    // Get show data (using prepared statement)
    $show = $conn->prepare("SELECT * FROM shows WHERE id = :show_id");
    $show->execute([':show_id' => $id]);
    $singleShow = $show->fetch(PDO::FETCH_OBJ);

    // Check if data exists
    if (!$singleEpisode || !$singleShow) {
        header("Location: 404.php");
        exit;
    }
} ?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?php echo APPURL; ?>"><i class="fa fa-home"></i> Home</a>
                    <a href="<?php echo APPURL; ?>/categories.php?name=<?php echo htmlspecialchars($singleShow->genre); ?>">Categories</a>
                    <a href="#"><?php echo htmlspecialchars($singleShow->genre); ?></a>
                    <span><?php echo htmlspecialchars($singleShow->title); ?>,</span>
                    <span>Episode <?php echo htmlspecialchars($singleEpisode->name); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Anime Section Begin -->
<section class="anime-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="anime__video__player">
                    <video id="player" playsinline controls
                        data-poster="<?php echo APPURL; ?>/videos/<?php echo htmlspecialchars($singleEpisode->thumbnail); ?>">
                        <source src="<?php echo APPURL; ?>/videos/<?php echo htmlspecialchars($singleEpisode->video); ?>" type="video/mp4" />
                    </video>

                    <!-- Download Button (only shows for logged in users) -->
                    <?php if ($isLoggedIn): ?>
                        <div style="margin-top: 15px; text-align: center;">
                            <a href="<?php echo APPURL; ?>/download.php?id=<?php echo $singleEpisode->id; ?>"
                                style="background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 4px; 
                                      text-decoration: none; display: inline-block; transition: all 0.3s;"
                                onmouseover="this.style.backgroundColor='#45a049'"
                                onmouseout="this.style.backgroundColor='#4CAF50'">
                                <i class="fa fa-download"></i> Download Episode
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="margin-top: 15px; text-align: center;">
                            <a href="login.php"
                                style="background-color: #e53637; color: white; padding: 10px 20px; border-radius: 4px; 
                                      text-decoration: none; display: inline-block; transition: all 0.3s;"
                                onmouseover="this.style.backgroundColor='#ff4757'"
                                onmouseout="this.style.backgroundColor='#e53637'">
                                <i class="fa fa-sign-in-alt"></i> Login to Download
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="anime__details__episodes">
                    <div class="section-title">
                        <h5>Episode List</h5>
                    </div>
                    <?php foreach ($allEpisodes as $episode): ?>
                        <a href="<?php echo APPURL; ?>/anime-watching.php?id=<?php echo $episode->show_id; ?>&ep=<?php echo $episode->name; ?>"
                            style="<?php echo $episode->name == $ep ? 'background-color: #e53637; color: white;' : ''; ?>">
                            Ep <?php echo htmlspecialchars($episode->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="anime__details__review">
                    <div class="section-title">
                        <h5>Reviews</h5>
                    </div>
                    <!-- Sample reviews (would normally come from database) -->
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="img/anime/review-1.jpg" alt="">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>Chris Curry - <span>1 Hour ago</span></h6>
                            <p>whachikan Just noticed that someone categorized this as belonging to the genre "demons" LOL</p>
                        </div>
                    </div>
                    <!-- More review items... -->
                </div>

                <div class="anime__details__form">
                    <div class="section-title">
                        <h5>Your Comment</h5>
                    </div>
                    <?php if ($isLoggedIn): ?>
                        <form method="post" action="#">
                            <textarea name="comment" placeholder="Your Comment" required></textarea>
                            <button type="submit"><i class="fa fa-location-arrow"></i> Review</button>
                        </form>
                    <?php else: ?>
                        <p style="text-align: center; color: #b7b7b7; font-size: 16px; margin: 20px 0;">
                            Please <a href="login.php" style="color: #e53637; text-decoration: none; font-weight: 600;">login</a> to post comments.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="anime__details__sidebar">
                    <div class="section-title">
                        <h5>You might like...</h5>
                    </div>
                    <!-- Recommended shows would go here -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Anime Section End -->

<?php require "includes/footer.php"; ?>