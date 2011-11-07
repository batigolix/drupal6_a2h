<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language ?>" xml:lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">

<head>
  <title><?php print $head_title ?></title>
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <?php print $head ?>
  <?php print $styles ?>

</head>

<body bgcolor="#ffffff">

<div class="hide"><a href="#content" title="<?php print t('Skip navigation') ?>." accesskey="2"><?php print t('Skip navigation') ?></a>.</div>

<div id="container">

    <div id="logo">
      <?php if ($logo) : ?>
        <a href="<?php print $base_path ?>" title="<?php print t('Home') ?>"><img src="<?php print($logo) ?>" alt="<?php print t('Home') ?>" border="0" /></a>
      <?php endif; ?>
    </div>

<div id="primary-menu" summary="Navigation elements.">

    <span class="primary-links">
      <?php print theme('links', $primary_links) ?>
    </span>

    <span id="site-info" >
      <?php if ($site_name) : ?>
        <div class='site-name'><a href="<?php print $base_path ?>" title="<?php print t('Home') ?>"><?php print($site_name) ?></a></div>
      <?php endif;?>
      <?php if ($site_slogan) : ?>
        <div class='site-slogan'><?php print($site_slogan) ?></div>
      <?php endif;?>
    </span>

</div>

<div id="secondary-menu" summary="Navigation elements.">
    <span class="secondary-links">
      <?php print theme('links', $secondary_links) ?>
    </span>
    <span>
      <?php print $search_box ?>
    </span>
    <span><div><?php print $header ?></div>
  </span>
</div>

<table id="content" border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <?php if ($left): ?>
    <td id="sidebar-left" valign="top">
      <?php print $left ?>
    </td>
    <?php endif; ?>

    <td valign="top">
      <?php if ($mission != ""): ?>
      <div id="mission"><?php print $mission ?></div>
      <?php endif; ?>

      <div id="main">
        <?php if ($title != ""): ?>
          <?php print $breadcrumb ?>
          <h1 class="title"><?php print $title ?></h1>

          <?php if ($tabs != ""): ?>
            <div class="tabs"><?php print $tabs ?></div>
          <?php endif; ?>

        <?php endif; ?>

        <?php if ($help != ""): ?>
            <div id="help"><?php print $help ?></div>
        <?php endif; ?>

        <?php if ($messages != ""): ?>
          <?php print $messages ?>
        <?php endif; ?>

      <!-- start main content -->
      <?php print($content) ?>
      <!-- end main content -->

      </div><!-- main -->
    </td>
    <?php if ($right != ""): ?>
    <td id="sidebar-right" valign="top">
      <?php print $right ?>
    </td>
    <?php endif; ?>
  </tr>
</table>

</div>

<?php if ($footer_message) : ?>
<div id="footer-message">
    <p><?php print $footer_message;?></p>
</div>
<?php endif; ?>
<?php print $closure;?>

</body>
</html>
