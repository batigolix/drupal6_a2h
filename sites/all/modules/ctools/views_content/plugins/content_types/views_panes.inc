<?php
// $Id: views_panes.inc,v 1.11 2009/05/19 19:30:22 merlinofchaos Exp $

/**
 * @file
 * Content type plugin to allow Views to be exposed as a display type,
 * leaving most of the configuration on the view.
 */

/**
 * Implementation of hook_ctools_content_types()
 */
function views_content_views_panes_ctools_content_types() {
  return array(
    'title' => t('View panes'),
    'admin settings' => 'views_content_admin_form',
    'js' => array(drupal_get_path('module', 'ctools') . '/js/dependent.js'),
  );
}

/**
 * Return all content types available.
 */
function views_content_views_panes_content_type_content_types($plugin) {
  $types = array();
  // It can be fairly intensive to calculate this, so let's cache this in the
  // cache_views table. The nice thing there is that if views ever change, that
  // table will always be cleared. Except for the occasional default view, so
  // we must use the Views caching functions in order to respect Views caching
  // settings.
  views_include('cache');
  $data = views_cache_get('views_content_panes', TRUE);
  if (!empty($data->data)) {
    $types = $data->data;
  }

  if (empty($types)) {
    $types = array();

    $views = views_get_all_views();

    foreach ($views as $view) {
      if (!empty($view->disabled)) {
        continue;
      }

      $view->init_display();

      foreach ($view->display as $id => $display) {
        if (empty($display->handler->panel_pane_display)) {
          continue;
        }
        $types[$view->name . '-' . $id] = _views_content_panes_content_type($view, $display);
      }

      $view->destroy();
    }
    views_cache_set('views_content_panes', $types, TRUE);
  }

  return $types;
}

/**
 * Return a single content type.
 */
function views_content_views_panes_content_type_content_type($subtype, $plugin) {
  list($name, $display) = explode('-', $subtype);
  $view = views_get_view($name);
  if (empty($view)) {
    return;
  }

  $view->set_display($display);
  $retval = _views_content_panes_content_type($view, $view->display[$display]);

  $view->destroy();
  return $retval;
}

function _views_content_panes_content_type($view, $display) {
  $title = $display->handler->get_option('pane_title');
  if (!$title) {
    $title = $view->name;
  }

  $description = $display->handler->get_option('pane_description');
  if (!$description) {
    $description = $view->description;
  }

  $category = $display->handler->get_option('pane_category');
  if (!$category['name']) {
    $category['name'] = t('View panes');
  }

  $icon = 'icon_views_page.png';

  $contexts = array();

  $arguments = $display->handler->get_argument_input();
  foreach ($arguments as $argument) {
    if ($argument['type'] == 'context') {
      if (strpos($argument['context'], '.')) {
        list($context, $converter) = explode('.', $argument['context'], 2);
      }
      else {
        $context = $argument['context'];
      }
      $contexts[] = new ctools_context_required($argument['label'], $context);
    }
  }

  $allow = $display->handler->get_option('allow');
  return array(
    'title' => $title,
    'icon' => $icon,
    'description' => filter_xss_admin($description),
    'required context' => $contexts,
    'category' => array($category['name'], $category['weight']),
    'no title override' => empty($allow['title_override']),
  );
}

/**
 * Output function for the 'views' content type.
 *
 * Outputs a view based on the module and delta supplied in the configuration.
 */
function views_content_views_panes_content_type_render($subtype, $conf, $panel_args, $contexts) {
  if (!is_array($contexts)) {
    $contexts = array($contexts);
  }

  list($name, $display) = explode('-', $subtype);
  $view = views_get_view($name);
  if (empty($view)) {
    return;
  }

  $view->set_display($display);
  if (!$view->display_handler->access($GLOBALS['user'])) {
    return;
  }

  $args = array();
  $arguments = $view->display_handler->get_option('arguments');

  $context_keys = array_keys($contexts);
  foreach ($view->display_handler->get_argument_input() as $id => $argument) {
    switch ($argument['type']) {
      case 'context':
        $key = array_shift($context_keys);
        if (isset($context[$key])) {
          if (strpos($argument['context'], '.')) {
            list($context, $converter) = explode('.', $argument['context'], 2);
            $args[] = ctools_context_convert_context($contexts[$key], $converter);
          }
          else {
            $args[] = $contexts[$key]->argument;
          }
        }
        break;

      case 'fixed':
        $args[] = $argument['fixed'];
        break;

      case 'panel':
        $args[] = $panel_args[$argument['panel']];
        break;

      case 'user':
        $args[] = isset($conf['arguments'][$id]) ? $conf['arguments'][$id] : NULL;
        break;

     case 'wildcard':
        // Put in the wildcard.
       $args[] = isset($arguments[$id]['wildcard']) ? $arguments[$id]['wildcard'] : '*';
       break;

     case 'none':
     default:
       // Put in NULL.
       // views.module knows what to do with NULL (or missing) arguments
       $args[] = NULL;
       break;
    }
  }

  // remove any trailing NULL arguments as these are non-args:
  while (count($args) && end($args) === NULL) {
    array_pop($args);
  }
  $view->set_arguments($args);

  $allow = $view->display_handler->get_option('allow');

  if ($allow['path_override'] && !empty($conf['path'])) {
    $view->override_path = $conf['path'];
  }

  $block = new stdClass();
  $block->module = 'views';
  $block->delta  = $view->name . $display;

  if (($allow['link_to_view'] && !empty($conf['link_to_view'])) ||
      (!$allow['link_to_view'] && $view->display_handler->get_option('link_to_view'))) {
    $block->title_link = $view->get_url();
  }

  // more link
  if (($allow['more_link'] && !empty($conf['more_link'])) ||
      (!$allow['more_link'] && $view->display_handler->get_option('more_link'))) {
    $block->more = array('href' => $view->get_url());
    $view->display_handler->set_option('use_more', FALSE);
    // make sure the view runs the count query so we know whether or not the more link
    // applies.
    $view->get_total_rows = TRUE;
  }

  if ($allow['use_pager']) {
    // Only set use_pager if they differ, this way we can avoid overwriting the
    // pager type that Views uses.
    if (!$view->display_handler->get_option('use_pager') || empty($conf['use_pager'])) {
      $view->display_handler->set_option('use_pager', $conf['use_pager']);
    }
    $view->display_handler->set_option('pager_element', $conf['pager_id']);
  }
  if ($allow['items_per_page'] && isset($conf['items_per_page'])) {
    $view->display_handler->set_option('items_per_page', $conf['items_per_page']);
  }
  if ($allow['offset']) {
    $view->display_handler->set_option('offset', $conf['offset']);
  }

  $stored_feeds = drupal_add_feed();

  $block->content = $view->preview();
  if (empty($view->result) && !$view->display_handler->get_option('empty') && empty($view->style_plugin->definition['even empty'])) {
    return;
  }

  $block->title = $view->get_title();

  if (empty($view->total_rows) || $view->total_rows <= $view->display_handler->get_option('items_per_page')) {
    unset($block->more);
  }

  if ((!empty($allow['feed_icons']) && !empty($conf['feed_icons'])) ||
      (empty($allow['feed_icons']) && $view->display_handler->get_option('feed_icons'))) {
    $new_feeds = drupal_add_feed();
    if ($diff = array_diff(array_keys($new_feeds), array_keys($stored_feeds))) {
      foreach ($diff as $url) {
        $block->feeds[$url] = $new_feeds[$url];
      }
    }
  }

  return $block;
}

/**
 * Returns an edit form for a block.
 */
function views_content_views_panes_content_type_edit_form(&$form, &$form_state) {
  $conf = $form_state['conf'];
  // This allows older content to continue to work, where we used to embed
  // the display directly.
  list($name, $display_id) = explode('-', $form_state['subtype_name']);
  $view = views_get_view($name);

  if (empty($view)) {
    $form['markup'] = array('#value' => t('Broken/missing/deleted view.'));
    return;
  }

  $view->set_display($display_id);

  $allow = $view->display_handler->get_option('allow');

  // Provide defaults for everything in order to prevent warnings.
  if (empty($conf)) {
    $conf['link_to_view'] = $view->display_handler->get_option('link_to_view');
    $conf['more_link'] = $view->display_handler->get_option('more_link');
    $conf['feed_icons'] = FALSE;
    $conf['use_pager'] = $view->display_handler->get_option('use_pager');
    $conf['pager_id'] = $view->display_handler->get_option('element_id');
    $conf['items_per_page'] = $view->display_handler->get_option('items_per_page');
    $conf['offset'] = $view->display_handler->get_option('offset');
    $conf['path_override'] = FALSE;
    $conf['path'] = $view->get_path();
  }

  $form['arguments']['#tree'] = TRUE;

  foreach ($view->display_handler->get_argument_input() as $id => $argument) {
    if ($argument['type'] == 'user') {
      $form['arguments'][$id] = array(
        '#type' => 'textfield',
        '#default_value' => isset($conf['arguments'][$id]) ? $conf['arguments'][$id] : '',
        '#title' => $argument['label'],
      );
    }
  }
  if ($view->display_handler->has_path()) {
    if ($allow['link_to_view'] ) {
      $form['link_to_view'] = array(
        '#type' => 'checkbox',
        '#default_value' => isset($conf['link_to_view']) ? $conf['link_to_view'] : $view->display_handler->get_option('link_to_view'),
        '#title' => t('Link title to page'),
      );
    }
    if ($allow['more_link']) {
      $form['more_link'] = array(
        '#type' => 'checkbox',
        '#default_value' => isset($conf['more_link']) ? $conf['more_link'] : $view->display_handler->get_option('more_link'),
        '#title' => t('Provide a "more" link.'),
      );
    }
  }

  if (!empty($allow['feed_icons'])) {
    $form['feed_icons'] = array(
      '#type' => 'checkbox',
      '#default_value' => !empty($conf['feed_icons']),
      '#title' => t('Display feed icons'),
    );
  }

  ctools_include('dependent');
  if ($allow['use_pager']) {
    $form['pager_aligner_start'] = array(
      '#value' => '<div class="option-text-aligner">',
    );
    $form['use_pager'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use pager'),
        '#default_value' => isset($conf['use_pager']) ? $conf['use_pager'] : $view->display_handler->get_option('use_pager'),
      '#id' => 'use-pager-checkbox',
    );
    $form['pager_id'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($conf['pager_id']) ? $conf['pager_id'] : $view->display_handler->get_option('element_id'),
      '#title' => t('Pager ID'),
      '#size' => 4,
      '#id' => 'use-pager-textfield',
      '#process' => array('ctools_dependent_process'),
      '#dependency' => array('use-pager-checkbox' => array(1)),
    );
    $form['pager_aligner_stop'] = array(
      '#value' => '</div><div style="clear: both; padding: 0; margin: 0"></div>',
    );
  }
  if ($allow['items_per_page']) {
    $form['items_per_page'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($conf['items_per_page']) ? $conf['items_per_page'] : $view->display_handler->get_option('items_per_page'),
      '#title' => t('Num items'),
      '#size' => 4,
      '#description' => t('Select the number of items to display, or 0 to display all results.'),
    );
  }
  if ($allow['offset']) {
    $form['offset'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($conf['offset']) ? $conf['offset'] : $view->display_handler->get_option('offset'),
      '#title' => t('Offset'),
      '#size' => 4,
      '#description' => t('Enter the number of items to skip; enter 0 to skip no items.'),
    );
  }
  if ($allow['path_override']) {
    $form['path'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($conf['path']) ? $conf['path'] : $view->get_path(),
      '#title' => t('Override path'),
      '#size' => 30,
      '#description' => t('If this is set, override the View URL path; this can sometimes be useful to set to the panel URL.'),
    );
  }
}

/**
 * Store form values in $conf.
 */
function views_content_views_panes_content_type_edit_form_submit(&$form, &$form_state) {
  // Copy everything from our defaults.
  $keys = array('link_to_view', 'more_link', 'feed_icons', 'use_pager',
    'pager_id', 'items_per_page', 'offset', 'path_override', 'path', 'arguments');

  foreach ($keys as $key) {
    if (isset($form_state['values'][$key])) {
      $form_state['conf'][$key] = $form_state['values'][$key];
    }
  }
}


/**
 * Returns the administrative title for a type.
 */
function views_content_views_panes_content_type_admin_title($subtype, $conf, $contexts) {
  list($name, $display) = explode('-', $subtype);
  $view = views_get_view($name);
  if (empty($view)) {
    return t('Deleted/missing view @view', array('@view' => $name));
  }
  if (empty($view->display[$display])) {
    return t('Deleted/missing view @view', array('@view' => $name));
  }

  $view->set_display($display);
  $title = $view->display_handler->get_option('pane_title');
  return check_plain($title ? $title : $view->name);
}

/**
 * Returns the administrative title for a type.
 */
function views_content_views_panes_content_type_admin_info($subtype, $conf, $contexts) {
}
