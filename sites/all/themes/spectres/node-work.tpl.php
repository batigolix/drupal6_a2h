<?php if ($teaser): ?>
  <!-- teaser template HTML here -->
    <div class="work-teaser">
      <?php echo "<a href=\"/node/" . $node->nid . "\">" . $node->node_images . "</a>"; ?>
      <br />
      <p><?php echo l($node->title, 'node/'. $node->nid, array('title' => t('View object')));?></p>
    </div>
	
<?php else: ?>
  <!-- regular node view template HTML here --> 
  <div class="content object">
<!--    <div class="taxonomy"><?php print $terms?></div> -->
    <div class="content"><?php print $content?></div>
<!--    <?php echo l(t('View all images'), 'node/'.$node->nid.'/image_gallery');?> -->
    <div class="clear-block clear"></div>
<!--    <?php if ($links) { ?><div class="links">&raquo; <?php print $links?></div><?php }; ?> -->
  </div>

<?php endif; ?>