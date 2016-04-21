<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Relationshipsummary_Form_Settings extends CRM_Core_Form {

  function buildQuickForm() {

    // get a list of available relationship types
    $rel_types = CRM_Core_PseudoConstant::relationshipType();
    $options = array();
    foreach ($rel_types as $rt) {
      $options[$rt['id']] = $rt['label_a_b'];
    }

    $this->add(
      'advmultiselect',
      'include_relationship_types',
      'Relationship Types',
      $options, TRUE,
      array(
        'size' => 5,
        'style' => 'width:240px',
        'class' => 'advmultiselect',
      )
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();

  }

  public function setDefaultValues() {

    $defaults = array();

    $defaults['include_relationship_types'] = CRM_Core_BAO_Setting::getItem(
      'relationshipsummary', 'include_relationship_types', NULL, array()
    );

    return $defaults;

  }

  function postProcess() {

    $values = $this->exportValues();
    $rel_types = implode(',', $values['include_relationship_types']);

    CRM_Core_BAO_Setting::setItem(
      $rel_types,
      'relationshipsummary',
      'include_relationship_types'
    );

    parent::postProcess();

  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
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
