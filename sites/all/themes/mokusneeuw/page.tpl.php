<?php
// $Id: page.tpl.php,v 1.28 2008/01/24 09:42:52 goba Exp $
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language ?>" xml:lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">

<head>
  <title><?php print $head_title ?></title>
  <?php print $head ?>
  <?php print $styles ?>
  <?php print $scripts ?>
  <script type="text/javascript"><?php /* Needed to avoid Flash of Unstyle Content in IE */ ?> </script>
</head>

<body>

  <div id="page"><div id="page-inner">
            <?php if ($tabs): ?>
              <div class="tabs"><?php print $tabs; ?></div>
            <?php endif; ?>

    <div id="header"><div id="header-inner" class="clear-block">

      <?php if ($logo || $site_name || $site_slogan): ?>
        <div id="logo-title">

             <div id="logo"><a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home"><img src="<?php print $logo; ?>" alt="<?php print $site_name; ?>" id="logo-image" /></a></div>

          <?php if ($site_slogan): ?>
            <div id='site-slogan'><?php print $site_slogan; ?></div>
          <?php endif; ?>

        </div> <!-- /#logo-title -->
      <?php endif; ?>

      <?php if ($header): ?>
        <div id="header-blocks">
          <?php print $header; ?>
        </div> <!-- /#header-blocks -->
      <?php endif; ?>

    </div></div> <!-- /#header-inner, /#header -->

      <?php if ($search_box || $primary_links || $secondary_links || $navbar): ?>
        <div id="navbar"><div id="navbar-inner">
        <?php if (isset($primary_links)) { ?><?php print theme('links', $primary_links, array('class' => 'links', 'id' => 'navlist')) ?><?php } ?>

          <?php if ($search_box): ?>
            <div id="search-box">
              <?php print $search_box; ?>
            </div> <!-- /#search-box -->
          <?php endif; ?>

          <?php print $navbar; ?>

        </div></div> <!-- /#navbar-inner, /#navbar -->

        <div id="sec-navbar"><div id="sec-navbar-inner">
         <?php if (isset($secondary_links)) { ?><?php print theme('links', $secondary_links, array('class' => 'links', 'id' => 'navlist')) ?><?php } ?>
        </div></div> <!-- /#sec-navbar-inner, /#sec-navbar -->


      <?php endif; ?>


    <div id="main"><div id="main-inner" class="clear-block<?php if ($search_box || $primary_links || $secondary_links || $navbar) { print ' with-navbar'; } ?>">



      <?php if ($left): ?>
        <div id="sidebar-left"><div id="sidebar-left-inner">
          <?php print $left; ?>
        </div></div> <!-- /#sidebar-left-inner, /#sidebar-left -->
      <?php endif; ?>

      <?php if ($right): ?>
        <div id="sidebar-right"><div id="sidebar-right-inner">
          <?php print $right; ?>
        </div></div> <!-- /#sidebar-right-inner, /#sidebar-right -->
      <?php endif; ?>


      <div id="content"><div id="content-inner">

        <?php if ($mission): ?>
          <div id="mission"><?php print $mission; ?></div>
        <?php endif; ?>

        <?php if ($content_top): ?>
          <div id="content-top">
            <?php print $content_top; ?>
          </div> <!-- /#content-top -->
        <?php endif; ?>

        <?php if ($breadcrumb or $title or $tabs or $help or $messages): ?>
          <div id="content-header">
            <?php // print $breadcrumb; ?>
            <?php if ($title): ?>
              <h1 class="title"><?php print $title; ?></h1>
            <?php endif; ?>



            <?php print $messages; ?>
            <?php print $help; ?>
          </div> <!-- /#content-header -->
        <?php endif; ?>

        <div id="content-area">
          <?php print $content; ?>
        </div>

        <?php if ($feed_icons): ?>
          <div class="feed-icons"><?php print $feed_icons; ?></div>
        <?php endif; ?>

        <?php if ($content_bottom): ?>
          <div id="content_bottom">
            <?php print $content_bottom; ?>
          </div> <!-- /#content-bottom -->
        <?php endif; ?>

      </div></div> <!-- /#content-inner, /#content -->

        <?php if ($bottom): ?>
        <div id="bottom"><div id="bottom-inner">
          <?php print $bottom; ?>
        </div></div> <!-- /#bottom-inner, /#bottom -->
      <?php endif; ?>

    </div></div> <!-- /#main-inner, /#main -->

<?php print $closure ?>
</div>
</div>

<div id="footer">
  <?php print $footer_message ?>
  <?php print $footer ?>
</div>


</body>
</html>
