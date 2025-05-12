<body class="section section--download" style="background:#ccc">

<style>#box{margin:30px 0}.single-offer{text-align:center;background-color:#fff;box-shadow:0 0 20px 3px rgba(0,0,0,.05);padding:20px;border-right:1px solid #eee;transition:.5s;margin-bottom:30px}.single-offer:hover{transform:scale(1.1);z-index:1;position:relative}i.fa-solid.fa-house{font-size:25px;color:red;margin-bottom:10px}.single-offer h3{margin-bottom:15px;color:#24416b;font-size:18px;font-weight:700}.single-offer:hover .circle-read-more i{background-color:#fa4612;color:#fff}.circle-read-more i{background-color:#fedad0;width:40px;height:40px;line-height:40px;border-radius:50%;color:#fa4612;display:inline-block;text-align:center;transition:.5s;position:relative;z-index:1}</style>
<article class="main main--download">
 <article class="main main--download" style="padding-top:50px;">
    <section class="download-block download-block--latest-version latest-version">
        <div class="wrapper" style="max-width: fit-content;margin-left: auto;margin-right: auto;">
			<div class="avatar" style="max-width: fit-content;margin-left: auto;margin-right: auto;">
				<img src="<?= htmlspecialchars($userAvatarPath ?? '') ?>" alt="User Avatar" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
			</div>
		
            <h1 class="latest-version__title"><h1><?= htmlspecialchars($userUsername) ?>'s Profile</h1>
			<p><?= nl2br(htmlspecialchars($userBio ?? '')) ?></p>
			
			<small><?php if (!$hide_last_seen): ?>Last Seen: <?= htmlspecialchars($userLastSeen ?? '') ?><?php endif; ?>
			<br /><?php if (!$hide_registered): ?>Joined: <?= htmlspecialchars($userRegistered) ?><?php endif; ?></small>
        </div>
    </section>
</article>
    <section class="download-block support--support support">
        <div class="wrapper">
            <div class="support__actions">
                <p class="support__actions__item"><a href="<?= htmlspecialchars($userPublicForum ?? '') ?>" class="support__actions__item__link"><span class="icon"><svg class="w-[18px] h-[18px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M8.64 4.737A7.97 7.97 0 0 1 12 4a7.997 7.997 0 0 1 6.933 4.006h-.738c-.65 0-1.177.25-1.177.9 0 .33 0 2.04-2.026 2.008-1.972 0-1.972-1.732-1.972-2.008 0-1.429-.787-1.65-1.752-1.923-.374-.105-.774-.218-1.166-.411-1.004-.497-1.347-1.183-1.461-1.835ZM6 4a10.06 10.06 0 0 0-2.812 3.27A9.956 9.956 0 0 0 2 12c0 5.289 4.106 9.619 9.304 9.976l.054.004a10.12 10.12 0 0 0 1.155.007h.002a10.024 10.024 0 0 0 1.5-.19 9.925 9.925 0 0 0 2.259-.754 10.041 10.041 0 0 0 4.987-5.263A9.917 9.917 0 0 0 22 12a10.025 10.025 0 0 0-.315-2.5A10.001 10.001 0 0 0 12 2a9.964 9.964 0 0 0-6 2Zm13.372 11.113a2.575 2.575 0 0 0-.75-.112h-.217A3.405 3.405 0 0 0 15 18.405v1.014a8.027 8.027 0 0 0 4.372-4.307ZM12.114 20H12A8 8 0 0 1 5.1 7.95c.95.541 1.421 1.537 1.835 2.415.209.441.403.853.637 1.162.54.712 1.063 1.019 1.591 1.328.52.305 1.047.613 1.6 1.316 1.44 1.825 1.419 4.366 1.35 5.828Z" clip-rule="evenodd"/>
</svg>
</span>
 <span>Website</span></a>
                <p class="support__actions__item"><?php if (!$hide_email): ?><a href="mailto:<?= htmlspecialchars($userEmail) ?>" class="support__actions__item__link"><?php endif; ?><span class="icon"><svg class="w-[18px] h-[18px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path d="M17 6h-2V5h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2h-.541A5.965 5.965 0 0 1 14 10v4a1 1 0 1 1-2 0v-4c0-2.206-1.794-4-4-4-.075 0-.148.012-.22.028C7.686 6.022 7.596 6 7.5 6A4.505 4.505 0 0 0 3 10.5V16a1 1 0 0 0 1 1h7v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3h5a1 1 0 0 0 1-1v-6c0-2.206-1.794-4-4-4Zm-9 8.5H7a1 1 0 1 1 0-2h1a1 1 0 1 1 0 2Z"/>
</svg>
</span>
 <span>Email</span></a></p>
                <p class="support__actions__item"><a href="#" class="support__actions__item__link"><span class="icon"><svg class="w-[18px] h-[18px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M5 5a2 2 0 0 0-2 2v3a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a2 2 0 0 0-2-2H5Zm9 2a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H14Zm3 0a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17ZM3 17v-3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm11-2a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H14Zm3 0a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd"/>
</svg>
</span>
 <span>Forums</span></a>
            </div>
        </div>
		
    </section>
	</section>
	<section class="download-block download-block--latest-version latest-version" style="background:#b9b9b9;">
	<!-- User Created Forums Section -->
	<!-- User Created Forums Section -->	
	<?php if (!$hide_forums): ?>
	<section class="user-created-forums mt-4" style="background:#b9b9b9;">
   <div class="wrapper">
	<h1 class="latest-version__title"><?= htmlspecialchars($userUsername) ?>'s Newest Forums</h1>


  <div class="container_forums">
    <div class="row">   
	<?php if (!empty($userForums)): ?>
            <?php foreach ($userForums as $forumData): 
                $forumName = htmlspecialchars($forumData['name']);
				$forumDesc = htmlspecialchars($forumData['description']);
                $forumLink = htmlspecialchars($website_domain . '/forum/' . $forumData['subdomain']);
                $memberCount = is_numeric($forumData['members']) ? number_format($forumData['members']) : 'N/A';
            ?>
			    <div class="col-lg-3 col-sm-6 p-0" style="margin: 10px;">
				  <div class="single-offer" style="border-radius: 10%;">
					<i class="fa-solid fa-community"></i>
					<h3><?= $forumName ?></h3>
					<h5><p class="card-text">Members: <?= $memberCount ?></p></h5>
					<p><?= !empty($forumDesc) ? htmlspecialchars($forumDesc) : 'No Description' ?></p>
				  <a class="circle-read-more" style="background:#007fd0;color:#fff;padding: 5px;border-radius: 10%;" <a href="<?= $forumLink ?>">
				  Visit Forum</a>
				  </div>
				</div>						
                            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">This user hasn't created any forums yet.</p>
        <?php endif; ?>
            </div>
        </div>
	</div>
</section>

<?php endif; ?>

</article>
