<?php $barname = $GLOBALS["barname"]; ?>
<?php if( strlen($barname) > 0 ) : ?>
   <?php if( is_dynamic_sidebar('sidebar-'.$barname) ) : ?>
      <?php dynamic_sidebar('sidebar-'.$barname) ?>
   <?php endif; ?>
<?php endif; ?>
