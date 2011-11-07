<?php // $Id: node.tpl.php,v 1.3 2010/12/31 21:15:42 jimmyax Exp $ ?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> Post">
  <div class="Post-body">
    <div class="Post-inner">
      <?php if (!$page): ?>
        <h2 class="PostHeaderIcon-wrapper"><span class="PostHeader"><a href="<?php echo $node_url; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a></span></h2>
      <?php endif; ?>
      <?php if ($submitted): ?>
      <div class="PostMetadataHeader">
        <div class="PostHeaderIcons metadata-icons">
          <?php echo art_submitted_worker($submitted, $date, $name); ?>
        </div>
      </div>
      <?php endif; ?>
      <div class="PostContent">
        <div class="article">
          <?php echo $content; ?>
          <?php if (isset($node->links['node_read_more'])) echo '<div class="read_more">' . get_html_link_output($node->links['node_read_more']) . '</div>'; ?>
        </div>
      </div>
      <div class="cleared"></div>
      <?php ob_start(); ?>
        <?php if ($links) echo art_links_woker($node->links); ?>
        <?php if ($terms) echo art_terms_worker($node); ?>
      <?php $metadataContent = ob_get_clean(); ?>
      <?php if (trim($metadataContent) != ''): ?>
      <div class="PostMetadataFooter">
        <div class="PostMetadataFooter">
          <?php echo $metadataContent; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
