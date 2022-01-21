<?php

namespace Aparlay\Core\Admin\Filters;

class FilterScope extends AbstractBaseFilter
{
    public function __construct(
        protected string $fieldName,
        protected string $fieldType,
        protected string $scopeName
    ) {
    }

    public function __invoke($query)
    {
        $scopeName = $this->getScopeName();

        return $query->$scopeName($this->getFieldValue());
    }

    /**
     * @return mixed
     */
    public function getScopeName()
    {
        return $this->scopeName;
    }
}
