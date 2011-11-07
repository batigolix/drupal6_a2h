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



<?php
$nid = $node->nid;
//print '<pre>';
//print_r(get_defined_vars());
//print '</pre>';
?>




<div id="container">



<table border="0" cellpadding="0" cellspacing="0" id="content"  class="page-<?php print $node->nid ?>">
  <tr>
    <td id="sidebar-left" valign="top">
      <?php if ($logo) { ?><a href="<?php print $front_page ?>" title="<?php print t('Home') ?>"><img src="<?php print $logo ?>" alt="<?php print t('Home') ?>" /></a><?php } ?>
      <?php if ($site_name) { ?><div class='site-name'><a href="<?php print $front_page ?>" title="<?php print t('Home') ?>"><?php print $site_name ?></a></div><?php } ?>
      <?php if ($site_slogan) { ?><div class='site-slogan'><?php print $site_slogan ?></div><?php } ?>
      <?php if ($left) { ?><?php print $left ?><?php } ?>
      <?php if (isset($secondary_links)) { ?><?php print theme('links', $secondary_links, array('class' => 'links', 'id' => 'subnavlist')) ?><?php } ?>
      <?php if (isset($primary_links)) { ?><?php print theme('links', $primary_links, array('class' => 'links', 'id' => 'navlist')) ?><?php } ?>
      <?php print $search_box ?>
    </td>
    <td id="another-column" valign="top"></td>
    <td id="center-column" valign="top">
      <?php if ($mission) { ?><div id="mission"><?php print $mission ?></div><?php } ?>
      <div id="main">
<!--        <?php print $breadcrumb ?> -->
        <div class="title"><?php print $title ?></div>
        <div class="tabs"><?php print $tabs ?></div>
        <?php if ($show_messages) { print $messages; } ?>
        <?php print $help ?>

        <?php print $content; ?>

<!--        <?php print $feed_icons; ?> -->
      </div>

<div id="footer">
  <?php print $footer_message ?>
  <?php print $footer ?>
</div>

   </td>
    <?php if ($right) { ?><td id="sidebar-right" valign="top">
      <?php print $right ?>
    </td><?php } ?>
  </tr>
</table>

<?php print $closure ?>
</div>
</body>
</html>
