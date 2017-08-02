<?php

namespace Lib;

class ValidateLatLog {

    /**
     * Validates a given latitude $lat
     *
     * @param float|int|string $lat Latitude
     * @return bool `true` if $lat is valid, `false` if not
     */
    function validateLatitude($lat) {
        return preg_match('/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/', $lat);
    }

    /**
     * Validates a given longitude $long
     *
     * @param float|int|string $long Longitude
     * @return bool `true` if $long is valid, `false` if not
     */
    function validateLongitude($long) {
        return preg_match('/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/', $long);
    }

    /**
     * Validates a given coordinate
     *
     * @param float|int|string $lat Latitude
     * @param float|int|string $long Longitude
     * @return bool `true` if the coordinate is valid, `false` if not
     */
    function validateLatLong($latlong) {
        if (sizeof($latlong) != 2) {
            return false;
        }
        return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $latlong[0] . ',' . $latlong[1]);
    }

}
