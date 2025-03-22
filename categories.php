<?php
require "includes/header.php";
require "config/config.php";

if (isset($_GET['name'])) {
    $name = $_GET['name'];
    $shows = $conn->query("SELECT 
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
    WHERE shows.genre = '$name' 
    GROUP BY shows.id 
    ORDER BY shows.created_at DESC ");
    $shows->execute();
    $allShows = $shows->fetchAll(PDO::FETCH_OBJ);
}

?>


<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?php echo APPURL; ?>"><i class="fa fa-home"></i> Home</a>
                    <a href="<?php echo APPURL; ?>/categories.php?name=<?php echo $name; ?>">Categories</a>
                    <span><?php echo $name; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Product Section Begin -->
<section class="product-page spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="product__page__content">
                    <div class="product__page__title">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-6">
                                <div class="section-title">
                                    <h4><?php echo $name ?></h4>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">

                        <?php if (count($allShows) > 0) : ?>
                            <?php foreach ($allShows as $show) : ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item">
                                        <div class="product__item__pic set-bg" data-setbg="img/hero/<?php echo $show->image; ?>">
                                            <div class="ep"><?php echo $show->num_available; ?> /
                                                <?php echo $show->num_total; ?> </div>

                                            <div class="view"><i class="fa fa-eye"></i> <?php echo $show->count_views; ?></div>
                                        </div>
                                        <div class="product__item__text">
                                            <ul>
                                                <li><?php echo $show->genre; ?></li>
                                                <li><?php echo $show->type; ?></li>
                                            </ul>
                                            <h5><a href="<?php echo APPURL ?>/anime-details.php?id=
                                        <?php echo $show->id; ?>">
                                                    <?php echo $show->title; ?></a></h5>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <p class="text-white">There are no shows in this genre at the moment.
                                Get back later!</p>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="product__sidebar">
                    <div class="product__sidebar__view">
                    </div>
                    <!-- </div>
                </div>         -->
                </div>
                <div class="product__sidebar__comment">
                    <div class="section-title">
                        <h5>FOR YOU</h5>
                    </div>
                    <div class="product__sidebar__comment__item">
                        <div class="product__sidebar__comment__item__pic">
                            <img src="img/sidebar/comment-1.jpg" alt="">
                        </div>
                        <div class="product__sidebar__comment__item__text">
                            <ul>
                                <li>Active</li>
                                <li>Movie</li>
                            </ul>
                            <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                            <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                        </div>
                    </div>
                    <div class="product__sidebar__comment__item">
                        <div class="product__sidebar__comment__item__pic">
                            <img src="img/sidebar/comment-2.jpg" alt="">
                        </div>
                        <div class="product__sidebar__comment__item__text">
                            <ul>
                                <li>Active</li>
                                <li>Movie</li>
                            </ul>
                            <h5><a href="#">Shirogane Tamashii hen Kouhan sen</a></h5>
                            <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                        </div>
                    </div>
                    <div class="product__sidebar__comment__item">
                        <div class="product__sidebar__comment__item__pic">
                            <img src="img/sidebar/comment-3.jpg" alt="">
                        </div>
                        <div class="product__sidebar__comment__item__text">
                            <ul>
                                <li>Active</li>
                                <li>Movie</li>
                            </ul>
                            <h5><a href="#">Kizumonogatari III: Reiket su-hen</a></h5>
                            <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                        </div>
                    </div>
                    <div class="product__sidebar__comment__item">
                        <div class="product__sidebar__comment__item__pic">
                            <img src="img/sidebar/comment-4.jpg" alt="">
                        </div>
                        <div class="product__sidebar__comment__item__text">
                            <ul>
                                <li>Active</li>
                                <li>Movie</li>
                            </ul>
                            <h5><a href="#">Monogatari Series: Second Season</a></h5>
                            <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<!-- Product Section End -->


<?php require "includes/footer.php"; ?>