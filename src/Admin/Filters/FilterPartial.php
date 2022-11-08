<?php

namespace Aparlay\Core\Admin\Filters;

class FilterPartial extends AbstractBaseFilter
{
    public function __construct(
        protected string $fieldName,
        protected string $fieldType,
        protected string|null $internalFieldName = null
    ) {
    }

    public function __invoke($query)
    {
        $query->where($this->getInternalFieldName(), 'like', '%'.$this->fieldValue.'%');
    }
}
