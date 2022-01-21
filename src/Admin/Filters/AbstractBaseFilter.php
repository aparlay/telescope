<?php

namespace Aparlay\Core\Admin\Filters;

abstract class AbstractBaseFilter
{
    protected string $fieldName;
    protected string $fieldType;
    protected $fieldValue;

    abstract public function __invoke($query);

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
