<?php

namespace Aparlay\Core\Admin\Filters;

class FilterExact extends AbstractBaseFilter
{
    /**
     * @param $fieldName
     * @param $fieldType
     * $param $fieldValue;
     */
    public function __construct($fieldName, $fieldType)
    {
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
    }


    public function __invoke($query)
    {
        $query->where($this->fieldName, $this->fieldValue);
    }

}
