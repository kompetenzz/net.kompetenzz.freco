<?php

namespace Civi\Api4\Action\FreCo;
use Civi\Api4\Generic\Result;

class Info extends FreCoBaseAction {
    
    public function _run(Result $result) {
        $result[] = \Civi\FreCo\Info::run(
            $this->activityTypeId, $this->activityStatusIds, 
            $this->customGroupId, $this->customFieldIds);
    }

    public static function fields() {
        return [
            ['name' => 'activity_type'],
            ['name' => 'activity_stati'],
            ['name' => 'custom_group'],
            ['name' => 'custom_fields'],
            ['name' => 'items', 'data_type' => 'Integer']
        ];
      }
}