<body>
<article class="main main--download" style="background:#fff;">
 <article class="main main--download">
    <section class="download-block latest-version">
        <div class="wrapper">
			<h1 class="latest-version__title"><?= $lang['login_title'] ?></h1>
			
			<p><?= $lang['login_desc'] ?></p>
			<?php if (!empty($error_message)): ?>
			<div class="alert alert-danger">
			<?= htmlspecialchars($error_message) ?>
			</div>
			<?php endif; ?>
		
			<div class="container">
				<div class="formm">
					<form method="POST" action="login_php.php">
						<div class="form-group">
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
							<input type="text" name="username" class="form-control" required>
							<label><?= $lang['login_username'] ?></label>
						</div>
						
						<div class="form-group">
							<input type="password" name="password" class="form-control" required>
							<label><?= $lang['login_password'] ?></label>
						</div>
							<button type="submit" class="btn btn-primary"><?= $lang['login_title'] ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</article>
</article>