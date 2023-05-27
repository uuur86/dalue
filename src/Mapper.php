<?php

namespace Dalue;

use StrObj\StringObjects;

class Mapper
{

    /**
     * Creates an array with new values using exists data.
     * 
     * @param array         $paths    The paths data that going to assign value
     * @param callable|null $callback User defined callback function with key-value pair
     * 
     * @return mixed
     */
    public static function map(StringObjects $data, array $paths, ?callable $callback = null)
    {
        $return = [];

        while ($key = key($paths)) {
            $has_many = false;
            $path = current($paths);

            if (substr_count($key, '[]') > 0) {
                $key = substr($key, 0, -2);
                $has_many = true;
            }

            if (is_array($path)) {
                $value = self::map($data, $path, $callback);

                if ($has_many) {
                    $value = self::mergeColumns($value);
                }

                $return[$key] = empty($callback) ? $value : $callback($key, $value);
                next($paths);

                continue;
            }

            // If the $path has a string value
            $value = $path;

            // If the $path has a data path
            if (is_string($path) && substr($path, 0, 6) === '@data/') {
                $path  = strlen($path) > 6 ? substr($path, 6) : '';
                $value = $data->get($path, '');
            }

            $return[$key] = empty($callback) ? $value : $callback($key, $value);

            next($paths);

        }

        return $return;
    }


    /**
     * Merges multiple column data rows
     *
     * @param array $cols data rows
     *
     * @return array
     */
    public static function mergeColumns(array $cols)
    {
        $return = [];

        foreach ($cols as $col_key => $col) {
            $i = 0;

            if (empty($col) || !is_array($col)) continue;

            foreach ($col as $row) {
                $return[$i++][$col_key] = $row;
            }
        }

        return $return;
    }
}
