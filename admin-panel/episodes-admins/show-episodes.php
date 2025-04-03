<?php
require '../layouts/header.php';
require '../../config/config.php';
if (!isset($_SESSION['admin_name'])) {
  header("Location: " . ADMINURL . "/admins/login-admins.php");
}

// Fetch shows with episode counts
$shows = $conn->query("
    SELECT s.id, s.title, COUNT(e.id) as episode_count 
    FROM shows s 
    LEFT JOIN episodes e ON e.show_id = s.id 
    GROUP BY s.id
    ORDER BY s.title ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">

        <!-- Title and Button Row -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="card-title mb-0">Episodes</h5>
          <a href="create-episodes.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Episodes
          </a>
        </div>

        <!-- Episodes Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="thead-light">
              <tr>
                <th style="width: 5%">#</th>
                <th style="width: 25%">Show Title</th>
                <th style="width: 15%">Episodes</th>
                <th style="width: 45%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($shows as $index => $show): ?>
                <tr data-toggle="collapse" data-target="#show-<?= $show['id'] ?>" class="accordion-toggle">
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($show['title']) ?></td>
                  <td>
                    <span class="badge badge-pill badge-primary"><?= $show['episode_count'] ?></span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-secondary toggle-episodes">
                      Toggle Episodes
                    </button>
                  </td>
                </tr>

                <!-- Hidden Episodes Row -->
                <tr>
                  <td colspan="4" class="hiddenRow p-0">
                    <div class="accordion-body collapse" id="show-<?= $show['id'] ?>">
                      <?php
                      $episodes = $conn->prepare("SELECT * FROM episodes WHERE show_id = ?");
                      $episodes->execute([$show['id']]);
                      $episodes = $episodes->fetchAll(PDO::FETCH_ASSOC);
                      ?>

                      <?php if (!empty($episodes)): ?>
                        <div class="p-3 bg-light">
                          <table class="table table-sm table-borderless mb-0">
                            <thead>
                              <tr>
                                <th>Episode</th>

                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($episodes as $episode): ?>
                                <tr>
                                  <td><?= htmlspecialchars($episode['name']) ?></td>

                                  <td>
                                    <a href="edit-episode.php?id=<?= $episode['id'] ?>" class="btn btn-sm btn-info">
                                      <i class="fas fa-edit">Edit</i>
                                    </a>
                                    <a href="delete-episode.php?id=<?= $episode['id'] ?>"
                                      class="btn btn-sm btn-danger"
                                      onclick="return confirm('Delete this episode?')">
                                      <i class="fas fa-trash">Delete</i>
                                    </a>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      <?php else: ?>
                        <div class="p-3 text-muted">No episodes found for this show.</div>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require '../layouts/footer.php'; ?>