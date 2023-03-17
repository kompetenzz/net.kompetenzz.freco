<?php
namespace Civi\FreCo;
/**
 * @package Civi
 */
abstract class FreCoBase {
  protected static $optionsCache = [];

  protected static function _fieldIsComputable_UNUSED($customField) {
    if (!$customField['is_active'])
      return false;
    if (in_array($customField['data_type'], ["Int","Date","Boolean"]))
      return true;
    if ($customField['option_group_id'])
      return true;
    return false;
  }



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

  protected static function _getGroup($groupId) {
    $grps = \Civi\Api4\Group::get()
      ->addWhere('id', '=', $groupId)
      ->setLimit(1)
      ->execute(); 
    if (!count($grps))
      return null;
    return $grps[0];
  }

  protected static function _getCustomFields($customGroup, $customFieldIds, $cache = true) {
    $getFields = \Civi\Api4\CustomField::get()
    ->addWhere('custom_group_id', '=', $customGroup['id'])
    ->addWhere('is_active', '=', TRUE)
    ->addClause('OR', 
      ['data_type', 'IN', ['Int', 'Boolean', 'Date']], 
      ['option_group_id', 'IS NOT EMPTY']);
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

  protected static function _getContacts($customGroup, $customFields, $groupId = 0) {
    $getContacts = \Civi\Api4\Contact::get();

    if (!empty($customFields)) {
      $hasAnyFieldSetClause = [];
      foreach ($customFields as $customField) 
        array_push($hasAnyFieldSetClause, [self::_getFieldName($customGroup, $customField), 'IS NOT NULL']);
      $getContacts = $getContacts
        ->addClause('OR', $hasAnyFieldSetClause);
    }

    if ($groupId > 0) {
      $getContacts = $getContacts
        ->addJoin('GroupContact AS group_contact', 'LEFT', ['id', '=', 'group_contact.group_id'])
        ->addWhere('group_contact.group_id', '=', $groupId);
    }
    if (!empty($customFields))
      foreach ($customFields as $customField) 
        $getContacts = $getContacts->addSelect(self::_getFieldName($customGroup, $customField));

    return $getContacts
      ->execute();

  }

  protected static function _getActivities($activityType, $activityStati, $customGroup, $customFields, $groupId) {
    $getActivities = \Civi\Api4\Activity::get()
      ->addWhere("activity_type_id", "=", $activityType)
      ->addWhere("status_id", "IN", self::enforceArray($activityStati));
    if ($groupId > 0) {
      $getActivities = $getActivities
        ->addJoin('ActivityContact AS activity_contact', 'LEFT', ['id', '=', 'activity_contact.activity_id'])
        ->addJoin('GroupContact AS group_contact', 'LEFT', ['activity_contact.id', '=', 'group_contact.contact_id'])
        ->addWhere('group_contact.group_id', '=', $groupId);
    }
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
