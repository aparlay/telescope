<?php

namespace Aparlay\Core\Admin\Filters;

abstract class AbstractBaseFilter
{
    protected string $fieldName;
    protected string $fieldType;
    protected string|int|array|bool $fieldValue;
    protected string|null $internalFieldName;

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

    public function setDefaultValue($fieldValue)
    {
        $this->setFieldValue($fieldValue);

        return $this;
    }

    /**
     * @param mixed $internalName
     *
     * @return $this
     */
    public function setInternalFieldName($internalName)
    {
        $this->internalFieldName = $internalName;

        return $this;
    }

    public function getInternalFieldName(): string
    {
        return $this->internalFieldName ?? $this->fieldName;
    }
}
