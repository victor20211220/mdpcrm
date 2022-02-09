<?php

class ProductHelper
{
    /**
     * Get search string by product name
     * @param $productName
     * @return null|string|string[]
     */
    public static function getSearchString($productName)
    {
        $productName = strtolower($productName);
        $productName = preg_replace('/[\s\!\@\#\$\%\^\&\*\(\)\_\-\=\+]/', '', $productName);

        return $productName;
    }
}
