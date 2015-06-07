<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 12:12 AM
 */

namespace AppBundle\Util;


class Temperature {
    /**
     * Format temp
     * @param string $input
     * @param string $unit
     * @param string $round
     * @return number
     */
    public static function mktemp($input, $unit = "c", $round = true) {
        if ($input == null)
            return null;

        $input = explode('.', $input)[0];
        $temp = PySlice::slice($input, ':-3') . '.' . substr($input, 2, 5);
        if ($unit == "f") {
            $t = (((double) $temp) * 1.8) + 32;
            $temp = $t;
        }

        $ret = 0;
        if ($round)
            $ret = round($temp, 1);
        else
            $ret = $temp;
        return $ret;
    }
}
