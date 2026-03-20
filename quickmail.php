<?php

require_once 'quickmail.civix.php';
use CRM_Quickmail_ExtensionUtil as E;

function quickmail_civicrm_permission(&$permissions) {
  // name of extension or module
  $prefix = E::ts('CiviCRM QuickMail') . ': ';
  $permissions['access quickmail'] = array(
    // label
    $prefix . E::ts('access QuickMail'),
    // description
    E::ts('Use CiviCRM QuickMail to send email'),
  );
  $permissions['administer quickmail'] = array(
    // if no description, just give an array with the label
    $prefix . E::ts('Administer QuickMail'),
    '',
  );
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function quickmail_civicrm_config(&$config) {
  _quickmail_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function quickmail_civicrm_install() {
  _quickmail_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function quickmail_civicrm_enable() {
  _quickmail_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 */
// function quickmail_civicrm_preProcess($formName, &$form) {

// } // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function quickmail_civicrm_navigationMenu(&$menu) {
  _quickmail_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('QuickMail'),
    'name' => 'quickmail',
    'url' => 'civicrm/quickmail/compose?reset=1',
    'permission' => 'access quickmail',
    'operator' => 'AND',
    'separator' => 1,
  ));
  _quickmail_civix_insert_navigation_menu($menu, 'Administer/CiviMail', array(
    'label' => E::ts('QuickMail Settings'),
    'name' => 'quickmail',
    'url' => 'civicrm/admin/quickmail/settings?reset=1',
    'permission' => 'administer CiviCRM',
    'operator' => 'AND',
  ));
  _quickmail_civix_navigationMenu($menu);
}

function quickmail_civicrm_coreResourceList(&$list, $region) {
  // Make wysiwyg JavaScript available on Joomla front end.
  $config = CRM_Core_Config::singleton();
  if ($config->userFrameworkFrontend) {
    foreach ($list as &$item) {
      if (
        is_array($item)
        && array_key_exists('config', $item)
        && array_key_exists('wysisygScriptLocation', $item['config'])
        && strpos($item['config']['wysisygScriptLocation'], '/components/') === 0
      ) {
        $item['config']['wysisygScriptLocation'] = '/administrator' . $item['config']['wysisygScriptLocation'];
      }
    }
  }
}
