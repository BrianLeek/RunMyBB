<article class="main main--download" style="background:#fff;">
	<article class="main main--download">
		<section class="user-created-forums" style="">	
		   <div class="wrapper">
			 <h1 class="download-block--more__title">Contact</h1>
					
					<p>If you have any questions, issues, or feedback, feel free to contact us using the form below.</p>

					<?php if (!empty($success_message)): ?>
						<div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
					<?php elseif (!empty($error_message)): ?>
						<div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
					<?php endif; ?>


					<div class="container">
						<div class="formm">
							<form method="POST" action="/contact.php">
								<div class="form-group">
									<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
									<input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
									<label>Username *</label>
								</div>

								<div class="form-group">
									<input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
									<label for="email">Email *</label>
								</div>

								<div class="form-group">
									<input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
									<label for="name">Full Name *</label>
								</div>

								<div class="form-group">
									<select id="issue_type" name="issue_type" class="form-control" required onchange="toggleForumUrlRequirement()">
										<option value="">-- Select an issue --</option>
										<option value="General Inquiry" <?= (isset($_POST['issue_type']) && $_POST['issue_type'] == 'General Inquiry') ? 'selected' : '' ?>>General Inquiry</option>
										<option value="Forum Problem" <?= (isset($_POST['issue_type']) && $_POST['issue_type'] == 'Forum Problem') ? 'selected' : '' ?>>Forum Problem</option>
										<option value="Account Help" <?= (isset($_POST['issue_type']) && $_POST['issue_type'] == 'Account Help') ? 'selected' : '' ?>>Account Help</option>
										<option value="Technical Support" <?= (isset($_POST['issue_type']) && $_POST['issue_type'] == 'Technical Support') ? 'selected' : '' ?>>Technical Support</option>
										<option value="Report a Forum" <?= (isset($_POST['issue_type']) && $_POST['issue_type'] == 'Report a Forum') ? 'selected' : '' ?>>Report a Forum</option>
									</select>
									<!--<label for="issue_type">Issue Type *</label>-->
								</div>

								<div class="form-group">
									<input type="url" id="forum_url" name="forum_url" class="form-control" value="<?= htmlspecialchars($_POST['forum_url'] ?? '') ?>">
									<label for="forum_url">Forum URL <?= (isset($_POST['issue_type']) && $_POST['issue_type'] == 'Report a Forum') ? '(Required)' : '(Optional)' ?></label>
								</div>

								<div class="form-group">
									<textarea id="message" name="message" rows="5" placeholder="Message *" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
								</div>

								<button type="submit" class="btn btn-primary">Send Message</button>
							</form><br />

							<script>
							function toggleForumUrlRequirement() {
								const issueType = document.getElementById('issue_type').value;
								const forumUrlInput = document.getElementById('forum_url');

								if (issueType === 'Report a Forum') {
									forumUrlInput.required = true;
								} else {
									forumUrlInput.required = false;
								}
							}

							document.addEventListener('DOMContentLoaded', toggleForumUrlRequirement);
							</script>
						</div>
					</div>
				</div>
		</section>
	</article>
</article>