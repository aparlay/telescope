<?php

namespace Aparlay\Core\Admin\Filters;

class FilterPartial extends AbstractBaseFilter
{
    /**
     * @param $fieldName
     * @param $fieldType
     */
    public function __construct($fieldName, $fieldType)
    {
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
    }

    public function __invoke($query)
    {
        return $query->where($this->fieldName, $this->fieldValue);
    }
}
