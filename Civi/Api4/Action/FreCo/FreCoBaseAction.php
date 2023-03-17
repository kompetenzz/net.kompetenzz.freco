<?php

namespace Civi\Api4\Action\FreCo;

abstract class FreCoBaseAction extends \Civi\Api4\Generic\AbstractAction {

    /**
     * FK to civicrm_group.id to filter by contact group
     * 
     * @var int
     */
    protected $groupId;

    /**
     * FK to civicrm_option_value.id, that has to be valid, registered activity type
     * 
     * @var int
     */
    protected $activityTypeId;

    /**
     * List of valid acivity stati. Either as comma separated string or as array of ints
     * @var int|array|string
     */
    protected $activityStatusIds = [];
    
    /**
     * FK to civicrm_option_value.id, that has to be valid, registered custom group 
     * 
     * @var int
     * @required
     */
    protected $customGroupId;
    
    /** 
     * List of valid custom field ids. Either as comma separated string or as array of ints
     * 
     * @var int|array|string
     */
    protected $customFieldIds = [];
 }