<?php
namespace Civi\FreCo;
/**
 * @package Civi
 */
class Info extends FreCoBase {


  public static function run($groupId, $activityTypeId = 0, $activityStatusIds = [], $customGroupId = 0, $customFieldIds = []) {
    
    $result = [];

    if ($groupId > 0) {
      $group = self::_getGroup($groupId);
      $result['group'] = $group['title'];
    } 

    $customGroup = self::_getCustomGroup($customGroupId);
    $customFields = self::_getCustomFields($customGroup, $customFieldIds, false);
    $result['custom_group'] = $customGroup['title'];
    $result['custom_fields'] = implode(", ", array_map(function($customField) {
      return $customField['label'];
    }, $customFields));

    // By activity
    if ($activityTypeId > 0) {
      $activityType = self::_getActivityType($activityTypeId);
      $result['activity_type'] = $activityType['label'];
      if (count($activityStatusIds)) {
        $activityStati = self::_getActivityStati($activityStatusIds);
        $result['activity_stati'] = implode(", ", array_map(function($status) {
          return $status['label'];
        }, $activityStati));
      }
      $activities = self::_getActivities($activityTypeId, $activityStatusIds, $customGroup, $customFields, $groupId);
      $result['items'] = count($activities);
    } else {
      $contacts = self::_getContacts($customGroup, $customFields, $groupId);
      $result['items'] = count($contacts);
    }

    return $result;
  }
}
