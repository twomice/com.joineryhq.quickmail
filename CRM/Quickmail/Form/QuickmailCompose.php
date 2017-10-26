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
      'from_address', // field name
      E::ts('From Address'), // field label
      NULL, // attributes
      TRUE // is required
    );
    $this->addRule('from_address', E::ts('Email is not valid.'), 'email');

    $this->addCheckBox(
      'recipient_group_ids', // field name
      E::ts('Recipient Groups'), // field label
      $recipientGroupsOptions,
      NULL,
      NULL,
      TRUE
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
    parent::postProcess();
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
    $ret['from_address'] = $result['email'];

    return $ret;
  }

}
