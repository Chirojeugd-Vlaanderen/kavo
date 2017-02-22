<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class.
 *
 * TODO: needs refactoring.
 *
 * This is still a hack. The idea was to use the same form to perform some
 * actions defined by the extension. But that is probably not a good idea.
 * Maybe I should create a base form, and inherit.
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Kavo_Form_Controller extends CRM_Core_Form {
  /**
   * @var int
   */
  protected $contactId;

  /**
   * @var int
   */
  protected $id;

  /**
   * @var string
   */
  protected $action;

  /**
   * Executes the requested action.
   *
   * The idea is to use this form for multiple smaller actions for the
   * KAVO extension. Not sure whether this is a good idea.
   *
   * I have the impression that preProcess is called before buildQuickForm.
   *
   * @throws Exception
   */
  public function preProcess() {
    parent::preProcess();

    $this->contactId = CRM_Utils_Request::retrieve('cid', 'Integer');
    $this->id = CRM_Utils_Request::retrieve('id', 'Integer');
    $this->action = CRM_Utils_Request::retrieve('action', 'String');

    if (!empty($this->_submitValues)) {
      // preProcess seems to be called when the form is submitted as well.
      // Our form should not do anything special after submission (yet), so
      // we just return, to avoid e.g. registering something at KAVO twice.
      return;
    }
    $action = CRM_Utils_Request::retrieve('action', 'String');
    if ($this->action == 'new_id') {
      $this->newId($this->contactId);
      $this->assign('entityName', 'contact');
    }
    else if ($this->action == 2 && !empty($this->id)) {
      // CiviCRM seems to automatically assign 2 to 'action' in links
      // generated with hook_civicrm_tabset.
      // We assume that the user wants to send a course to KAVO. At the moment
      // there is no way we can be sure about that.
      $this->newCourse($this->id);
      $this->assign('entityName', 'event');
    }
    else {
      throw new Exception("Unexpected action: ${action}.");
    }
  }


  public function buildQuickForm() {
    $this->addButtons(array(
      array(
        'type' => 'done',
        'name' => ts('OK'),
        'isDefault' => TRUE,
      ),
    ));

    $this->addElement('hidden', 'contact_id', $this->contactId);
    $this->addElement('hidden', 'id', $this->id);

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Create a KAVO-ID for the contact with given $contactId, and prepare the view model.
   *
   * @param $contactId
   */
  private function newId($contactId) {
    try {
      $result = civicrm_api3('Kavo', 'createaccount', ['contact_id' => $contactId]);
      $contact = CRM_Utils_Array::first($result['values']);
      $this->assign('kavoId', $contact[CRM_Kavo_Field::KAVO_ID()]);
      $this->assign('codes', []);
    }
    catch (CiviCRM_API3_Exception $ex) {
      $extraParams = $ex->getExtraParams();
      $codes = CRM_Kavo_Helper::getBits($extraParams['error_code']);
      $this->assign('codes', $codes);
      if (!empty($extraParams['missing'])) {
        $this->assign('missing', $extraParams['missing']);
      }
      if (in_array(CRM_Kavo_Error::UNKNOWN, $codes))
      {
        $this->assign('error_message', $extraParams['error_message']);
      }
    }
  }

  /**
   * Send the event with given $eventId to KAVO as a new course.
   *
   * @param $eventId
   */
  private function newCourse($eventId) {
    try {
      $result = civicrm_api3('Kavo', 'createcourse', ['event_id' => $eventId]);
      $event = CRM_Utils_Array::first($result['values']);
      $this->assign('kavoId', $event[CRM_Kavo_Field::COURSE_ID()]);
      $this->assign('codes', []);
    }
    catch (CiviCRM_API3_Exception $ex) {
      $extraParams = $ex->getExtraParams();
      $codes = CRM_Kavo_Helper::getBits($extraParams['error_code']);
      $this->assign('codes', $codes);
      $this->assign('missing', $extraParams['missing']);
    }
  }

  public function postProcess() {
    parent::postProcess();

    if (!empty($this->contactId)) {
      CRM_Core_Session::singleton()->pushUserContext(
        CRM_Utils_System::url('civicrm/contact/view', "cid={$this->contactId}")
      );
    }
    if (!empty($this->id)) {
      CRM_Core_Session::singleton()->pushUserContext(
        CRM_Utils_System::url('civicrm/event/info', "id={$this->id}")
      );
    }
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
