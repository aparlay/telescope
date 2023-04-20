<?php

namespace Aparlay\Core\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use MongoDB\BSON\ObjectId;

class BaseModel extends \Jenssegers\Mongodb\Eloquent\Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Qualify the given column name by the model's table.
     *
     * @param string $column
     *
     * @return string
     */
    public function qualifyColumn($column)
    {
        return $column;
        /*
        if (str_contains($column, '.')) {
            return $column;
        }

        return $this->getTable().'.'.$column;
        */
    }

    /**
     * Get only class name without namespace.
     *
     * @return bool|string
     */
    public static function shortClassName()
    {
        return substr(strrchr(static::class, '\\'), 1);
    }

    public function addToSet(string $attribute, mixed $item, ?int $length = null): void
    {
        if (!is_array($this->$attribute)) {
            $this->$attribute = [];
        }
        $values           = $this->$attribute;
        if (!in_array($item, $values, false)) {
            array_unshift($values, $item);
        }

        if (null !== $length) {
            $values = array_slice($values, 0, $length);
        }

        $this->$attribute = $values;
    }

    public function removeFromSet(string $attribute, mixed $item): void
    {
        if (!is_array($this->$attribute)) {
            $this->$attribute = [];
        }
        $values           = $this->$attribute;
        if (($key = array_search($item, $values, false)) !== false) {
            unset($values[$key]);
            if (is_int($key)) {
                $values = array_values($values);
            }
        }

        $this->$attribute = $values;
    }

    public function addToPosition(string $attribute, mixed $content, int $position = 0): void
    {
        $this::raw(function ($collection) use ($attribute, $content, $position) {
            $collection->findOneAndUpdate(
                ['_id' => $this->_id],
                [
                    '$push' => [
                        $attribute => [
                            '$each' => [$content],
                            '$position' => $position,
                        ],
                    ],
                ]
            );
        });
    }

    public function updateNestedArray(string $attribute, string $nestedField, mixed $nestedValue, string $keyName, mixed $keyValue): void
    {
        $this::raw(function ($collection) use ($attribute, $nestedField, $nestedValue, $keyName, $keyValue) {
            $collection->findOneAndUpdate(
                [],
                [
                    '$set' => [
                        $attribute . '$[elem].' . $nestedField => $nestedValue,
                    ],
                ],
                [
                    'arrayFilters' => [
                        'elem.' . $keyName => $keyValue,
                    ],
                ]
            );
        });
    }

    public function getCollection(): string
    {
        return $this->collection;
    }

    public function setAttribute($key, $value)
    {
        if ($this->hasCast($key)) {
            $value = $this->castAttribute($key, $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @return bool
     */
    public function creatorIs(User|Authenticatable|ObjectId|string $user)
    {
        $userId = false;
        if ($user instanceof ObjectId) {
            $userId = (string) $user;
        } elseif ($user instanceof Authenticatable) {
            $userId = (string) $user->_id;
        }

        return (string) $this->creatorObj->_id === $userId;
    }

    /**
     * @return bool
     */
    public function idEqualTo($value)
    {
        return (string) $this->_id === (string) $value;
    }

    public function getMorphClass()
    {
        return self::shortClassName();
    }
}
