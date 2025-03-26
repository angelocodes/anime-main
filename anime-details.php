<?php
require "includes/header.php";
require "config/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $shows = $conn->query("SELECT 
        shows.id AS id, 
        shows.image AS image, 
        shows.type AS type, 
        shows.status as status,
        shows.duration as duration,
        shows.quality as quality,
        shows.description as description,
        shows.genre AS genre,
        shows.date_aired as date_aired,
        shows.studios as studios, 
        shows.num_available AS num_available, 
        shows.num_total AS num_total, 
        shows.title AS title, 
        COUNT(views.show_id) AS count_views 
    FROM shows 
    LEFT JOIN views ON shows.id = views.show_id 
    WHERE shows.id = '$id' 
    GROUP BY shows.id ");
    $shows->execute();
    $singleShow = $shows->fetch(PDO::FETCH_OBJ);


    //comments
    $comments = $conn->query("SELECT * FROM comments WHERE show_id = '$id'");
    $comments->execute();

    $allComments = $comments->fetchAll(PDO::FETCH_OBJ);

    //following
    if (isset($_POST['submit'])) {
        $user_id = $_POST['user_id'];
        $show_id = $_POST['show_id'];

        $follow = $conn->prepare("INSERT INTO followings (user_id,
            show_id) 
            VALUES (:user_id, :show_id)");

        $follow->execute([
            ":user_id" => $user_id,
            ":show_id" => $show_id,

        ]);
        echo "<script><alert>You have followed this show</alert></script>";
    }

    //Check following
    $checkFollowing = $conn->query("SELECT * FROM followings 
    where show_id='$id' and user_id='$_SESSION[user_id]' ");
    $checkFollowing->execute();


    //inserting comments
    if (isset($_POST['insert_comment'])) {

        if (empty($_POST['comment'])) {
            echo "<script>alert('Empty comment')</script>";
        } else {

            $comment = $_POST['comment'];
            $show_id = $id;
            $user_id = $_SESSION['user_id'];
            $user_name = $_SESSION['username'];


            $insert = $conn->prepare("INSERT INTO comments (comment, show_id, user_id, user_name) 
            VALUES (:comment, :show_id, :user_id, :user_name)");

            $insert->execute([
                ":comment" => $comment,
                ":show_id" => $show_id,
                ":user_id" => $user_id,
                ":user_name" => $user_name,

            ]);

            echo "<script><alert>Comment added</alert></script>";
        }
    }
}

?>



<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                    <a href="<?php echo APPURL; ?>/anime-details.php?id=
                    <?php echo $singleShow->id; ?>">Details</a>
                    <span><?php echo $singleShow->title; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Anime Section Begin -->
<section class="anime-details spad">
    <div class="container">
        <div class="anime__details__content">
            <div class="row">
                <div class="col-lg-3">
                    <div class="anime__details__pic set-bg" data-setbg="img/hero/<?php echo $singleShow->image; ?>">
                        <div class="view"><i class="fa fa-eye"></i> <?php echo $singleShow->count_views; ?></div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="anime__details__text">
                        <div class="anime__details__title">
                            <h3><?php echo $singleShow->title; ?></h3>
                        </div>

                        <p><?php echo $singleShow->description; ?></p>
                        <div class="anime__details__widget">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <ul>
                                        <li><span>Type:</span> <?php echo $singleShow->type; ?></li>
                                        <li><span>Studios:</span> <?php echo $singleShow->studios; ?></li>
                                        <li><span>Date aired:</span> <?php echo $singleShow->date_aired; ?></li>
                                        <li><span>Status:</span> <?php echo $singleShow->status; ?></li>
                                    </ul>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <ul>
                                        <li><span>Genre:</span> <?php echo $singleShow->genre; ?></li>

                                        <li><span>Duration:</span> <?php echo $singleShow->duration; ?></li>
                                        <li><span>Quality:</span> <?php echo $singleShow->quality; ?></li>
                                        <li><span>Views:</span> <?php echo $singleShow->count_views; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="anime__details__btn">
                            <form action="anime-details.php?id=<?php echo $singleShow->id; ?>" method="post">
                                <input type="hidden" value="<?php echo $id; ?>" name="show_id" id="">
                                <input type="hidden" value="<?php echo $_SESSION['user_id']; ?>" name="user_id" id="">

                                <?php if ($checkFollowing->rowCount() > 0) : ?>
                                    <button name="submit" type="submit" disabled style="color:coral;" class="follow-btn"><i class="fa fa-heart-o"></i> Followed</button>
                                <?php else: ?>

                                    <button name="submit" type="submit" class="follow-btn"><i class="fa fa-heart-o"></i> Follow</button>
                                <?php endif; ?>
                                <a href="anime-watching.php?id=<?php echo $id; ?>&ep=1" class="watch-btn"><span>Watch Now</span> <i
                                        class="fa fa-angle-right"></i></a>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="anime__details__review">
                    <div class="section-title">
                        <h5>Comments</h5>
                    </div>

                    <?php foreach ($allComments as $comment) : ?>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="img/anime/review-1.jpg" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6><?php echo $comment->user_name ?> - <span><?php echo $comment->created_at ?></span></h6>
                                <p><?php echo $comment->comment ?></p>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
                <div class="anime__details__form">
                    <div class="section-title">
                        <h5>Your Comment</h5>
                    </div>
                    <form method="post" action="<?php echo APPURL; ?>/anime-details.php?id=<?php echo $id; ?>">
                        <textarea name="comment" placeholder="Your Comment"></textarea>
                        <button name="insert_comment" type="submit"><i class="fa fa-location-arrow"></i> Comment</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="anime__details__sidebar">
                    <div class="section-title">
                        <h5>you might like...</h5>
                    </div>
                    <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-1.jpg">
                        <div class="ep">18 / ?</div>
                        <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        <h5><a href="#">Boruto: Naruto next generations</a></h5>
                    </div>
                    <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-2.jpg">
                        <div class="ep">18 / ?</div>
                        <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                    </div>
                    <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-3.jpg">
                        <div class="ep">18 / ?</div>
                        <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        <h5><a href="#">Sword art online alicization war of underworld</a></h5>
                    </div>
                    <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-4.jpg">
                        <div class="ep">18 / ?</div>
                        <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        <h5><a href="#">Fate/stay night: Heaven's Feel I. presage flower</a></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Anime Section End -->

<?php require "includes/footer.php"; ?>