<?php

require_once 'relationshipsummary.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function relationshipsummary_civicrm_config(&$config) {
  _relationshipsummary_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function relationshipsummary_civicrm_xmlMenu(&$files) {
  _relationshipsummary_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function relationshipsummary_civicrm_install() {
  _relationshipsummary_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function relationshipsummary_civicrm_uninstall() {
  _relationshipsummary_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function relationshipsummary_civicrm_enable() {
  _relationshipsummary_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function relationshipsummary_civicrm_disable() {
  _relationshipsummary_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function relationshipsummary_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _relationshipsummary_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function relationshipsummary_civicrm_managed(&$entities) {
  _relationshipsummary_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function relationshipsummary_civicrm_caseTypes(&$caseTypes) {
  _relationshipsummary_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function relationshipsummary_civicrm_angularModules(&$angularModules) {
_relationshipsummary_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function relationshipsummary_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _relationshipsummary_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function relationshipsummary_civicrm_summary($contactID, &$content, &$contentPlacement) {

  $relationship_types = CRM_Core_BAO_Setting::getItem(
    'relationshipsummary', 'include_relationship_types', NULL, array()
  );
  if (empty($relationship_types)) {
    return; 
  }

  $sql = "
SELECT c.id, c.display_name, CASE WHEN r.contact_id_a = $contactID THEN rt.label_a_b ELSE rt.label_b_a END as relationship_type, SUM(IFNULL(contrib.total_amount,0)) as total_amount, MAX(contrib.receive_date) as latest
  FROM civicrm_relationship r
  INNER JOIN civicrm_contact c ON (r.contact_id_a = $contactID AND r.contact_id_b = c.id)
    OR (r.contact_id_a = c.id AND r.contact_id_b = $contactID)
  INNER JOIN civicrm_relationship_type rt ON rt.id = r.relationship_type_id
  INNER JOIN civicrm_contribution contrib ON contrib.contact_id = c.id
    AND contrib.contribution_status_id = 1
WHERE r.relationship_type_id IN ($relationship_types) and r.is_active = 1
GROUP BY c.id
ORDER BY c.sort_name";
  $dao = CRM_Core_DAO::executeQuery($sql);

  $relationship_table = '';
  $config = CRM_Core_Config::singleton();
  while ($dao->fetch()) {
    $url = CRM_Utils_System::url( 'civicrm/contact/view',
      "reset=1&cid={$dao->id}" );
    $amount = CRM_Utils_Money::format($dao->total_amount);
    $latest = CRM_Utils_Date::customFormat($dao->latest, $config->dateformatFull);
    $relationship_table .= "<tr><td width=20%>{$dao->relationship_type}</td><td><a href='$url'>{$dao->display_name}</a></td><td width=20%>$latest</td><td width=20%>$amount</td></tr>";
  }

  if ($relationship_table != '') {
    $contentPlacement = CRM_Utils_Hook::SUMMARY_ABOVE;
    $content .= "<table><thead><th colspan=2>Related financial history</th><th>Most Recent</th><th>Lifetime</th></thead>$relationship_table</table>";
  }
}
