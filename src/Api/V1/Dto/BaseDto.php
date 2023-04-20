<?php

namespace Aparlay\Core\Api\V1\Dto;

use ErrorException;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
abstract class BaseDto extends DataTransferObject
{
    /**
     * @throws ErrorException
     *
     * @return void
     */
    public function load(array $data)
    {
        foreach ($data as $prop => $value) {
            if (!property_exists($this, $prop)) {
                $className = get_class($this);

                throw new ErrorException("{$prop} does not exists for this DTO {$className}");
            }
            $this->{$prop} = $value;
        }
    }
}
