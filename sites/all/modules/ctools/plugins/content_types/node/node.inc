<?php
// $Id: node.inc,v 1.3 2009/05/09 07:44:55 merlinofchaos Exp $

/**
 * @file
 * Plugin to handle the 'node' content type which allows individual nodes
 * to be embedded into a panel.
 */

/**
 * Implementation of hook_ctools_content_types()
 */
function ctools_node_ctools_content_types() {
  return array(
    'title' => t('Node'),
    'single' => TRUE,
    'defaults' => array(
      'nid' => '',
      'teaser' => TRUE,
      'links' => TRUE,
      'leave_node_title' => FALSE,
      'identifier' => '',
    ),
    'title' => t('Add a node'),
    'icon' => 'icon_node.png',
    'description' => t('Add a node from your site as content.'),
    'category' => t('Custom'),
    'top level' => TRUE,
    'js' => array('misc/autocomplete.js'),
  );
}

/**
 * Output function for the 'node' content type.
 *
 * Outputs a node based on the module and delta supplied in the configuration.
 */
function ctools_node_content_type_render($subtype, $conf, $panel_args) {
  $nid = $conf['nid'];
  $block = new stdClass();

  foreach (explode('/', $_GET['q']) as $id => $arg) {
    $nid = str_replace("%$id", $arg, $nid);
  }

  foreach ($panel_args as $id => $arg) {
    $nid = str_replace("@$id", $arg, $nid);
  }

  // Support node translation
  if (module_exists('translation')) {
    if ($translations = module_invoke('translation', 'node_get_translations', $nid)) {
  	  if ($translations[$GLOBALS['language']->language])  {
        $nid = $translations[$GLOBALS['language']->language]->nid;
      }
    }
  }

  if (!is_numeric($nid)) {
    return;
  }

  $node = node_load($nid);
  if (!node_access('view', $node)) {
    return;
  }

  if (node_access('update', $node)) {
    $block->admin_links['update'] = array(
      'title' => t('Edit node'),
      'alt' => t("Edit this node"),
      'href' => "node/$node->nid/edit",
      'query' => drupal_get_destination(),
    );
  }

  $block->module = 'node';
  $block->delta = $node->nid;

  $block->title = $node->title;

  if (empty($conf['leave_node_title'])) {
    $node->title = NULL;
  }
  if (!empty($conf['identifier'])) {
    $node->panel_identifier = $conf['identifier'];
  }

  $block->content = node_view($node, $conf['teaser'], FALSE, $conf['links']);
  return $block;
}

/**
 * The form to add or edit a node as content.
 */
function ctools_node_content_type_edit_form(&$form, &$form_state) {
  $conf = $form_state['conf'];

  if ($form_state['op'] == 'add') {
    $form['nid'] = array(
      '#prefix' => '<div class="no-float">',
      '#title' => t('Enter the title or NID of a post'),
      '#description' => t('To use a NID from the URL, you may use %0, %1, ..., %N to get URL arguments. Or use @0, @1, @2, ..., @N to use arguments passed into the panel.'),
      '#type' => 'textfield',
      '#maxlength' => 512,
      '#autocomplete_path' => 'ctools/autocomplete/node',
      '#weight' => -10,
      '#suffix' => '</div>',
    );
  }
  else {
    $form['nid'] = array(
      '#type' => 'value',
      '#value' => $conf['nid'],
    );
  }

  $form['teaser'] = array(
    '#title' => t('Teaser'),
    '#type' => 'checkbox',
    '#default_value' => $conf['teaser'],
    '#description' => t('Check here to show only the node teaser'),
  );

  $form['links'] = array(
    '#type' => 'checkbox',
    '#default_value' => $conf['links'],
    '#title' => t('Display links'),
    '#description' => t('Check here to display the links with the post.'),
  );

  $form['leave_node_title'] = array(
    '#type' => 'checkbox',
    '#default_value' => !empty($conf['leave_node_title']),
    '#title' => t('Leave node title'),
    '#description' => t('Advanced: if checked, do not touch the node title; this can cause the node title to appear twice unless your theme is aware of this.'),
  );

  $form['identifier'] = array(
    '#type' => 'textfield',
    '#default_value' => !empty($conf['identifier']) ? $conf['identifier'] : '',
    '#title' => t('Identifier'),
    '#description' => t('Whatever is placed here will appear in $node->panel_identifier in the node template to make it easier to theme a node or part of a node as necessary. This identifier will automatically be added as a node template suggestion: node-panel-IDENTIFIER.tpl.php'),
  );

}

/**
 * Validate the node selection.
 */
function  ctools_node_content_type_edit_form_validate(&$form, &$form_state) {
  if ($form_state['op'] != 'add') {
    return;
  }

  $nid = $form_state['values']['nid'];
  $preg_matches = array();
  $match = preg_match('/\[nid: (\d+)\]/', $nid, $preg_matches);
  if (!$match) {
    $match = preg_match('/^nid: (\d+)/', $nid, $preg_matches);
  }

  if ($match) {
    $nid = $preg_matches[1];
  }
  if (is_numeric($nid)) {
    $node = db_fetch_object(db_query(db_rewrite_sql("SELECT n.nid FROM {node} n WHERE n.nid = %d"), $nid));
  }
  else {
    $node = db_fetch_object(db_query(db_rewrite_sql("SELECT n.nid FROM {node} n WHERE LOWER(n.title) = LOWER('%s')"), $nid));
  }
  if ($node) {
    $form_state['values']['nid'] = $node->nid;
  }

  if (!($node || preg_match('/^[@%]\d+$/', $nid))) {
    form_error($form['nid'], t('Invalid node'));
  }
}

/**
 * Validate the node selection.
 */
function ctools_node_content_type_edit_form_submit(&$form, &$form_state) {
  foreach (array('nid', 'teaser', 'links', 'leave_node_title', 'identifier') as $key) {
    $form_state['conf'][$key] = $form_state['values'][$key];
  }
}

/**
 * Returns the administrative title for a node.
 */
function ctools_node_content_type_admin_title($subtype, $conf) {
  if (!is_numeric($conf['nid'])) {
    return t('Node loaded from @var', array('@var' => $conf['nid']));
  }

  $node = node_load($conf['nid']);
  if ($node) {
    return check_plain($node->title);
  }
  else {
    return t('Deleted/missing node @nid', array('@nid' => $conf['nid']));
  }
}

/**
 * Display the administrative information for a node pane.
 */
function ctools_node_content_type_admin_info($subtype, $conf) {
  // Just render the node.
  return ctools_node_content_type_render($subtype, $conf, array());
}
