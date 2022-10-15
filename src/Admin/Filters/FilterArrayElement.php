<?php

namespace Aparlay\Core\Admin\Filters;

final class FilterArrayElement extends AbstractBaseFilter
{
    public function __construct(
        protected string  $fieldName,
        protected string  $fieldType,
        protected string  $elementFieldName,
        protected ?string $internalFieldName,
        protected bool    $partial = true
    )
    {
    }

    public function __invoke($query)
    {
        $matches = [
            '$or' => array_map(function ($value) {
                $value = trim($value);

                if ($this->partial) {
                    return [
                        $this->elementFieldName => [
                            '$regex' => $value
                        ]
                    ];
                } else {
                    return [
                        $this->elementFieldName => [
                            '$eq' => $value
                        ]
                    ];
                }
            }, explode(',', $this->fieldValue))
        ];

        $query->where($this->getInternalFieldName(), ['$elemMatch' => $matches]);
    }
}
