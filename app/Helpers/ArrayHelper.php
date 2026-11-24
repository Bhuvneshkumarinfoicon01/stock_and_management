<?php

if (!function_exists('array_diff_assoc_recursive')) {
    function array_diff_assoc_recursive(array $a, array $b): array
    {
        $diff = [];
        foreach ($a as $key => $value) {
            if (array_key_exists($key, $b)) {
                if (is_array($value)) {
                    $sub = array_diff_assoc_recursive($value, $b[$key]);
                    if ($sub)
                        $diff[$key] = $sub;
                } elseif ($value !== $b[$key]) {
                    $diff[$key] = $value;
                }
            } else {
                $diff[$key] = $value;
            }
        }
        return $diff;
    }
}
