<?php

namespace Aheadworks\CustGroupCatPermissions\Service;

class CategoryChildren
{
    private $childrenIds;

    public function setIds($childrenIds)
    {
        $this->childrenIds = $childrenIds;
    }

    public function getIds()
    {
        return $this->childrenIds;
    }
}
