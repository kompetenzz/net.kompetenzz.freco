<?php
namespace Civi\FreCo;
/**
 * @package Civi
 */
class Computer extends FreCoBase {

  private static function _getResultsItem($customGroup, $customField, $activity) {
    return [
      'id' => self::_getResultsItemId($customGroup, $customField, $activity),
      'name' => self::_getResultsItemName($customGroup, $customField, $activity),
      'title' => self::_getResultsItemTitle($customGroup, $customField, $activity),
      'value' => 0
    ];
  }

  private static function _getResultsItemId($customGroup, $customField, $activity) {
    $name = self::_getFieldName($customGroup, $customField);
    $value = $activity[$name];
    if (empty($value))
      $value = "null";
    return $customField['id'] . "[" . $value . "]"; 
  }

  private static function _getResultsItemName($customGroup, $customField, $activity) {
    $name = self::_getFieldName($customGroup, $customField); // groupname.fieldname
    $value = $activity[$name]; // 0,1,NULL
    if (empty($value)) 
      return $customField['label'] . " [null]";

    $option = self::_getOption($customField, $value);
    $oName = $option != null ? $option["name"] : $value;
    return $customField['name'] . "[" . $oName . "]"; 
  }

  private static function _getResultsItemTitle($customGroup, $customField, $activity) {
    $name = self::_getFieldName($customGroup, $customField); // groupname.fieldname
    $value = $activity[$name]; // 0,1,NULL
    if (empty($value)) 
      return $customField['label'] . ": keine Angabe"; 

    $option = self::_getOption($customField, $value);
    $oTitle = $option != null ? $option["label"] : $value;
    return $customField['label'] . ": " . $oTitle; 
  }

  private static function _compute($customGroup, $customFields, $activities) {
    $results = [];
    foreach ($activities as $activity) {
      foreach($customFields as $customField) {
        $id = self::_getResultsItemId($customGroup, $customField, $activity);  
        if (!array_key_exists($id, $results)) 
          $results[$id] = self::_getResultsItem($customGroup, $customField, $activity);
        $results[$id]['value']++;
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
