<?php $barname = $GLOBALS["barname"]; ?>
<?php if( strlen($barname) > 0 ) : ?>
   <?php if( is_dynamic_sidebar('sidebar-landing') ) : ?>
      <?php dynamic_sidebar('sidebar-landing') ?>
   <?php endif; ?>
<?php endif; ?>
