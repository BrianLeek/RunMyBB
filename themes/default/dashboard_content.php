<?php include "config.php"; ?> <body class="section section--download" style="background:#ccc">
<article class="main main--download">
    <section class="download-block download-block--latest-version latest-version">
        <div class="wrapper">
            <h1 class="latest-version__title"><?= $lang['dashboard_welcome'] ?> <?php echo htmlspecialchars($username); ?></h1>
            <div class="latest-version__details">
                <a class="latest-version__details__item latest-version__details__item--file-size" href="profile_settings"><?= $lang['edit_profile'] ?></a>
                <a class="latest-version__details__item latest-version__details__item--file-size" href="profile?username=<?php echo htmlspecialchars($username); ?>"><?= $lang['view_profile'] ?></a>
                <a class="latest-version__details__item latest-version__details__item--file-size" href="logout"><?= $lang['logout'] ?></a>
            </div>
        </div>
    </section>

    <section class="download-block download-block--more">
        <div class="wrapper">
            <h1 class="download-block--more__title"><?= $lang['dashboard_your_forums'] ?></h1>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
<div class="col-md-4">
    <div class="card mb-3 <?= $row['status'] === 'suspended' ? 'border-danger' : 'border-primary' ?>">
        <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($row['name']) ?></h2>
            <p class="card-text">Status: 
                <span class="badge <?= $row['status'] === 'suspended' ? 'bg-danger' : 'bg-success' ?>">
                    <?= ucfirst($row['status']) ?>
                </span>
            </p>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <!-- View Forum -->
                <a href="forum/<?= htmlspecialchars($row['subdomain']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                    <?= $lang['view_forum'] ?>
                </a>
				
				<!-- Edit Button -->
				<a href="edit_forum.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm">
					<?= $lang['edit_details'] ?>
				</a>

                <!-- Delete Forum -->
                <a href="delete_forum.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm"
                   onclick="return confirm('Are you sure you want to delete this forum? This action cannot be undone!')">
                    <i class="bi bi-trash3"></i> <?= $lang['delete_forum'] ?>
                </a>

                <!-- Export Forum -->
                <form action="export_forum.php" method="post" class="d-inline">
                    <input type="hidden" name="forum_name" value="<?= htmlspecialchars($row['subdomain']) ?>">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download"></i> <?= $lang['export_forum'] ?>
                    </button>
                </form>
            </div>

            <hr>

			<form method="POST" action="suspend_forum.php" class="form-check form-switch mb-2">
				<input type="hidden" name="forum_id" value="<?= $row['id'] ?>">
				<input type="checkbox" class="form-check-input" id="suspend<?= $row['id'] ?>"
					   name="suspend_toggle" onchange="this.form.submit()"
					   <?= $row['status'] === 'suspended' ? '' : 'checked' ?>>
				<label class="form-check-label" for="suspend<?= $row['id'] ?>">
					<?= $row['status'] === 'suspended' ? 'Suspended' : 'Active' ?>
				</label>
			</form>

            <!-- Show/Hide from Public Toggle -->
            <form method="POST" action="toggle_forum_visibility.php" class="form-check form-switch">
                <input type="hidden" name="forum_id" value="<?= $row['id'] ?>">
                <input type="checkbox" class="form-check-input" id="toggle<?= $row['id'] ?>"
                       name="visibility_toggle" onchange="this.form.submit()" 
                       <?= $row['hidden'] ? '' : 'checked' ?>>
                <label class="form-check-label" for="toggle<?= $row['id'] ?>">
                    <?= $row['hidden'] ? $lang['dashboard_hidden_forum'] : $lang['dashboard_shown_forum'] ?>
                </label>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>
            </div>
        </div>
    </section>
</article>

