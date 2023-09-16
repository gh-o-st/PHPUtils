<?php

/**
 * ArrMate - Advanced Array Utility for PHP
 * 
 * A comprehensive utility library tailored for complex array operations in PHP.
 * 
 * Features:
 * - Deep search within multi-dimensional arrays.
 * - Recursive map for nested data manipulation.
 * - Distinct array merging to handle overlapping keys.
 * - Efficient extraction and insertion with pluck and inject.
 * - Utility functions for clear array navigation.
 * - Dot notation for array flattening and reconstruction.
 * 
 * Designed to simplify common tasks and address intricate array challenges.
 */
class ArrMate {

    /**
     * Recursively searches for a given value in a multi-dimensional array.
     *
     * @param mixed $needle   The searched value.
     * @param array $haystack The array to search in.
     * @return string|bool    The key if found, otherwise false.
     */
    public static function search_recursive($needle, array $haystack) {
        return self::search_or_key_exists_recursive($needle, $haystack, 'value');
    }

    /**
     * Recursively checks if the given key exists in a multi-dimensional array.
     *
     * @param string|int $needle   The searched key.
     * @param array      $haystack The array to search in.
     * @return string|bool         The key if exists, otherwise false.
     */
    public static function key_exists_recursive($needle, array $haystack) {
        return self::search_or_key_exists_recursive($needle, $haystack, 'key');
    }

    /**
     * A unified "recursion" method to search for a key or a value in a multi-dimensional array.
     * Dumps to an explicit stack instead of using recursion to avoid stack overflow.
     *
     * @param mixed  $needle   The searched value or key.
     * @param array  $haystack The array to search in.
     * @param string $mode     Either 'value' to search by value or 'key' to search by key.
     * @return string|bool     The key if found or exists, otherwise false.
     */
    private static function search_or_key_exists_recursive($needle, array $haystack, string $mode) {
        $stack = [$haystack];

        while (!empty($stack)) {
            $currentArray = array_pop($stack);
    
            foreach ($currentArray as $key => $value) {
                if (
                    ($mode === 'value' && $needle === $value) ||
                    ($mode === 'key' && $needle === $key)
                ) {
                    return true;
                }
    
                if (is_array($value)) {
                    $stack[] = $value;
                }
            }
        }
    
        return false;
    }

    /**
     * Applies a callback function recursively to each element of an array.
     *
     * @param callable $callback The callback function to run for each element.
     * @param array    $array    The input array.
     * @return array              The resulting array after applying the callback.
     */
    public static function map_recursive(callable $callback, array $array): array {
        $func = function ($item) use (&$func, &$callback) {
            return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
        };
        return array_map($func, $array);
    }

    /**
     * Merges two arrays recursively, overwriting keys of the same name.
     *
     * @param array $array1 The first input array.
     * @param array $array2 The second input array.
     * @return array         The merged array.
     */
    public static function merge_recursive_distinct(array &$array1, array &$array2): array {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }
        return $merged;
    }

    /**
     * Removes an element from an array by its value.
     *
     * @param array $array The input array.
     * @param mixed $value The value to remove.
     * @return array       The resulting array.
     */
    public static function remove_by_value(array $array, $value): array {
        if (($key = array_search($value, $array, true)) !== false) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * Extracts a single value from a list of arrays or objects.
     *
     * @param array  $array The input list of arrays or objects.
     * @param string $key   The key or property name to pluck.
     * @return array        List of values.
     */
    public static function pluck(array $array, string $key): array {
        return array_map(function($v) use ($key) {
            return is_object($v) ? $v->$key : $v[$key];
        }, $array);
    }

    /**
     * Injects a single value into a list of arrays or objects.
     *
     * @param array  $array The input list of arrays or objects.
     * @param string $key   The key or property name to inject.
     * @param mixed  $value The value to inject.
     * @return array        The resulting list of arrays or objects.
     */
    public static function inject(array $array, string $key, $value): array {
        return array_map(function($v) use ($key, $value) {
            $v[$key] = $value;
            return $v;
        }, $array);
    }

    /**
     * Returns the first element of an array that satisfies the given predicate.
     *
     * @param array    $array     The input array.
     * @param callable $predicate The predicate function.
     * @return mixed              The first element that satisfies the predicate.
     */
    public static function find(array $array, callable $predicate) {
        foreach ($array as $key => $value) {
            if (call_user_func($predicate, $value, $key)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Shoves the given elements onto the beginning of the array,
     * while popping the same number of elements from the end.
     *
     * @param array $array The input array.
     * @param mixed ...$values The values to shove onto the beginning.
     * @return array The values that were popped from the end.
     * @throws InvalidArgumentException if too many values are provided.
     */
    public static function shove(array &$array, ...$values): array {
        $numValues = count($values);

        // If no values are provided, return an empty array.
        if ($numValues === 0) {
            return [];
        }

        // If there are more values to add than exist in the array, throw an exception.
        if ($numValues > count($array)) {
            throw new \InvalidArgumentException("Input values exceed allocated space.");
        }

        $popped = array_splice($array, -$numValues);
        array_unshift($array, ...$values);
        return $popped;
    }

    /**
     * Returns the first key of an array.
     *
     * @param array $array The input array.
     * @return mixed       The first key of the array.
     */
    public static function kfirst(array $array) {
        reset($array);
        return key($array);
    }

    /**
     * Returns the last key of an array.
     *
     * @param array $array The input array.
     * @return mixed       The last key of the array.
     */
    public static function klast(array $array) {
        end($array);
        return key($array);
    }

    /**
     * Returns the middle key of an array.
     *
     * @param array $array The input array.
     * @return mixed       The middle key of the array.
     */
    public static function kmiddle(array $array) {
        $keys = array_keys($array);
        return $keys[floor(count($keys) / 2)];
    }

    /**
     * Returns the first element of an array.
     *
     * @param array $array The input array.
     * @return mixed       The first element of the array.
     */
    public static function first(array $array) {
        return reset($array);
    }

    /**
     * Returns the last element of an array.
     *
     * @param array $array The input array.
     * @return mixed       The last element of the array.
     */
    public static function last(array $array) {
        return end($array);
    }

    /**
     * Returns the middle element of an array.
     *
     * @param array $array The input array.
     * @return mixed       The middle element of the array.
     */
    public static function middle(array $array) {
        return $array[floor(count($array) / 2)];
    }

    /**
     * Turns a multi-dimensional array into a single-dimensional array, using a delimiter.
     *
     * @param array $array
     * @param string $prepend
     * @return array
     */
    public static function dot(array $array, string $prepend = ''): array {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, self::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }

    /**
     * Turns a single-dimensional array into a multi-dimensional array, using a delimiter.
     *
     * @param array $array
     * @return array
     */
    public static function undot(array $array): array {
        $results = [];
        foreach ($array as $key => $value) {
            self::set($results, $key, $value);
        }
        return $results;
    }

    /**
     * Sets a value in a multi-dimensional array by using a dot notation for the key.
     *
     * @param array  $array The input array.
     * @param string $key   The key to set.
     * @param mixed  $value The value to set.
     * @return array        The resulting array.
     */
    public static function set(array $array, string $key, $value): array {
        $keys = explode('.', $key);
        $copy = $array;
        $temp = &$copy;
        
        foreach ($keys as $index => $key) {
            if ($index === count($keys) - 1) {
                $temp[$key] = $value;
            } else {
                if (!isset($temp[$key]) || !is_array($temp[$key])) {
                    $temp[$key] = [];
                }
                $temp = &$temp[$key];
            }
        }
    
        return $copy;
    }
}
