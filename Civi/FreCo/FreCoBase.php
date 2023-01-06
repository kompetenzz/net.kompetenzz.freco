<?php
namespace Civi\FreCo;
/**
 * @package Civi
 */
abstract class FreCoBase {
  protected static $optionsCache = [];

  protected static function _cacheOptions($customField) {
    if (!array_key_exists('option_group_id', $customField))
      return;
    if (array_key_exists($customField["option_group_id"], self::$optionsCache))
      return;
    self::$optionsCache[$customField["option_group_id"]] = \Civi\Api4\OptionValue::get()
      ->addWhere('option_group_id', '=', $customField["option_group_id"])
      ->execute(); 
  }

  protected static function _getOption($customField, $value) {
    if (!array_key_exists('option_group_id', $customField))
      return null;
    if (!array_key_exists($customField["option_group_id"], self::$optionsCache))
      return null;
    foreach(self::$optionsCache[$customField["option_group_id"]] as $option) {
      if ($option['value'] == $value)
        return $option;
    }
    return null;
  }

  protected static function _getCustomGroup($customGroupId) {
    $cGrps = \Civi\Api4\CustomGroup::get()
      ->addWhere('id', '=', $customGroupId)
      ->setLimit(1)
      ->execute(); 
    if (!count($cGrps))
      return null;
    return $cGrps[0];
  }

  protected static function _getCustomFields($customGroup, $customFieldIds, $cache = true) {
    $getFields = \Civi\Api4\CustomField::get()
      ->addWhere('custom_group_id', '=', $customGroup['id']);
    if (!empty($customFieldIds)) 
      $getFields = $getFields->addWhere('id', 'IN', self::enforceArray($customFieldIds));
    $customFields = $getFields
      ->execute()
      ->getArrayCopy();
    if ($cache)
      foreach ($customFields as $customField)
        self::_cacheOptions($customField);

    return $customFields;
  }

  protected static function _getFieldName($customGroup, $customField) {
    return $customGroup['name'] . "." . $customField['name'];
  }

  protected static function _getActivityType($activityTypeId) {
    $ov = \Civi\Api4\OptionValue::get()
      ->addWhere('option_group_id', '=', 2)
      ->addWhere('value', '=', $activityTypeId)
      ->setLimit(1)
      ->execute(); 
    return $ov[0];
  }

  protected static function _getActivityStati($activityStatusIds) {
    $ov = \Civi\Api4\OptionValue::get()
      ->addWhere('option_group_id', '=', 25)
      ->addWhere('value', 'IN', self::enforceArray($activityStatusIds))
      ->execute(); 
    return $ov
      ->getArrayCopy();
  }

  protected static function _getActivities($activityType, $activityStati, $customGroup, $customFields) {
    $getActivities = \Civi\Api4\Activity::get()
      ->addWhere("activity_type_id", "=", $activityType)
      ->addWhere("status_id", "IN", self::enforceArray($activityStati));
    if (!empty($customFields))
      foreach ($customFields as $customField) 
        $getActivities = $getActivities->addSelect(self::_getFieldName($customGroup, $customField));

    return $getActivities
      ->execute();
  } 

  protected static function enforceArray($intOrStringOrArray) {
    if (is_array($intOrStringOrArray))
      return $intOrStringOrArray;
    if (is_numeric($intOrStringOrArray)) 
      return [$intOrStringOrArray];
    return explode(',', $intOrStringOrArray);
  }
}
