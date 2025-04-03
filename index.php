<?php require "includes/header.php"; ?>
<?php require "config/config.php"; ?>
<?php

$shows = $conn->query("SELECT * FROM shows LIMIT 4");
$shows->execute();

$allshows = $shows->fetchAll(PDO::FETCH_OBJ);

//trending shows
$trendingShows = $conn->prepare("
    SELECT 
        s.id, 
        s.image, 
        s.type, 
        s.genre, 
        s.num_available, 
        s.num_total, 
        s.title, 
        COUNT(v.show_id) AS count_views
    FROM 
        shows s
    LEFT JOIN 
        views v ON s.id = v.show_id
    GROUP BY 
        s.id
    HAVING 
        COUNT(v.show_id) > 0
    ORDER BY 
        count_views DESC
    LIMIT 6;
");
$trendingShows->execute();

$trendingShows->execute();
$allTrendingShows = $trendingShows->fetchAll(PDO::FETCH_OBJ);

//adventure shows
$adventureShows = $conn->query("
SELECT 
        shows.id AS id, 
        shows.image AS image, 
        shows.type AS type, 
        shows.genre AS genre, 
        shows.num_available AS num_available, 
        shows.num_total AS num_total, 
        shows.title AS title, 
        COUNT(views.show_id) AS count_views 
    FROM shows 
    LEFT JOIN views ON shows.id = views.show_id 
    WHERE shows.genre = 'Adventure' 
    GROUP BY shows.id 
    ORDER BY shows.created_at DESC 
    ;");

$adventureShows->execute();
$allAdventureShows = $adventureShows->fetchAll(PDO::FETCH_OBJ);

//recently added
$recentShows = $conn->query("
    SELECT 
        shows.id AS id, 
        shows.image AS image, 
        shows.type AS type, 
        shows.genre AS genre, 
        shows.num_available AS num_available, 
        shows.num_total AS num_total, 
        shows.title AS title, 
        COUNT(views.show_id) AS count_views 
    FROM shows 
    LEFT JOIN views ON shows.id = views.show_id 
    GROUP BY shows.id 
    ORDER BY shows.created_at DESC 
    LIMIT 6;
");
$recentShows->execute();
$allRecentShows = $recentShows->fetchAll(PDO::FETCH_OBJ);

// live action
$liveActionShows = $conn->query("
    SELECT 
        shows.id AS id, 
        shows.image AS image, 
        shows.type AS type, 
        shows.genre AS genre, 
        shows.num_available AS num_available, 
        shows.num_total AS num_total, 
        shows.title AS title, 
        COUNT(views.show_id) AS count_views 
    FROM shows 
    LEFT JOIN views ON shows.id = views.show_id 
    WHERE shows.genre = 'Action' 
    GROUP BY shows.id 
    ORDER BY shows.created_at DESC 
    LIMIT 6;
");
$liveActionShows->execute();
$allLiveActionShows = $liveActionShows->fetchAll(PDO::FETCH_OBJ);

//for You shows
$forYouShows = $conn->query("
    SELECT 
        shows.id AS id, 
        shows.image AS image, 
        shows.type AS type, 
        shows.genre AS genre, 
        shows.num_available AS num_available, 
        shows.num_total AS num_total, 
        shows.title AS title, 
        COUNT(views.show_id) AS count_views 
    FROM shows 
    JOIN views ON shows.id = views.show_id 
    GROUP BY shows.id 
    ORDER BY count_views DESC 
    LIMIT 4;
");
$forYouShows->execute();
$allForYouShows = $forYouShows->fetchAll(PDO::FETCH_OBJ);


?>

<!-- Hero Section Begin -->
<section class="hero">
    <div class="container">
        <div class="hero__slider owl-carousel">
            <?php foreach ($allshows as $show) : ?>
                <div class="hero__items set-bg" style="background-size: 100% 100%;" data-setbg="img/hero/<?php echo $show->image; ?>">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="hero__text">
                                <div class="label"><?php echo $show->genre; ?></div>
                                <h2><?php echo $show->title; ?></h2>
                                <p style="background: rgba(59, 57, 57, 0.3); backdrop-filter: blur(5px); padding: 20px; border-radius: 10px; color: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);"><?php echo $show->description; ?></p>
                                <a href="anime-watching.php?id=<?php echo $show->id; ?>&ep=1"><span>Watch Now</span> <i class="fa fa-angle-right"></i></a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>
<!-- Hero Section End -->

<!-- Product Section Begin -->
<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="trending__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>Trending Now</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="btn__all">
                                <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php foreach ($allTrendingShows as $trendingShow) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="img/hero/<?php echo $trendingShow->image; ?>">
                                        <div class="ep"><?php echo $trendingShow->num_available; ?> / <?php echo $trendingShow->num_total; ?></div>

                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $trendingShow->count_views; ?></div>
                                    </div>
                                    <div class="product__item__text">
                                        <ul>
                                            <li><?php echo $trendingShow->genre; ?></li>
                                            <li><?php echo $trendingShow->type; ?></li>
                                        </ul>
                                        <h5><a href="anime-details.php?id=<?php echo $trendingShow->id; ?>"><?php echo $trendingShow->title; ?></a></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="popular__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>Adventure Shows</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="btn__all">
                                <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($allAdventureShows as $adventureShow) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="img/hero/<?php echo $adventureShow->image; ?>">
                                        <div class="ep"><?php echo $adventureShow->num_available; ?> / <?php echo $adventureShow->num_total; ?></div>

                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $adventureShow->count_views; ?></div>
                                    </div>
                                    <div class="product__item__text">
                                        <ul>
                                            <li><?php echo $adventureShow->genre; ?></li>
                                            <li><?php echo $adventureShow->type; ?></li>
                                        </ul>
                                        <h5><a href="#"><?php echo $adventureShow->title; ?></a></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="recent__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>Recently Added Shows</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="btn__all">
                                <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($allRecentShows as $recentShow) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="img/hero/<?php echo $recentShow->image; ?>">
                                        <div class="ep"><?php echo $recentShow->num_available; ?> / <?php echo $recentShow->num_total; ?></div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $recentShow->count_views; ?></div>
                                    </div>
                                    <div class="product__item__text">
                                        <ul>
                                            <li><?php echo $recentShow->genre; ?></li>
                                            <li><?php echo $recentShow->type; ?></li>
                                        </ul>
                                        <h5><a href="#"><?php echo $recentShow->title; ?></a></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="live__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>Live Action</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="btn__all">
                                <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($allLiveActionShows as $liveActionShow) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="img/hero/<?php echo $liveActionShow->image; ?>">
                                        <div class="ep"><?php echo $liveActionShow->num_available; ?> / <?php echo $liveActionShow->num_total; ?></div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $liveActionShow->count_views; ?></div>
                                    </div>
                                    <div class="product__item__text">
                                        <ul>
                                            <li><?php echo $liveActionShow->genre; ?></li>
                                            <li><?php echo $liveActionShow->type; ?></li>
                                        </ul>
                                        <h5><a href="#"><?php echo $liveActionShow->title; ?></a></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>




            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="product__sidebar">
                    <div class="product__sidebar__view">
                    </div>
                </div>
                <div class="product__sidebar__comment">
                    <div class="section-title">
                        <h5>For You</h5>
                    </div>
                    <?php foreach ($allForYouShows as $forYouShow) : ?>

                        <div class="product__sidebar__comment__item">
                            <div class="product__sidebar__comment__item__pic">
                                <img style="width:90px; height:130px;" src="img/hero/<?php echo $forYouShow->image; ?>" alt="">
                            </div>
                            <div class="product__sidebar__comment__item__text">
                                <ul>
                                    <li><?php echo $forYouShow->genre; ?></li>
                                    <li><?php echo $forYouShow->type; ?></li>
                                </ul>
                                <h5><a href="#"><?php echo $forYouShow->title; ?></a></h5>
                                <span><i class="fa fa-eye"></i> <?php echo $forYouShow->count_views; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<!-- Product Section End -->

<?php require "includes/footer.php"; ?>