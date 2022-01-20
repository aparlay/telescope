<?php

namespace Aparlay\Core\Admin\Filters;

abstract class AbstractBaseFilter
{
    protected $fieldName;
    protected $fieldType;
    protected $fieldValue;

    abstract function __invoke($query);

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return mixed
     */
    public function getCastType()
    {
        return $this->fieldType;
    }

    /**
     * @return mixed
     */
    public function getFieldValue()
    {
        return $this->fieldValue;
    }

    /**
     * @param mixed $fieldValue
     */
    public function setFieldValue($fieldValue): void
    {
        $this->fieldValue = $fieldValue;
    }




}
