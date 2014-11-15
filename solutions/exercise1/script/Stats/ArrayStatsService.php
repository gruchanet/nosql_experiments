<?php

namespace Stats;

class ArrayStatsService
{
    public static function keysToString($keys, $delimiter, $withCounters = false)
    {
        if ($withCounters) {
            $keys = array_map(
                function ($key, $value) {
                    return $value . '(' . $key . ')';
                },
                $keys,
                array_keys($keys)
            );
        }

        return implode($delimiter, $keys);
    }
}