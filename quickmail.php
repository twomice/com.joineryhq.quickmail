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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function quickmail_civicrm_xmlMenu(&$files) {
  _quickmail_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function quickmail_civicrm_postInstall() {
  _quickmail_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function quickmail_civicrm_uninstall() {
  _quickmail_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function quickmail_civicrm_enable() {
  _quickmail_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function quickmail_civicrm_disable() {
  _quickmail_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function quickmail_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _quickmail_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function quickmail_civicrm_managed(&$entities) {
  _quickmail_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function quickmail_civicrm_caseTypes(&$caseTypes) {
  _quickmail_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function quickmail_civicrm_angularModules(&$angularModules) {
  _quickmail_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function quickmail_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _quickmail_civix_civicrm_alterSettingsFolders($metaDataFolders);
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
