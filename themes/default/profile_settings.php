<body>
<article class="main main--download">
 <article class="main main--download">
    <section class="download-block latest-version">
        <div class="wrapper">
			<div class="container mt-5">
				<h2>Profile Settings</h2>
				<form method="POST" action="save_profile_settings.php" enctype="multipart/form-data">
					<!-- Username -->
					<div class="mb-3">
						<label for="profile_username" class="form-label">Username</label>
						<input type="text" class="form-control" id="profile_username" name="profile_username" value="<?= htmlspecialchars($currentUsername) ?>" required>
					</div>

					<!-- Email -->
					<div class="mb-3">
						<label for="profile_email" class="form-label">Email</label>
						<input type="email" class="form-control" id="profile_email" name="profile_email" value="<?= htmlspecialchars($currentEmail) ?>" required>
					</div>

					<!-- Password -->
					<div class="mb-3">
						<label for="profile_password" class="form-label">Password</label>
						<input type="password" class="form-control" id="profile_password" name="profile_password">
						<small class="form-text text-muted">Leave blank if you don’t want to change your password.</small>
					</div>

					<!-- Bio -->
					<div class="mb-3">
						<label for="profile_bio" class="form-label">Bio</label>
						<textarea class="form-control" id="profile_bio" name="profile_bio" rows="4"><?= htmlspecialchars($currentBio) ?></textarea>
					</div>
					
					<!-- Website URL -->
					<div class="mb-3">
						<label for="public_forum" class="form-label">Website <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="right" title="This is your website that will be displayed on your profile on RunMyBB."></i></label>
						<input type="text" class="form-control" id="public_forum" name="profile_public_forum" value="<?= htmlspecialchars($currentPublicForumURL) ?>">
					</div>

					<!-- Avatar -->
					<div class="mb-3">
						<label for="profile_avatar" class="form-label">Avatar</label>
						<input type="file" class="form-control" id="profile_avatar" name="profile_avatar">
					</div>
					
					<!-- Hide Email -->
					<div class="mb-3 form-check">
						<input type="checkbox" name="hide_email" class="form-check-input" id="hide_email" <?= $hide_email ? 'checked' : '' ?>>
						<label for="hide_email" class="form-check-label">Hide my email from other users</label>
					</div>

					<!-- Hide Last Seen -->
					<div class="mb-3 form-check">
						<input type="checkbox" name="hide_last_seen" class="form-check-input" id="hide_last_seen" <?= $hide_last_seen ? 'checked' : '' ?>>
						<label for="hide_last_seen" class="form-check-label">Hide my last seen date</label>
					</div>

					<!-- Hide Registered -->
					<div class="mb-3 form-check">
						<input type="checkbox" name="hide_registered" class="form-check-input" id="hide_registered" <?= $hide_registered ? 'checked' : '' ?>>
						<label for="hide_registered" class="form-check-label">Hide my registered date</label>
					</div>

					<!-- Hide Forums -->
					<div class="mb-3 form-check">
						<input type="checkbox" name="hide_forums" class="form-check-input" id="hide_forums" <?= $hide_forums ? 'checked' : '' ?>>
						<label for="hide_forums" class="form-check-label">Hide forums I created</label>
					</div>

					<button type="submit" class="btn btn-primary">Save Changes</button>
				</form>
			</div>
		</div>
	</section>
</article>
