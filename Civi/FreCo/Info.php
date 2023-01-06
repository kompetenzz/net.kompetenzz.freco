<?php
namespace Civi\FreCo;
/**
 * @package Civi
 */
class Info extends FreCoBase {


  public static function run($activityTypeId, $activityStatusIds, $customGroupId, $customFieldIds) {
    
    $activityType = self::_getActivityType($activityTypeId);
    $activityStati = self::_getActivityStati($activityStatusIds);
    $customGroup = self::_getCustomGroup($customGroupId);
    $customFields = self::_getCustomFields($customGroup, $customFieldIds, false);
    $activities = self::_getActivities($activityTypeId, $activityStatusIds, $customGroup, $customFields);
    return [
      'activity_type' => $activityType['label'],
      'activity_stati' => implode(", ", array_map(function($status) {
        return $status['label'];
      }, $activityStati)),
      'custom_group' => $customGroup['title'],
      'custom_fields' => implode(", ", array_map(function($customField) {
        return $customField['label'];
      }, $customFields)),
      'items' => count($activities)
    ];
  }
}
