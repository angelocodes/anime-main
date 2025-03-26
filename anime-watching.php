<?php
require "includes/header.php";
require "config/config.php";

if (isset($_GET['id']) and isset($_GET['ep'])) {
    $id = $_GET['id'];
    $ep = $_GET['ep'];

    $episodes = $conn->query("SELECT * FROM episodes WHERE show_id='$id'");
    $episodes->execute();

    $allEpisodes = $episodes->fetchAll(PDO::FETCH_OBJ);

    //grabbing each episode
    $episode = $conn->query("SELECT * FROM episodes where show_id='$id' and name='$ep'");
    $episode->execute();

    $singleEpisode = $episode->fetch(PDO::FETCH_OBJ);

    //show data
    $show = $conn->query("SELECT * FROM shows where id='$id'");
    $show->execute();

    $singleShow = $show->fetch(PDO::FETCH_OBJ);
}




?>


<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?php echo APPURL; ?>"><i class=" fa fa-home"></i> Home</a>
                    <a href="<?php echo APPURL; ?>/categories.php?name=<?php echo $singleShow->genre; ?>">Categories</a>
                    <a href="#"><?php echo $singleShow->genre; ?></a>
                    <span><?php echo $singleShow->title; ?>,</span>
                    <span>Episode <?php echo $singleEpisode->name; ?></span>

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
                    <video id="player" playsinline controls data-poster="<?php echo APPURL; ?>/videos/<?php $singleEpisode->thumbnail; ?>">
                        <source src="<?php echo APPURL; ?>/videos/<?php echo $singleEpisode->video ?>" type="video/mp4" />
                        <!-- Captions are optional -->
                        <!---<track kind="captions" label="English captions" src="#" srclang="en" default /> -->
                    </video>
                </div>
                <div class="anime__details__episodes">

                    <div class="section-title">
                        <h5>List Name</h5>
                    </div>
                    <?php foreach ($allEpisodes as $episodes): ?>
                        <a href="<?php echo APPURL; ?>/anime-watching.php?id=<?php echo $episodes->show_id; ?>&ep=<?php echo $episodes->id; ?>">Ep <?php echo $episodes->id; ?></a>
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
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="img/anime/review-1.jpg" alt="">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>Chris Curry - <span>1 Hour ago</span></h6>
                            <p>whachikan Just noticed that someone categorized this as belonging to the genre
                                "demons" LOL</p>
                        </div>
                    </div>
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="img/anime/review-2.jpg" alt="">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>Lewis Mann - <span>5 Hour ago</span></h6>
                            <p>Finally it came out ages ago</p>
                        </div>
                    </div>
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="img/anime/review-3.jpg" alt="">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>Louis Tyler - <span>20 Hour ago</span></h6>
                            <p>Where is the episode 15 ? Slow update! Tch</p>
                        </div>
                    </div>
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="img/anime/review-4.jpg" alt="">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>Chris Curry - <span>1 Hour ago</span></h6>
                            <p>whachikan Just noticed that someone categorized this as belonging to the genre
                                "demons" LOL</p>
                        </div>
                    </div>
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="img/anime/review-5.jpg" alt="">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>Lewis Mann - <span>5 Hour ago</span></h6>
                            <p>Finally it came out ages ago</p>
                        </div>
                    </div>
                    <div class="anime__review__item">
                        <div class="anime__review__item__pic">
                            <img src="img/anime/review-6.jpg" alt="">
                        </div>
                        <div class="anime__review__item__text">
                            <h6>Louis Tyler - <span>20 Hour ago</span></h6>
                            <p>Where is the episode 15 ? Slow update! Tch</p>
                        </div>
                    </div>
                </div>
                <div class="anime__details__form">
                    <div class="section-title">
                        <h5>Your Comment</h5>
                    </div>
                    <form action="#">
                        <textarea placeholder="Your Comment"></textarea>
                        <button type="submit"><i class="fa fa-location-arrow"></i> Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Anime Section End -->


<?php require "includes/footer.php"; ?>