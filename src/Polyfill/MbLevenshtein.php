<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Polyfill;

class MbLevenshtein
{
    public static function mb_levenshtein(
            string $string1,
            string $string2,
            int $insertion_cost = 1,
            int $replacement_cost = 1,
            int $deletion_cost = 1
    ): int {
        if (mb_strlen($string1) == 0) {
            return mb_strlen($string2) * $insertion_cost;
        }

        if (mb_strlen($string2) == 0) {
            return mb_strlen($string1) * $deletion_cost;
        }

        $p1 = [];
        $p2 = [];

        for ($i2 = 0; $i2 <= mb_strlen($string2); $i2++) {
            $p1[$i2] = $i2 * $insertion_cost;
        }

        for ($i1 = 0; $i1 < mb_strlen($string1); $i1++) {
            $p2[0] = $p1[0] + $deletion_cost;

            for ($i2 = 0; $i2 < mb_strlen($string2); $i2++) {
                $c0 = $p1[$i2] + ((mb_substr($string1, $i1, 1) == mb_substr($string2, $i2, 1)) ? 0 : $replacement_cost);
                $c1 = $p1[$i2 + 1] + $deletion_cost;
                if ($c1 < $c0) {
                    $c0 = $c1;
                }
                $c2 = $p2[$i2] + $insertion_cost;
                if ($c2 < $c0) {
                    $c0 = $c2;
                }
                $p2[$i2 + 1] = $c0;
            }
            $tmp = $p1;
            $p1 = $p2;
            $p2 = $tmp;
        }

        return $p1[mb_strlen($string2)];
    }
}
