<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class.
 *
 * This is still a hack. The idea was to use the same form to perform some
 * actions defined by the extension. Not sure whether that's a good idea.
 *
 * Anyway, if we continue like this, the logic that's now in buildQuickForm
 * should be moved to a dedicated method to build the form for 'new KAVO-id',
 * and buildQuickForm should decide what to do based on the action parameter in
 * the HTTP request. The template to be used should depend on the action as well.
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Kavo_Form_Controller extends CRM_Core_Form {
  public function buildQuickForm() {
    $contactId = CRM_Utils_Request::retrieve('cid', 'Integer');
    $action = CRM_Utils_Request::retrieve('action', 'String');
    if ($action != 'new_id') {
      throw new Exception("Unexpected action: ${action}.");
    }

    // FIXME: This should not be done in buildQuickForm, because buildQuickForm is
    // also called after submitting. (It is not that much of a problem, because
    // calling createaccount twice does not cause any troubles. It wil probably
    // just throw an exception, the exception will be caught, and no output will
    // be shown.)
    try {
      $result = civicrm_api3('Kavo', 'createaccount', ['contact_id' => $contactId]);
      $contact = CRM_Utils_Array::first($result['values']);
      $this->assign('kavoId', $contact[KAVO_FIELD_KAVO_ID]);
      $this->assign('codes', []);
    }
    catch (CiviCRM_API3_Exception $ex) {
      $extraParams = $ex->getExtraParams();
      $codes = $this->getIndividualErrorCodes($extraParams['error_code']);
      $this->assign('codes', $codes);
      $this->assign('missing', $extraParams['missing']);
    }

    $this->addButtons(array(
      array(
        'type' => 'done',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Extract the bits from an error code in an exception of Kavo.createaccount.
   *
   * The error code returned in exceptions of Kavo.createaccount is a bitwise or of individual error codes.
   * Because smarty does not seem to support bitwise and (??), I wrote this function to extract the individual
   * bits.
   *
   * @param int $code
   * @return array
   */
  private function getIndividualErrorCodes($code) {
    $result = [];
    $current = 1;
    while ($code) {
      if ($code & 1) {
        $result[] = $current;
      }
      $code >>= 1;
      $current <<= 1;
    }
    return $result;
  }

  public function postProcess() {
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
}
