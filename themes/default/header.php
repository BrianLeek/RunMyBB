<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= htmlspecialchars($page_title ?? 'No Title Found') ?></title>
    
    <meta name="description" content="<?= htmlspecialchars($page_desc ?? 'No description') ?>" />
	<meta name="keywords" content="<?= htmlspecialchars($page_keywords ?? '') ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=yes">
    <link href="https://fonts.googleapis.com/css?family=Asap:400,600,600i|Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://kit.fontawesome.com/2e7a3e8a8f.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= htmlspecialchars($website_domain) ?>/themes/default/assets/css/main30f4.css?v=3">
	<link rel="stylesheet" href="<?= htmlspecialchars($website_domain) ?>/themes/default/assets/css/customform.css">
</head>

<body class="section section--download" style="background:#ccc">
	<script>
		document.addEventListener('DOMContentLoaded', (event) => {
			var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
			tooltipTriggerList.forEach(function (tooltipTriggerEl) {
				new bootstrap.Tooltip(tooltipTriggerEl);
			});
		});
	</script>
	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

   <header class="header">
        <div class="wrapper">
            <h1 class="header__title">
                <a href="<?= htmlspecialchars($website_domain) ?>" class="logo logo--light  header__title__logo">
					<span class="logo__icon"><img src="<?= htmlspecialchars($website_domain) ?>/themes/default/assets/images/logo.png" alt="<?= $lang['site_logo_alt'] ?>" style="max-height: 120px; height: auto; width: auto;"/></span>
				</a>
            </h1>
            <label class="menu-button" for="show-mobile-menu"><span class="icon"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z"/></svg></span>
</span></label>
            <label class="menu-close-button menu-close-button--hide" for="show-mobile-menu"><span class="icon"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z"/></svg></span>
</span></label>
            <input type="checkbox" class="header__checkbox" id="show-mobile-menu">
            <input type="checkbox" class="header__checkbox" id="show-site-search">
            <nav class="main-menu">
                <ul class="main-menu__links">
                    <li class="main-menu__links__gap">&nbsp;</li>
                    <li class="main-menu__links__item main-menu__links__item--home">
                        <a href="<?= htmlspecialchars($website_domain) ?>/index" class="main-menu__links__item__link"><?= $lang['home'] ?></a>
                    </li>
                    <li class="main-menu__links__item main-menu__links__item--download main-menu__links__item">
                        <a href="<?= htmlspecialchars($website_domain) ?>/index#create_forum" class="main-menu__links__item__link"><?= $lang['create_forum'] ?></a>
                    </li>
					                    <li class="main-menu__links__item main-menu__links__item--home">
                        <a href="<?= htmlspecialchars($website_domain) ?>/about" class="main-menu__links__item__link"><?= $lang['about'] ?></a>
                    </li>
					                    <li class="main-menu__links__item main-menu__links__item--home">
                        <a href="<?= htmlspecialchars($website_domain) ?>/#" class="main-menu__links__item__link"><?= $lang['support'] ?></a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
