<?php

namespace Aparlay\Core\Builders;

use DateTimeInterface;
use Illuminate\Support\Str;
use MongoDB\BSON\UTCDateTime;

class Builder extends \Jenssegers\Mongodb\Query\Builder
{
    /**
     * Compile the where array.
     *
     * @return array
     */
    protected function compileWheres()
    {
        // The wheres to compile.
        $wheres = $this->wheres ?: [];

        // We will add all compiled wheres to this array.
        $compiled = [];

        foreach ($wheres as $i => &$where) {
            // Make sure the operator is in lowercase.
            if (isset($where['operator'])) {
                $where['operator'] = strtolower($where['operator']);

                // Operator conversions
                $convert = [
                    'regexp' => 'regex',
                    'elemmatch' => 'elemMatch',
                    'geointersects' => 'geoIntersects',
                    'geowithin' => 'geoWithin',
                    'nearsphere' => 'nearSphere',
                    'maxdistance' => 'maxDistance',
                    'centersphere' => 'centerSphere',
                    'uniquedocs' => 'uniqueDocs',
                ];

                if (array_key_exists($where['operator'], $convert)) {
                    $where['operator'] = $convert[$where['operator']];
                }
            }

            // Convert id's.
            if (isset($where['column']) && Str::endsWith($where['column'], '_id')) {
                // Multiple values.
                if (isset($where['values'])) {
                    foreach ($where['values'] as &$value) {
                        $value = $this->convertKey($value);
                    }
                } // Single value.
                elseif (isset($where['value'])) {
                    $where['value'] = $this->convertKey($where['value']);
                }
            }

            // Convert DateTime values to UTCDateTime.
            if (isset($where['value'])) {
                if (is_array($where['value'])) {
                    array_walk_recursive($where['value'], function (&$item, $key) {
                        if ($item instanceof DateTimeInterface) {
                            $item = new UTCDateTime($item->format('Uv'));
                        }
                    });
                } else {
                    if ($where['value'] instanceof DateTimeInterface) {
                        $where['value'] = new UTCDateTime($where['value']->format('Uv'));
                    }
                }
            } elseif (isset($where['values'])) {
                array_walk_recursive($where['values'], function (&$item, $key) {
                    if ($item instanceof DateTimeInterface) {
                        $item = new UTCDateTime($item->format('Uv'));
                    }
                });
            }

            // The next item in a "chain" of wheres devices the boolean of the
            // first item. So if we see that there are multiple wheres, we will
            // use the operator of the next where.
            if ($i == 0 && count($wheres) > 1 && $where['boolean'] == 'and') {
                $where['boolean'] = $wheres[$i + 1]['boolean'];
            }

            // We use different methods to compile different wheres.
            $method = "compileWhere{$where['type']}";
            $result = $this->{$method}($where);

            // Wrap the where with an $or operator.
            if ($where['boolean'] == 'or') {
                $result = ['$or' => [$result]];
            }

            // If there are multiple wheres, we will wrap it with $and. This is needed
            // to make nested wheres work.
            elseif (count($wheres) > 1) {
                $result = ['$and' => [$result]];
            }

            // Merge the compiled where with the others.
            $compiled = array_merge_recursive($compiled, $result);
        }

        return $compiled;
    }
}
