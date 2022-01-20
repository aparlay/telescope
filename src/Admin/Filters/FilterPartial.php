<?php

namespace Aparlay\Core\Admin\Filters;

use MongoDB\BSON\Regex;

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
        $query->where($this->fieldName, 'regex', new Regex('^'.$this->fieldValue));
    }
}
