<?php

use CRM_Quickmail_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Quickmail_Form_QuickmailCompose extends CRM_Core_Form {
  public function buildQuickForm() {
    $recipientGroupsOptions = array_flip(CRM_Quickmail_Settings::getGroupOptions(CRM_Quickmail_Settings::FILTER_ALLOWED));
    if (empty($recipientGroupsOptions)) {
      // No groups are allowed, so can't send mail. Admin needs to configure
      // before anyone can use QuickMail.
      CRM_Core_Error::statusBounce(E::ts('No recipient groups are configured. Please contact your site administrator before using this feature.'), NULL, 'QuickMail');
    }
    // add form elements
    $this->add(
      'text', // field type
      'from_name', // field name
      E::ts('From Name'), // field label
      NULL, // attributes
      TRUE // is required
    );
    $this->add(
      'text', // field type
      'from_email', // field name
      E::ts('From Address'), // field label
      NULL, // attributes
      TRUE // is required
    );
    $this->addRule('from_email', E::ts('Email is not valid.'), 'email');

    $this->addCheckBox(
      'recipient_group_ids', // field name
      E::ts('Recipient Groups'), // field label
      $recipientGroupsOptions,
      NULL,
      NULL,
      TRUE
    );

    $this->add(
      'text', // field type
      'subject', // field name
      E::ts('Subject'), // field label
      NULL, // attributes
      TRUE // is required
    );

    $this->add('wysiwyg', 'email_body', ts('Message Body'));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    $settings = CRM_Quickmail_Settings::getSettingValues(array('quickmail_header_id', 'quickmail_footer_id'));
    $headerId = self::getComponentId('header', CRM_Utils_Array::value('quickmail_header_id', $settings, 0));
    $footerId = self::getComponentId('footer', CRM_Utils_Array::value('quickmail_footer_id', $settings, 0));

    $userCid = CRM_Core_Session::getLoggedInContactID();
    $currentDbDateTime = CRM_Utils_Date::currentDBDate();
    $params = array(
      'from_name' => $values['from_name'],
      'from_email' => $values['from_email'],
      'replyto_email' => CRM_Utils_Mail::formatRFC822Email($values['from_name'], $values['from_email']),
      'name' => 'QuickMail:' . $currentDbDateTime,
      'header_id' => $headerId,
      'footer_id' => $footerId,
      'subject' => $values['subject'],
      'body_html' => $values['email_body'],
      'scheduled_id' => $userCid,
      'approver_id' => $userCid,
      'scheduled_date' => $currentDbDateTime,
      'approval_date' => $currentDbDateTime,
    );

    try {
      $result = civicrm_api3('Mailing', 'create', $params);
      $mailingId = $result['id'];

      $recipentGroupIds = array_flip($values['recipient_group_ids']);
      $recipientContacts = $this->getRecipientContacts($recipentGroupIds);
      foreach ($recipientContacts as $recipientContact) {
        $bao = new CRM_Mailing_BAO_Recipients();
        $bao->mailing_id = $mailingId;
        $bao->contact_id = $recipientContact['id'];
        $bao->email_id = $recipientContact['email_id'];
        $bao->save();
      }
      CRM_Core_Session::setStatus('done', 'QuickMail', 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/quickmail/compose', 'reset=1'));
    }
    catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage(), 'QuickMail', 'error');
    }
  }

  private function getRecipientContacts($recipentGroupIds = array()) {
    if (empty($recipentGroupIds)) {
      // If no recipient groups, then no recipient contacts, so just return
      // an empty array.
      return array();
    }
    $recipientContacts = civicrm_api3('contact', 'get', array(
      'group' => $recipentGroupIds,
      'is_deceased' => 0,
      'is_deleted' => 0,
      'do_not_email' => 0,
      'is_opt_out' => 0,
      'email' => array('IS NOT NULL' => 1),
      'options' => array(
        'limit' => 0,
      ),
      'return' => array(
        'id',
        'email_id',
      ),
    ));
    return $recipientContacts['values'];
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

  public function setDefaultValues() {
    $ret = array();

    $result = civicrm_api3('Contact', 'getSingle', array(
      'id' => CRM_Core_Session::getLoggedInContactID(),
      'return' => array(
        'display_name',
        'email',
      ),
    ));

    $ret['from_name'] = $result['display_name'];
    $ret['from_email'] = $result['email'];

    return $ret;
  }

  private function getComponentId($componentType, $settingValue = 0) {
    $vaildTypes = array('header', 'footer');
    if (!in_array(strtolower($componentType), $vaildTypes)) {
      // Invalid type; fail loudly.
      CRM_Core_Error::fatal("Invalid mailing component type '$componentType'.");
    }
    $params = array(
      'component_type' => $componentType,
      'is_active' => 1,
    );
    if ($settingValue == 0) {
      $params['is_default'] = 1;
    }
    else {
      $params['id'] = $settingValue;
    }

    try {
      $component = civicrm_api3('MailingComponent', 'getSingle', $params);
    }
    catch (CiviCRM_API3_Exception $e) {
      // Couldn't find one. Is there a default component of this type?
      return NULL;
    }
    return $component['id'];
  }

}
