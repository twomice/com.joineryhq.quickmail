<?php
use CRM_Quickmail_ExtensionUtil as E;

$manageUrl = CRM_Utils_System::url('civicrm/admin/component', 'reset=1');
$manageLink = "See also: <a href=\"$manageUrl\">" . E::ts('Headers, Footers, and Automated Messages') . '</a>';

return array(
  'quickmail_allowed_group_ids' => array(
    'group_name' => 'QuickMail Settings',
    'group' => 'quickmail',
    'name' => 'quickmail_allowed_group_ids',
    'type' => 'Int',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('Selected groups will be offered as QuickMail recipients. Only groups of type "Mailing List" are shown here.'),
    'title' => E::ts('Allowed groups'),
    'help_text' => '',
    'html_type' => 'CheckBox',
    'quick_form_type' => 'Element',
    'X_options_callback' => 'CRM_Quickmail_Settings::getGroupOptions',
  ),
  'quickmail_header_id' => array(
    'group_name' => 'QuickMail Settings',
    'group' => 'quickmail',
    'name' => 'quickmail_header_id',
    'type' => 'Int',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('The selected header will be automatically placed in all QuickMail mailings.') . " $manageLink",
    'title' => E::ts('Header'),
    'help_text' => '',
    'html_type' => 'Select',
    'quick_form_type' => 'Element',
    'X_options_callback' => 'CRM_Quickmail_Settings::getHeaderOptions',
  ),
  'quickmail_footer_id' => array(
    'group_name' => 'QuickMail Settings',
    'group' => 'quickmail',
    'name' => 'quickmail_footer_id',
    'type' => 'Int',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('The selected footer will be automatically placed in all QuickMail mailings.') . " $manageLink",
    'title' => E::ts('Footer'),
    'help_text' => '',
    'html_type' => 'Select',
    'quick_form_type' => 'Element',
    'X_options_callback' => 'CRM_Quickmail_Settings::getFooterOptions',
  ),
);
