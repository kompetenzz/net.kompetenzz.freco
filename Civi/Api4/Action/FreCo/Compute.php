<?php

namespace Civi\Api4\Action\FreCo;
use Civi\Api4\Generic\Result;

class Compute extends FreCoBaseAction {
    
    public function _run(Result $result) {
        foreach (\Civi\FreCo\Computer::run(
            $this->groupId,
            $this->activityTypeId, $this->activityStatusIds, 
            $this->customGroupId, $this->customFieldIds) as $r)
            $result[] = $r;
    }
}