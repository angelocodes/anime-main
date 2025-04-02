<?php

require "includes/header.php";
require "config/config.php";
?>


<?php
$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get show details
    $shows = $conn->prepare("SELECT 
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
    WHERE shows.id = :id 
    GROUP BY shows.id");
    $shows->execute([':id' => $id]);
    $singleShow = $shows->fetch(PDO::FETCH_OBJ);

    if (!$singleShow) {
        header("Location: 404.php");
        exit;
    }

    // Get comments
    $comments = $conn->prepare("SELECT * FROM comments WHERE show_id = :show_id ORDER BY created_at DESC");
    $comments->execute([':show_id' => $id]);
    $allComments = $comments->fetchAll(PDO::FETCH_OBJ);

    // Handle follow action
    if ($isLoggedIn && isset($_POST['submit'])) {
        $follow = $conn->prepare("INSERT INTO followings (user_id, show_id) VALUES (:user_id, :show_id)");
        $follow->execute([
            ":user_id" => $_SESSION['user_id'],
            ":show_id" => $id
        ]);
        echo "<script>alert('You have followed this show');</script>";
    }

    // Check if user is following (only if logged in)
    $isFollowing = false;
    if ($isLoggedIn) {
        $checkFollowing = $conn->prepare("SELECT * FROM followings WHERE show_id = :show_id AND user_id = :user_id");
        $checkFollowing->execute([
            ':show_id' => $id,
            ':user_id' => $_SESSION['user_id']
        ]);
        $isFollowing = $checkFollowing->rowCount() > 0;
    }

    // Handle comment submission
    if ($isLoggedIn && isset($_POST['insert_comment']) && !empty($_POST['comment'])) {
        $insert = $conn->prepare("INSERT INTO comments (comment, show_id, user_id, user_name) 
            VALUES (:comment, :show_id, :user_id, :user_name)");
        $insert->execute([
            ":comment" => $_POST['comment'],
            ":show_id" => $id,
            ":user_id" => $_SESSION['user_id'],
            ":user_name" => $_SESSION['username']
        ]);
        echo "<script>alert('Comment added');</script>";
    }

    // Track view (only for logged in users)
    if ($isLoggedIn) {
        $checkView = $conn->prepare("SELECT * FROM views WHERE show_id = :show_id AND user_id = :user_id");
        $checkView->execute([
            ':show_id' => $id,
            ':user_id' => $_SESSION['user_id']
        ]);

        if ($checkView->rowCount() == 0) {
            $insertView = $conn->prepare("INSERT INTO views (show_id, user_id) VALUES (:show_id, :user_id)");
            $insertView->execute([
                ":show_id" => $id,
                ":user_id" => $_SESSION['user_id']
            ]);
        }
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                    <a href="<?php echo APPURL; ?>/anime-details.php?id=<?php echo htmlspecialchars($singleShow->id); ?>">Details</a>
                    <span><?php echo htmlspecialchars($singleShow->title); ?></span>
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
                    <div class="anime__details__pic set-bg" data-setbg="img/hero/<?php echo htmlspecialchars($singleShow->image); ?>">
                        <div class="view"><i class="fa fa-eye"></i> <?php echo htmlspecialchars($singleShow->count_views); ?></div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="anime__details__text">
                        <div class="anime__details__title">
                            <h3><?php echo htmlspecialchars($singleShow->title); ?></h3>
                        </div>
                        <p><?php echo htmlspecialchars($singleShow->description); ?></p>
                        <div class="anime__details__widget">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <ul>
                                        <li><span>Type:</span> <?php echo htmlspecialchars($singleShow->type); ?></li>
                                        <li><span>Studios:</span> <?php echo htmlspecialchars($singleShow->studios); ?></li>
                                        <li><span>Date aired:</span> <?php echo htmlspecialchars($singleShow->date_aired); ?></li>
                                        <li><span>Status:</span> <?php echo htmlspecialchars($singleShow->status); ?></li>
                                    </ul>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <ul>
                                        <li><span>Genre:</span> <?php echo htmlspecialchars($singleShow->genre); ?></li>
                                        <li><span>Duration:</span> <?php echo htmlspecialchars($singleShow->duration); ?></li>
                                        <li><span>Quality:</span> <?php echo htmlspecialchars($singleShow->quality); ?></li>
                                        <li><span>Views:</span> <?php echo htmlspecialchars($singleShow->count_views); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="anime__details__btn">
                            <?php if ($isLoggedIn): ?>
                                <form action="anime-details.php?id=<?php echo htmlspecialchars($singleShow->id); ?>" method="post">
                                    <input type="hidden" value="<?php echo htmlspecialchars($id); ?>" name="show_id">
                                    <input type="hidden" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>" name="user_id">
                                    <?php if ($isFollowing): ?>
                                        <button name="submit" type="submit" disabled style="color:coral;" class="follow-btn">
                                            <i class="fa fa-heart-o"></i> Followed
                                        </button>
                                    <?php else: ?>
                                        <button name="submit" type="submit" class="follow-btn">
                                            <i class="fa fa-heart-o"></i> Follow
                                        </button>
                                    <?php endif; ?>
                                    <a href="anime-watching.php?id=<?php echo htmlspecialchars($id); ?>&ep=1" class="watch-btn">
                                        <span>Watch Now</span> <i class="fa fa-angle-right"></i>
                                    </a>
                                </form>
                            <?php else: ?>
                                <a href="auth/login.php" class="follow-btn"><i class="fa fa-heart-o"></i> Login to Follow</a>
                                <a href="anime-watching.php?id=<?php echo htmlspecialchars($id); ?>&ep=1" class="watch-btn">
                                    <span>Watch Now</span> <i class="fa fa-angle-right"></i>
                                </a>
                            <?php endif; ?>
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
                    <?php foreach ($allComments as $comment): ?>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="img/anime/review-1.jpg" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6><?php echo htmlspecialchars($comment->user_name); ?> - <span><?php echo htmlspecialchars($comment->created_at); ?></span></h6>
                                <p><?php echo htmlspecialchars($comment->comment); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="anime__details__form">
                    <div class="section-title">
                        <h5>Your Comment</h5>
                    </div>
                    <?php if ($isLoggedIn): ?>
                        <form method="post" action="<?php echo APPURL; ?>/anime-details.php?id=<?php echo htmlspecialchars($id); ?>">
                            <textarea name="comment" placeholder="Your Comment" required></textarea>
                            <button name="insert_comment" type="submit"><i class="fa fa-location-arrow"></i> Comment</button>
                        </form>
                    <?php else: ?>
                        <p style="
            text-align: center;
            color: #b7b7b7;
            font-size: 16px;
            margin: 20px 0;
            font-family: 'Muli', sans-serif;
        ">
                            Please <a href="auth/login.php" style="
                color: #e53637;
                text-decoration: none;
                transition: all 0.3s;
                font-weight: 600;
            ">login</a> to post comments.
                        </p>
                    <?php endif; ?>
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