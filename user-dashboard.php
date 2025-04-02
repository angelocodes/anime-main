<?php
require "includes/header.php";
require "config/config.php";

// Authentication check

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle unfollow actions
if (isset($_POST['unfollow'])) {
    $show_id = $_POST['show_id'];
    $delete = $conn->prepare("DELETE FROM followings WHERE user_id = :user_id AND show_id = :show_id");
    $delete->execute([
        ':user_id' => $_SESSION['user_id'],
        ':show_id' => $show_id
    ]);
}

// Mark notifications as read
if (isset($_POST['mark_read'])) {
    $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id")
        ->execute([':user_id' => $_SESSION['user_id']]);
}

// Get followed shows
$followedShows = $conn->prepare("
    SELECT shows.id, shows.title, shows.image, shows.genre 
    FROM followings
    JOIN shows ON followings.show_id = shows.id
    WHERE followings.user_id = :user_id
    ORDER BY followings.created_at DESC
");
$followedShows->execute([':user_id' => $_SESSION['user_id']]);
$followedShows = $followedShows->fetchAll(PDO::FETCH_OBJ);

// Get unread notifications
$notifications = $conn->prepare("
    SELECT notifications.*, shows.title, shows.image
    FROM notifications
    JOIN shows ON notifications.show_id = shows.id
    WHERE notifications.user_id = :user_id AND is_read = 0
    ORDER BY notifications.created_at DESC
    LIMIT 10
");
$notifications->execute([':user_id' => $_SESSION['user_id']]);
$unreadNotifications = $notifications->fetchAll(PDO::FETCH_OBJ);

// Get all notifications
$allNotifications = $conn->prepare("
    SELECT notifications.*, shows.title, shows.image
    FROM notifications
    JOIN shows ON notifications.show_id = shows.id
    WHERE notifications.user_id = :user_id
    ORDER BY notifications.created_at DESC
");
$allNotifications->execute([':user_id' => $_SESSION['user_id']]);
$allNotifications = $allNotifications->fetchAll(PDO::FETCH_OBJ);
?>

<!-- Dashboard HTML -->
<div class="dashboard-container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <h1 style="margin-bottom: 30px;">Your Dashboard</h1>

    <div class="dashboard-section">
        <h2><i class="fa fa-bell"></i> Notifications</h2>
        <?php if (count($unreadNotifications) > 0): ?>
            <form method="post" style="margin-bottom: 15px;">
                <button type="submit" name="mark_read" class="btn-mark-read"
                    style="background: #e53637; color: white; border: none; padding: 5px 10px; border-radius: 3px;">
                    Mark All as Read
                </button>
            </form>
        <?php endif; ?>

        <div class="notifications-list">
            <?php if (!empty($unreadNotifications)): ?>
                <?php foreach ($unreadNotifications as $notification): ?>
                    <div class="notification unread" style="background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-left: 3px solid #e53637;">
                        <div style="display: flex; align-items: center;">
                            <img src="img/hero/<?php echo htmlspecialchars($notification->image); ?>"
                                style="width: 50px; height: 70px; object-fit: cover; margin-right: 15px;">
                            <div>
                                <h4 style="margin: 0;"><?php echo htmlspecialchars($notification->title); ?></h4>
                                <p style="margin: 5px 0 0;"><?php echo htmlspecialchars($notification->message); ?></p>
                                <small style="color: #6c757d;"><?php echo date('M j, Y g:i a', strtotime($notification->created_at)); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No new notifications</p>
            <?php endif; ?>
        </div>

        <h3 style="margin-top: 30px;">Notification History</h3>
        <div class="all-notifications">
            <?php if (!empty($allNotifications)): ?>
                <?php foreach ($allNotifications as $notification): ?>
                    <div class="notification" style="padding: 10px; margin-bottom: 8px; border-bottom: 1px solid #eee;">
                        <div style="display: flex; align-items: center;">
                            <img src="img/hero/<?php echo htmlspecialchars($notification->image); ?>"
                                style="width: 40px; height: 56px; object-fit: cover; margin-right: 10px;">
                            <div>
                                <h5 style="margin: 0; font-size: 16px;"><?php echo htmlspecialchars($notification->title); ?></h5>
                                <p style="margin: 3px 0 0; font-size: 14px;"><?php echo htmlspecialchars($notification->message); ?></p>
                                <small style="color: #6c757d;"><?php echo date('M j, Y g:i a', strtotime($notification->created_at)); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No notification history</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-section" style="margin-top: 40px;">
        <h2><i class="fa fa-heart"></i> Followed Shows</h2>
        <div class="followed-shows-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
            <?php if (!empty($followedShows)): ?>
                <?php foreach ($followedShows as $show): ?>
                    <div class="show-card" style="position: relative;">
                        <a href="anime-details.php?id=<?php echo $show->id; ?>">
                            <img src="img/hero/<?php echo htmlspecialchars($show->image); ?>"
                                style="width: 100%; height: 250px; object-fit: cover; border-radius: 5px;">
                        </a>
                        <div style="padding: 10px 0;">
                            <h4 style="margin: 5px 0; font-size: 16px;"><?php echo htmlspecialchars($show->title); ?></h4>
                            <small style="color: #6c757d;"><?php echo htmlspecialchars($show->genre); ?></small>
                        </div>
                        <form method="post" style="position: absolute; top: 10px; right: 10px;">
                            <input type="hidden" name="show_id" value="<?php echo $show->id; ?>">
                            <button type="submit" name="unfollow" class="btn-unfollow"
                                style="background: rgba(0,0,0,0.7); color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer;">
                                <i class="fa fa-times"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't followed any shows yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require "includes/footer.php"; ?>


<?php
/*
// After inserting a new episode in your admin panel:
$followers = $conn->prepare("
    SELECT user_id FROM followings WHERE show_id = :show_id
");
$followers->execute([':show_id' => $show_id]);
$followers = $followers->fetchAll(PDO::FETCH_OBJ);

foreach ($followers as $follower) {
    $message = "New episode added: " . $episode_title;
    $conn->prepare("
        INSERT INTO notifications (user_id, show_id, message) 
        VALUES (:user_id, :show_id, :message)
    ")->execute([
        ':user_id' => $follower->user_id,
        ':show_id' => $show_id,
        ':message' => $message
    ]);
}
*/
?>