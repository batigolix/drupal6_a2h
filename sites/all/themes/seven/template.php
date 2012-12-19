<?php

/**
 * Override or insert variables into the page template.
 */
function seven_preprocess_page(&$vars) {
  $vars['primary_local_tasks'] = menu_primary_local_tasks();
  $vars['secondary_local_tasks'] = menu_secondary_local_tasks();
}

/**
 * Display the list of available node types for node creation.
 */
function seven_node_add_list($content) {
  $output = '';
  if ($content) {
    $output = '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="clearfix">';
      $output .= '<span class="label">' . l($item['title'], $item['href'], $item['localized_options']) . '</span>';
      $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  else {
    $output = '<p>' . t('You have not created any content types yet. Go to the <a href="@create-content">content type creation page</a> to add a new content type.', array('@create-content' => url('admin/structure/types/add'))) . '</p>';
  }
  return $output;
}

/**
 * Overrides theme_admin_block_content().
 *
 * Use unordered list markup in both compact and extended mode.
 */
function seven_admin_block_content($content) {
  $output = '';
  if (!empty($content)) {
    $output = system_admin_compact_mode() ? '<ul class="admin-list compact">' : '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="leaf">';
      $output .= l($item['title'], $item['href'], $item['localized_options']);
      if (isset($item['description']) && !system_admin_compact_mode()) {
        $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      }
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  return $output;
}

/**
 * Override of theme_tablesort_indicator().
 *
 * Use our own image versions, so they show up as black and not gray on gray.
 */
function seven_tablesort_indicator($style) {
  $theme_path = drupal_get_path('theme', 'seven');
  if ($style == 'asc') {
    return theme('image', $theme_path . '/images/arrow-asc.png', t('sort ascending'), t('sort ascending'));
  }
  else {
    return theme('image', $theme_path . '/images/arrow-desc.png', t('sort descending'), t('sort descending'));
  }
}

/**
 * Override of theme_fieldset().
 *
 * Add span to legend tag, so we can style it to be inside the fieldset.
 */
function seven_fieldset($element) {
  if (!empty($element['#collapsible'])) {
    drupal_add_js('misc/collapse.js');

    if (!isset($element['#attributes']['class'])) {
      $element['#attributes']['class'] = '';
    }

    $element['#attributes']['class'] .= ' collapsible';
    if (!empty($element['#collapsed'])) {
      $element['#attributes']['class'] .= ' collapsed';
    }
  }

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    // Always wrap fieldset legends in a SPAN for CSS positioning.
    $output .= '<legend><span class="fieldset-legend">' . $element['#title'] . '</span></legend>';
  }
  // Add a wrapper if this fieldset is not collapsible.
  // theme_fieldset() in D7 adds a wrapper to all fieldsets, however in D6 this
  // wrapper is added by Drupal.behaviors.collapse(), but only to collapsible
  // fieldsets. So to ensure the wrapper is consistantly added here we add the
  // wrapper to the markup, but only for non collapsible fieldsets.
  if (empty($element['#collapsible'])) {
    $output .= '<div class="fieldset-wrapper">';
  }
  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . '</div>';
  }
  if (isset($element['#children'])) {
    $output .= $element['#children'];
  }
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  if (empty($element['#collapsible'])) {
    $output .= '</div>';
  }
  $output .= "</fieldset>\n";
  return $output;
}

function seven_img_assist_inline($node, $size, $attributes) {
  $caption = '';
  if ($attributes['title'] && $attributes['desc']) {
    $caption = '<strong>'. $attributes['title'] .': </strong>'. $attributes['desc'];
  }
  elseif ($attributes['title']) {
    $caption = '<strong>'. $attributes['title'] .'</strong>';
  }
  elseif ($attributes['desc']) {
    $caption = $attributes['desc'];
  }
  // Change the node title because img_assist_display() uses the node title for
  // alt and title.
  $node->title = strip_tags($caption);
  $img_tag = img_assist_display($node, $size);
  
  // Always define an alignment class, even if it is 'none'.
  $output = '<span class="inline inline-'. $attributes['align'] .'">';

  $link = $attributes['link'];
  $url  = '';
  // Backwards compatibility: Also parse link/url in the format link=url,foo.
  if (strpos($link, ',') !== FALSE) {
    list($link, $url) = explode(',', $link, 2);
  }
  elseif (isset($attributes['url'])) {
    $url = $attributes['url'];
  }
  
  if ($link == 'node') {
    $output .= l($img_tag, 'node/'. $node->nid, array('html' => TRUE));
  }
  elseif ($link == 'popup') {
    $popup_size = variable_get('img_assist_popup_label', IMAGE_PREVIEW);
    $info       = image_get_info(file_create_path($node->images[$popup_size]));
	dpm($info);
    $width      = $info['width'];
    $height     = $info['height'];
    $popup_url  = file_create_url($node->images[variable_get('img_assist_popup_label', IMAGE_PREVIEW)]);
    $output .= l($img_tag, $popup_url, array('attributes' => array('onclick' => "launch_popup({$node->nid}, {$width}, {$height}); return false;", 'target' => '_blank'), 'html' =>TRUE));
  }
  elseif ($link == 'url') {
    $output .= l($img_tag, $url, array('html' => TRUE));
  }
  else {
    $output .= $img_tag;
  }
  
  if ($caption) {
    if ($attributes['align'] != 'center') {
      $info = image_get_info(file_create_path($node->images[$size['key']]));
      // Reduce the caption width slightly so the variable width of the text
      // doesn't ever exceed image width.
      $width = $info['width'] - 2;
      $output .= '<span class="caption" style="width: '. $width .'px;">'. $caption .'</span>';
    }
    else {
      $output .= '<span class="caption">'. $caption .'</span>';
    }
  }
  $output .= '</span>';
  return $output;
}
