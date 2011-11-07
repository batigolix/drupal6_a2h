<?php // $Id: page.tpl.php,v 1.1 2009/05/15 07:28:05 agileware Exp $ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $language->language; ?>" xml:lang="<?php echo $language->language; ?>">

  <head>
    <title><?php if (isset($head_title )) { echo $head_title; } ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <?php echo $head; ?>
    <?php echo $styles ?>
    <?php echo $scripts ?>
    <!--[if IE 6]><link rel="stylesheet" href="<?php echo $base_path . $directory; ?>/style.ie6.css" type="text/css" /><![endif]-->  
    <!--[if IE 7]><link rel="stylesheet" href="<?php echo $base_path . $directory; ?>/style.ie7.css" type="text/css" media="screen" /><![endif]-->
    <script type="text/javascript"><?php /* Needed to avoid Flash of Unstyle Content in IE */ ?> </script>
  </head>

  <body>
    <div class="PageBackgroundSimpleGradient"></div>
    <div class="PageBackgroundGlare">
      <div class="PageBackgroundGlareImage"></div>
    </div>
    <div class="Main">
      <div class="Sheet">
        <div class="Sheet-cc"></div>
        <div class="Sheet-body">
          <div class="Header">
            <div class="Header-jpeg"></div>
            <div class="logo">
              <?php if ($logo): ?>
                <a href="<?php print $base_path ?>" title="<?php print t('Home') ?>"><img align="middle" src="<?php print $logo ?>" alt="<?php print t('Home') ?>" /></a>
              <?php endif; ?>
              </div>
              
             
              <div class="site_name"><?php if ($site_name): echo $site_name; endif; ?></div>
              <div class="site_slogan"><?php if ($site_slogan): echo $site_slogan; endif; ?></div>
            
          </div>
          <?php if ($navigation): ?>
            <div class="nav">
              <div class="l"></div>
              <div class="r"></div>
              <?php echo $navigation; ?>
            </div>
          <?php endif; ?>
          <div class="contentLayout">
            <div class="<?php echo (!empty($right) || !empty($sidebar_right)) ? 'content' : 'content-wide'; ?>">
                      <div class="PreContent">
                        <?php if ($pre_content) { echo $pre_content; } ?>
                      </div>
              <div class="Post">
                <div class="Post-body">
                  <div class="Post-inner">
                    <div class="PostContent">
                      <?php // if ($breadcrumb): echo theme('breadcrumb', $breadcrumb); endif; ?>
                      <?php if ($tabs): echo '<div id="tabs-wrapper" class="clear-block">'; endif; ?>
                      <?php if ($title): echo '<h2 class="PostHeaderIcon-wrapper'. ($tabs ? ' with-tabs' : '') .'">'. $title .'</h2>'; endif; ?>
                      <?php if ($tabs): echo $tabs . '</div>'; endif; ?>
                      <?php if ($tabs2): echo '<ul class="tabs secondary">'. $tabs2 .'</ul>'; endif; ?>
                      <?php if ($mission): echo '<div id="mission">' . $mission . '</div>'; endif; ?>
                      <?php if ($help): echo $help; endif; ?>
                      <?php if ($show_messages && $messages): echo $messages; endif; ?>
                      <?php echo art_content_replace($content); ?>
                    </div>
                    <div class="cleared"></div>
                  </div>
                </div>
              </div>
            </div>
            <?php if ($right) echo '<div class="sidebar1">' . $right . "</div>"; 
                  else if ($sidebar_right) echo '<div class="sidebar1">' . $sidebar_right . "</div>";?>
          </div>
          <div class="cleared"></div>
          <div class="Footer">
            <div class="Footer-inner">
              <a href="<?php $feedsUrls = array_keys(drupal_add_feed()); if(isset($feedsUrls[0]) && strlen($feedsUrls[0])>0) {echo $feedsUrls[0];} ?>" class="rss-tag-icon" title="RSS"></a>
              <div class="Footer-text">
                <?php if ($footer) { echo $footer; } ?>
              </div>
            </div>
            <div class="Footer-background"></div>
          </div>
        </div>
      </div>
      <div class="cleared"></div>
      <p class="page-footer"><?php echo $footer_message; ?></p>
    </div>
    <?php print $closure; ?>
  </body>
</html>