  <?php if ($teaser): ?>
    <!-- teaser template HTML here -->
      <div class="object-teaser">
       <div class="object-teaser-image"><a href="/<?php echo $node->path;?>"><?php echo $node->node_images;?></a></div>
       <div class="object-teaser-text">
          <h2><?php echo l($node->title, 'node/'. $node->nid, array('title' => t('View object')));?></h2>
          <?php if ($node->content['body']['#value']) { echo '<p class="object-teaser-description">'. $node->content['body']['#value'] .'</p>'; } ?>
          <?php if ($node->content['field_prijs']['#value']) { ?>
            <p class="object-teaser-value"><?php echo t('Price'); ?><sup><a href="#"></a></sup>: <?php echo $node->field_prijs[0]['view'];?></p>
          <?php } ?>
         </div>
      </div> 

  <?php else: ?>
    <!-- regular node view template HTML here --> 
      <div class="content object">
        <?php if ($node->content['body']['#value']) {
          echo '<div class="object-description">'. $node->content['body']['#value'] .'</div>';
          }
        ?>
        <div class="object-content-images">
          <?php echo $node->node_images;?> <br /><br />
        <?php // echo l(t('View all images'), 'node/'.$node->nid.'/image_gallery');?>        
        </div>
        <?php if ($node->content['body']['#value']) { ?>
          <p>
            <span class="object-label"><?php echo t('Price'); ?> :</span> 
            <span class="object-value"><?php echo $node->field_prijs[0]['view'];?></span>
          </p>
         <?php } ?>
         <p>
          <span class="object-label"><?php echo t('Weight'); ?> :</span>
          <span class="object-value"><?php echo $node->field_gewicht[0]['view'];?></span>
        </p>
        <p>
          <span class="object-label"><?php echo t('Article number'); ?> :</span> 
          <span class="object-value"><?php echo $node->nid;?></span>
        </p>

 
        <?php if ($taxonomy) { ?>
          <div class="terms">
		   <p>
            <span class="object-label"><?php echo t('Categories'); ?> :</span><br />
            <span class="object-value">
              <?php foreach($node->taxonomy as $tid => $taxo)
                $taxo_links[] = l($taxo->name,"taxonomy/term/$taxo->tid", array('title' => $taxo->name));
                print implode(', ',$taxo_links);
              ?></span>
            </p>

		  <p class="form-link">
           <a href="/content/contact-opnemen?artnr=<?php echo $node->nid;?>&artnaam=<?php echo urlencode($node->title);?>&subject=Bestelling"><?php echo t('Order this product'); ?></a>
          </p>
		  <p class="form-link">
           <a href="/content/contact-opnemen?artnr=<?php echo $node->nid;?>&artnaam=<?php echo urlencode($node->title);?>&subject=Vraag"><?php echo t('Ask question about product'); ?></a>
          </p>

          </div>
        <?php } ?>
         
      </div>
      <div class="clear-block clear"></div>


  <?php endif; ?>