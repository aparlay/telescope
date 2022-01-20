<?php

namespace Aparlay\Core\Admin\Filters;

class FilterScope extends AbstractBaseFilter
{
    protected $scopeName;

    /**
     * @param $fieldName
     * @param $fieldType
     */
    public function __construct($fieldName, $fieldType, $scopeName)
    {
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
        $this->scopeName = $scopeName;
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
