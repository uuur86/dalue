<?php

namespace Dalue;

use StrObj\StringObjects;

/**
 * Maps source data into a new structure described by a schema array.
 *
 * Schema rules:
 *  - A string starting with "@data/" is read from the source data by its path.
 *  - Any other value is copied to the result unchanged.
 *  - A nested array is mapped recursively.
 *  - A key ending with "[]" turns the columns of a nested schema into rows.
 */
class Mapper
{
    /**
     * Prefix that marks a schema value as a path into the source data.
     */
    public const DATA_PREFIX = '@data/';

    /**
     * Key suffix that marks a nested schema as a collection of rows.
     */
    public const COLLECTION_SUFFIX = '[]';

    /**
     * Creates an array with new values using existing data.
     *
     * @param StringObjects            $data     The source data
     * @param array<int|string, mixed> $paths    The schema: target keys and the values or paths assigned to them
     * @param callable|null            $callback Optional callback receiving ($key, $value); its return value is stored
     * @param mixed                    $default  Value used for paths that do not exist in the source data
     *
     * @return array<int|string, mixed>
     */
    public static function map(StringObjects $data, array $paths, ?callable $callback = null, $default = null): array
    {
        $result = [];

        foreach ($paths as $key => $path) {
            $isCollection = is_string($key) && self::isCollectionKey($key);

            if ($isCollection) {
                $key = substr($key, 0, -strlen(self::COLLECTION_SUFFIX));
            }

            if (is_array($path)) {
                $value = self::map($data, $path, $callback, $default);

                if ($isCollection) {
                    $value = self::mergeColumns($value);
                }
            } else {
                $value = self::resolve($data, $path, $default);
            }

            $result[$key] = $callback === null ? $value : $callback($key, $value);
        }

        return $result;
    }

    /**
     * Merges column lists into rows.
     *
     * ['sku' => ['A', 'B'], 'qty' => [1, 2]] becomes
     * [['sku' => 'A', 'qty' => 1], ['sku' => 'B', 'qty' => 2]].
     * Columns that are not non-empty arrays are skipped. When the columns have
     * different lengths, every row of the longest column is kept and the missing
     * cells are filled with null.
     *
     * @param array<int|string, mixed> $cols Column lists keyed by column name
     *
     * @return list<array<int|string, mixed>>
     */
    public static function mergeColumns(array $cols): array
    {
        $columns = [];
        $rowCount = 0;

        foreach ($cols as $name => $col) {
            if (empty($col) || !is_array($col)) {
                continue;
            }

            $columns[$name] = array_values($col);
            $rowCount = max($rowCount, count($col));
        }

        $rows = [];

        for ($i = 0; $i < $rowCount; $i++) {
            $row = [];

            foreach ($columns as $name => $values) {
                $row[$name] = array_key_exists($i, $values) ? $values[$i] : null;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Checks whether a schema key marks a collection.
     *
     * @param string $key Schema key
     *
     * @return bool
     */
    private static function isCollectionKey(string $key): bool
    {
        $length = strlen(self::COLLECTION_SUFFIX);

        return strlen($key) >= $length && substr($key, -$length) === self::COLLECTION_SUFFIX;
    }

    /**
     * Resolves a single schema value.
     *
     * @param StringObjects $data    The source data
     * @param mixed         $path    Schema value
     * @param mixed         $default Value used for missing paths
     *
     * @return mixed
     */
    private static function resolve(StringObjects $data, $path, $default)
    {
        if (!is_string($path) || strpos($path, self::DATA_PREFIX) !== 0) {
            return $path;
        }

        $path = (string) substr($path, strlen(self::DATA_PREFIX));

        // Wildcard reads always return a list, so only concrete paths can be missing.
        if ($path !== '' && strpos($path, '*') === false && !$data->has($path)) {
            return $default;
        }

        return $data->get($path, $default);
    }
}
