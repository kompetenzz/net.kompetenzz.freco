<?php
namespace Civi\FreCo;

use __PHP_Incomplete_Class;
use CRM_Freco_ExtensionUtil as E;

/**
 * @package Civi
 */
class Computer extends FreCoBase {

  private static function _addResultsItem(&$results, $customGroup, $customField, $rawValue) {
    $fieldName = self::_getFieldName($customGroup, $customField);
    // Handle multiple values
    $values = [];
    if (empty($rawValue)) 
      $values = [$customField['default_value'] ?? "unset"];
    else if (is_array($rawValue)) 
      $values = $rawValue;
    else
      $values =[$rawValue];

    foreach($values as $value) {
      $id = self::_getResultsItemId($customField['id'], $value);
      $option = self::_getOption($customField, $value);
      $optionName = $option != null ? $option["name"] : $value;
      $optionTitle = $option != null ? $option["label"] : E::ts($value);
      if (!array_key_exists($id, $results))
        $results[$id] = [
          'id' => $id,
          'name' => $optionName,
          'title' => $optionTitle,
          'field_id' => $customField['id'],
          'field_name' => $fieldName,
          'field_title' => $customField['label'],
          'value' => 0
        ];
      $results[$id]['value']++;
    }
  }

  private static function _addActivityResultsItem(&$results, $customGroup, $customField, $activity) {
    $fieldName = self::_getFieldName($customGroup, $customField);
    $rawValue = $activity[$fieldName];    
    self::_addResultsItem($results, $customGroup, $customField, $rawValue);
  }

  private static function _addContactResultsItem(&$results, $customGroup, $customField, $contact) {
    $fieldName = self::_getFieldName($customGroup, $customField);
    $rawValue = $contact[$fieldName];    
    self::_addResultsItem($results, $customGroup, $customField, $rawValue);
  }

  private static function _getResultsItemId($fieldId, $value) {
    if (empty($value))
      $value = "null";
    return $fieldId . "[" . $value . "]"; 
  }

  private static function _compute($customGroup, $customFields, $contacts, $activities) {
    $results = [];
    foreach ($activities as $activity) {
      foreach($customFields as $customField) {
        self::_addActivityResultsItem($results,$customGroup, $customField, $activity);
      }
    }
    foreach ($contacts as $contact) {
      foreach($customFields as $customField) {
        self::_addContactResultsItem($results,$customGroup, $customField, $contact);
      }
    }
    ksort($results);
    return array_values($results);
  }

  public static function run($groupId, $activityType, $activityStati, $customGroupId, $customFieldIds) {
    
    $customGroup = self::_getCustomGroup($customGroupId);
    $customFields = self::_getCustomFields($customGroup, $customFieldIds);
    if ($activityType > 0)
      $activities = self::_getActivities($activityType, $activityStati, $customGroup, $customFields, $groupId);
    else 
      $contacts =  self::_getContacts($customGroup, $customFields, $groupId);
    return self::_compute($customGroup, $customFields, $contacts, $activities);
  }
}
