<?php

/**
 * Settings-related utility methods.
 *
 */
class CRM_Quickmail_Settings {

  const FILTER_ALLOWED = 1;
  const FILTER_DISALLOWED = 2;
  const SETTINGS_FILTER = array('group' => 'quickmail');

  private static function getComponentOptions($type) {
    $vaildTypes = array('header', 'footer');
    if (!in_array(strtolower($type), $vaildTypes)) {
      // Invalid type; just return empty array.
      return array();
    }

    $result = civicrm_api3('MailingComponent', 'get', array(
      'component_type' => $type,
      'return' => array("name"),
      'options' => array(
        'sort' => 'name',
      ),
    ));
    $ret = array(
      '' => '- ' . ts('None') . ' -',
      '0' => '- ' . ts('Use default') . ' -',
    );
    $ret += array_map(function($value) {
      return $value['name'];
    }, $result['values']);

    return $ret;
  }

  public static function getHeaderOptions() {
    return self::getComponentOptions('header');
  }

  public static function getFooterOptions() {
    return self::getComponentOptions('footer');
  }

  public static function getGroupOptions($filter = NULL) {

    $params = array(
    // 'group_type' => "Mailing List", // FIXME: CiviCRM 4.7.22 doesn't
                                        // seem to respect this param. In later
                                        // version we can try and use it. For now,
                                        // we'll have to filter manually.
      'is_hidden' => 0,
      'is_active' => 1,
      'return' => array('id', 'title', 'group_type_id', 'group_type'),
      'options' => array(
        'limit' => 0,
      ),
    );

    if (isset($filter)) {
      $settingsValues = self::getSettingValues('quickmail_allowed_group_ids');
      $ids = CRM_Utils_Array::value('quickmail_allowed_group_ids', $settingsValues, array());
      switch ($filter) {
        case self::FILTER_ALLOWED:
          if (empty($ids)) {
            // We're filtering for allowed groups, but there are none. Querying
            // the API for "'id' IN array()" will trigger a fatal error. So just
            // return an empty array.
            return array();
          }
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
        return $value['title'];
      }
    }, $getGroupResult['values']);
    // Remove NULL values.
    $values = array_filter($values);
    return $values;
  }

  /**
   * Get saved values for the given settings.
   *
   * @param array|string $settings Name or names of desired setting values.
   * @param bool $isForm If TRUE, values will be formatted in a way that
   *    makes sense for QuickForm (e.g., for checkbox options, selected values
   *    are in the array keys); otherwise values are returned in a way that
   *    makes sense for everybody else in the whole world.
   *
   * @return Array of values, keyed to setting names.
   */
  public static function getSettingValues($settings, $isForm = FALSE) {
    $settings = (array) $settings;
    $result = civicrm_api3('setting', 'get', array('return' => $settings));
    $domainID = CRM_Core_Config::domainID();
    $ret = CRM_Utils_Array::value($domainID, $result['values']);

    if (!$isForm) {
      $settingsMetadata = self::getSettingsMetadata();
      array_walk($ret, function(&$value, $key) use ($settingsMetadata) {
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
