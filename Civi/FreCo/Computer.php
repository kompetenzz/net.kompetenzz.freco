<?php
namespace Civi\FreCo;
/**
 * @package Civi
 */
class Computer extends FreCoBase {

  private static function _getResultsItem(&$results, $customGroup, $customField, $activity) {
    $fieldName = self::_getFieldName($customGroup, $customField);
    $value = $activity[$fieldName];
    $id = self::_getResultsItemId($customField['id'], $value);
    $option = self::_getOption($customField, $value);
    $optionName = $option != null ? $option["name"] : $value;
    $optionTitle = $option != null ? $option["label"] : $value;
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

  private static function _getResultsItemId($fieldId, $value) {
    if (empty($value))
      $value = "null";
    return $fieldId . "[" . $value . "]"; 
  }

  private static function _compute($customGroup, $customFields, $activities) {
    $results = [];
    foreach ($activities as $activity) {
      foreach($customFields as $customField) {
        self::_getResultsItem($results,$customGroup, $customField, $activity);
      }
    }
    ksort($results);
    return array_values($results);
  }

  public static function run($activityType, $activityStati, $customGroupId, $customFieldIds) {
    
    $customGroup = self::_getCustomGroup($customGroupId);
    $customFields = self::_getCustomFields($customGroup, $customFieldIds);
    $activities = self::_getActivities($activityType, $activityStati, $customGroup, $customFields);
    return self::_compute($customGroup, $customFields, $activities);
  }
}
