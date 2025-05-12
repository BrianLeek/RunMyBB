<body>
<article class="main main--download">
 <article class="main main--download">
    <section class="download-block latest-version">
        <div class="wrapper">
			<div class="container mt-5">
				<h2><?= $lang['edit_tagline'] ?></h2>

				<?php if ($error): ?>
					<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
				<?php elseif ($success): ?>
					<div class="alert alert-success"><?= $lang['edit_success'] ?></div>
				<?php endif; ?>

				<form method="POST">
					<!-- Forum Name -->
					<div class="mb-3">
						<label for="forum_name" class="form-label"><?= $lang['edit_forum_name'] ?></label>
						<input type="text" class="form-control" id="forum_name" name="forum_name" value="<?= htmlspecialchars($original_name ?? '') ?>" required>
						<div class="form-text"><?= $lang['edit_forum_name_desc'] ?></div>
					</div>

					<!-- Forum Description -->
					<div class="mb-3">
						<label for="forum_description" class="form-label"><?= $lang['edit_forum_desc'] ?></label>
						<textarea class="form-control" id="forum_description" name="forum_description" rows="3"><?= htmlspecialchars($original_description ?? '') ?></textarea>
						<div class="form-text"><?= $lang['edit_forum_desc_desc'] ?></div>
					</div>

					<!-- Forum Subdomain -->
					<div class="mb-3">
						<label for="forum_subdomain" class="form-label"><?= $lang['edit_forum_url'] ?></label>
						<input type="text" class="form-control" id="forum_subdomain" name="forum_subdomain" value="<?= htmlspecialchars($original_subdomain ?? '') ?>" required>
						<div class="form-text"><?= $lang['edit_forum_url_desc'] ?></div>
					</div>

					<button type="submit" class="btn btn-primary"><?= $lang['edit_save'] ?></button>
					<a href="dashboard.php" class="btn btn-secondary"><?= $lang['edit_cancel'] ?></a>
				</form>
			</div>
		</div>
	</section>
</article>
