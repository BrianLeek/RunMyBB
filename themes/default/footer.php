<?php include 'config.php'; ?>
    <footer class="footer">
        <div class="wrapper">
            <nav class="footer__nav footer__nav--internal">
                <ul class="footer__nav__links">
                    <li class="footer__nav__links__item">
                        <a href="<?= htmlspecialchars($website_domain) ?>/index" class="footer__nav__links__item__link"><?= $lang['home'] ?></a>
                    </li>
                    <li class="footer__nav__links__item">
                        <a href="<?= htmlspecialchars($website_domain) ?>/about" class="footer__nav__links__item__link"><?= $lang['about'] ?></a>
                    </li>
                    <li class="footer__nav__links__item">
                        <a href="<?= htmlspecialchars($website_domain) ?>/mybb_features" class="footer__nav__links__item__link"><?= $lang['mybb_features'] ?></a>
                    </li>
                    <li class="footer__nav__links__item">
                        <a href="<?= htmlspecialchars($website_domain) ?>/support" class="footer__nav__links__item__link"><?= $lang['support'] ?></a>
                    </li>
                    <li class="footer__nav__links__item">
                        <a href="<?= htmlspecialchars($website_domain) ?>/contact" class="footer__nav__links__item__link"><?= $lang['contact'] ?></a>
                    </li>
                </ul>
            </nav>
        </div>
    </footer>
    <div class="copyright">
        Copyright &copy; <?php echo date("Y"); ?> RunMyBB
    </div>
    <script src="ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="../assets/js/libs/jquery-3.2.1.min.html"><\/script>')</script>
    <script defer src="<?= htmlspecialchars($website_domain) ?>/themes/default/assets/js/plugins.js"></script>
	<script defer src="<?= htmlspecialchars($website_domain) ?>/themes/default/assets/js/script5e1f.js?v=2"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
	<script>
	$(function(){
    
    $('.form-control').each(function(){
        changeClass($(this));
    });
  
    $('.form-control').on('focusout', function(){
      
        changeClass($(this));
    });
  function changeClass($formcontrol){
    if($formcontrol.val().length > 0){
          $formcontrol.addClass('has-value');
        }
        else{
          $formcontrol.removeClass('has-value');
        }
  }
  $('.datepicker').datepicker();
});
</script>
</body>
</html>
