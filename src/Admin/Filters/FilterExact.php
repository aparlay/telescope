<?php

namespace Aparlay\Core\Admin\Filters;

class FilterExact extends AbstractBaseFilter
{
    public function __construct(
        protected string $fieldName,
        protected string $fieldType
    ) {
    }

    public function __invoke($query)
    {
        $query->where($this->fieldName, $this->fieldValue);
    }
}
