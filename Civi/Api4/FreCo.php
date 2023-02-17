<?php
namespace Civi\Api4;

use Civi\Api4\Action\FreCo\Compute;
use Civi\Api4\Action\FreCo\Info;

/**
 * @package Civi\Api4
 */
class FreCo extends Generic\AbstractEntity {

/**
   * Every entity **must** implement `getFields`.
   *
   * This tells the action classes what input/output fields to expect,
   * and also populates the _API Explorer_.
   *
   * The `BasicGetFieldsAction` takes a callback function. We could have defined the function elsewhere
   * and passed a `callable` reference to it, but passing in an anonymous function works too.
   *
   * The callback function takes the `BasicGetFieldsAction` object as a parameter in case we need to access its properties.
   * Especially useful is the `getAction()` method as we may need to adjust the list of fields per action.
   *
   * Note that it's possible to bypass this function if an action class lists its own fields by declaring a `fields()` method.
   *
   * Read more about how to implement your own `GetFields` action:
   * @see \Civi\Api4\Generic\BasicGetFieldsAction
   *
   * @param bool $checkPermissions
   *
   * @return Generic\BasicGetFieldsAction
   */
  public static function getFields($checkPermissions = TRUE) {
    return (new Generic\BasicGetFieldsAction(__CLASS__, __FUNCTION__, function($getFieldsAction) {
      return [
        [
          'name' => 'id',
          'description' => 'Field id + option/value name',
        ],
        [
          'name' => 'name',
          'description' => "Option/Value name",
        ],
        [
          'name' => 'title',
          'description' => "Option/Value title",
        ],
        [
          'name' => 'field_id',
          'description' => "The custom field id",
        ],
        [
          'name' => 'field_name',
          'description' => "The custom field title",
        ],
        [
          'name' => 'field_title',
          'description' => "The custom field title",
        ],
        [
          'name' => 'value',
          'data_type' => 'Integer',
          'description' => "The result for this field aggregation.",
        ]
      ];
    }))->setCheckPermissions($checkPermissions);
  }

  /**
   * @param bool $checkPermissions
   * @return Action\FreCo\Compute
   */
  public static function compute($checkPermissions = TRUE) {
    return (new Compute(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @param bool $checkPermissions
   * @return Action\FreCo\Info
   */
  public static function info($checkPermissions = TRUE) {
    return (new Info(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }
}
