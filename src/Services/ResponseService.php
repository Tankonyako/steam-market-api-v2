<?php

namespace SteamApi\Services;

class ResponseService
{
    /**
     * @param $data
     * @return array
     */
    public static function fillBaseData($data): array
    {
        return [
            'start'       => $data['start'],
            'page_size'   => $data['pagesize'],
            'total_count' => $data['total_count'],
            'listings'    => []
        ];
    }

    /**
     * @param $assets
     * @param $listingAssetData
     * @return array
     */
    public static function getAssetData($assets, $listingAssetData): array
    {
        $asset = $assets[$listingAssetData['appid']][$listingAssetData['contextid']][$listingAssetData['id']];

        return [
            'id' => $asset['id'],
            'class_id' => $asset['classid'],
            'instance_id' => $asset['instanceid'],
            'market_hash_name' => $asset['market_hash_name'],
            'icon_url' => "https://steamcommunity-a.akamaihd.net/economy/image/" . $asset['icon_url'],
            'icon_url_large' => "https://steamcommunity-a.akamaihd.net/economy/image/" . $asset['icon_url_large'],
            'amount' => $asset['amount'],
            'status' => $asset['status'],
            'tradable' => $asset['tradable'],
            'marketable' => $asset['marketable'],
            'inspect_link' => str_replace("%assetid%", $asset['id'], $asset['actions'][0]['link'])
        ];
    }

    public static function filterData($data, $select, $makeHidden): array
    {
        $returnData = self::selectKeys($data, $select);

        self::hideKeys($returnData, $makeHidden);

        return $returnData;
    }

    /**
     * @param $arr
     * @param $keys
     * @return array
     */
    public static function selectKeys($arr, $keys): array
    {
        if (!$keys)
            return $arr;

        $saved = [];

        foreach ($keys as $key => $value) {
            if (is_int($key) || is_int($value))
                $keysKey = $value;
            else
                $keysKey = $key;

            if (isset($arr[$keysKey])) {
                $saved[$keysKey] = $arr[$keysKey];

                if (is_array($value))
                    $saved[$keysKey] = self::selectKeys($saved[$keysKey], $keys[$keysKey]);
            }
        }

        return $saved;
    }

    /**
     * @param $arr
     * @param $keys
     */
    public static function hideKeys(&$arr, $keys)
    {
        foreach ($keys as $key => $value) {
            if (is_int($key) || is_int($value))
                $keysKey = $value;
            else
                $keysKey = $key;

            if (isset($arr[$keysKey])) {
                if (is_array($value))
                    self::hideKeys($arr[$keysKey], $keys[$keysKey]);
                else
                    unset($arr[$keysKey]);
            }
        }
    }
}