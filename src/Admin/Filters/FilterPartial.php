<?php

namespace Aparlay\Core\Admin\Filters;

use MongoDB\BSON\Regex;

class FilterPartial extends AbstractBaseFilter
{
    public function __construct(
        protected string $fieldName,
        protected string $fieldType
    ) {
    }

    public function __invoke($query)
    {
        $query->where($this->fieldName, 'regex', new Regex('^'.$this->fieldValue));
    }
}
