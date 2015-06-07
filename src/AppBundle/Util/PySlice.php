<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 12:10 AM
 */

namespace AppBundle\Util;


class PySlice {

    /**
     * Implementation of parts of pythons slice behavior. Well it covers the part I want.
     * From http://se2.php.net/manual/en/function.substr.php#103143
     * @param  string $input
     * @param  string $slice
     * @return string
     */
    public static function slice($input, $slice) {
        $arg = explode(':', $slice);
        $start = intval($arg[0]);
        if ($start < 0) {
            $start += strlen($input);
        }
        if (count($arg) === 1) {
            return substr($input, $start, 1);
        }
        if (trim($arg[1]) === '') {
            return substr($input, $start);
        }
        $end = intval($arg[1]);
        if ($end < 0) {
            $end += strlen($input);
        }
        return substr($input, $start, $end - $start);
    }

}
