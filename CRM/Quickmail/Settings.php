<?php

/**
 * Settings-related utility methods.
 *
 */
class CRM_Quickmail_Settings {
  
  const FILTER_ALLOWED = 1;
  const FILTER_DISALLOWED = 2;
  const SETTINGS_FILTER = array('group' => 'quickmail');
  
  //put your code here
  public static function getGroupOptions($filter = NULL) {
    $params = array(
//      'group_type' => "Mailing List", // FIXME: CiviCRM 4.7.22 doesn't 
                                        // seem to respect this param. In later
                                        // version we can try and use it. For now,
                                        // we'll have to filter manually.
      'is_hidden' => 0,
      'is_active' => 1,
      'return' => array("id", "name", 'group_type_id', 'group_type'),
      'options' => array(
        'limit' => 0,
      ),
    );
    
    if (isset($filter)) {
      $settingsValues = self::getSettingValues('quickmail_allowed_group_ids');
      $ids = $settingsValues['quickmail_allowed_group_ids'];
      switch ($filter) {
        case self::FILTER_ALLOWED:
          $params['id'] = array('IN' => $ids);
          break;

        case self::FILTER_DISALLOWED:
          $params['id'] = array('NOT IN' => $ids);
          break;
      }
    }
    $getGroupResult = civicrm_api3('Group', 'get', $params);
    
    // Filter for only groups of type "Mailing List"
    $values = array_map(function($value) {
      if (in_array(2, $value['group_type'])) {
        return $value['name'];
      }
    }, $getGroupResult['values']);
    // Remove NULL values.
    $values = array_filter($values);
    return $values;
  }
  
  /**
   * Get saved values for the given settings.
   * 
   * @param Array|String $settings Name or names of desired setting values.
   * @param Boolean $isForm If TRUE, values will be formatted in a way that 
   *    makes sense for QuickForm (e.g., for checkbox options, selected values
   *    are in the array keys); otherwise values are returned in a way that
   *    makes sense for everybody else in the whole world.
   * 
   * @return Array of values, keyed to setting names.
   */
  public static function getSettingValues($settings, $isForm = FALSE) {
    $settings = (array)$settings;
    $result = civicrm_api3('setting', 'get', array('return' => $settings));
    $domainID = CRM_Core_Config::domainID();
    $ret = CRM_Utils_Array::value($domainID, $result['values']);

    if (!$isForm) {
      $settingsMetadata = self::getSettingsMetadata();
      array_walk($ret, function(&$value, $key) use ($settingsMetadata){     
        if ($settingsMetadata[$key]['html_type'] == 'CheckBox') {
          $value = array_keys($value);
        }
      });
    }
    return $ret;    
  }

  public static function getSettingsMetadata() {
    $settings = civicrm_api3('setting', 'getfields', array('filters' => self::SETTINGS_FILTER));
    return CRM_Utils_Array::value('values', $settings);
  }

  
}
