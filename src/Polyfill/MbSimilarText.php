<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Polyfill;

class MbSimilarText
{
    public static function mb_similar_text(
        string $t1,
        string $t2,
        float &$percent = null
    ): int {
        $compute_percentage = func_num_args() >= 3;

        if (mb_strlen($t1) + mb_strlen($t2) == 0) {
            if ($compute_percentage) {
                $percent = 0.0;
            }

            return 0;
        }

        $sim = self::php_similar_char($t1, mb_strlen($t1), $t2, mb_strlen($t2));

        if ($compute_percentage) {
            $percent = $sim * 200.0 / (mb_strlen($t1) + mb_strlen($t2));
        }

        return $sim;
    }

    private static function php_similar_char(string $txt1, int $len1, string $txt2, int $len2): int
    {
        $sum = 0;
        $pos1 = 0;
        $pos2 = 0;
        $max = 0;
        $count = 0;

        self::php_similar_str($txt1, $len1, $txt2, $len2, $pos1, $pos2, $max, $count);
        if (($sum = $max)) {
            if ($pos1 && $pos2 && $count > 1) {
                $sum += self::php_similar_char(mb_substr($txt1, 0, $pos1), $pos1,
                    mb_substr($txt2, 0, $pos2), $pos2);
            }
            if (($pos1 + $max < $len1) && ($pos2 + $max < $len2)) {
                $sum += self::php_similar_char(mb_substr($txt1,  $pos1 + $max), $len1 - $pos1 - $max,
                    mb_substr($txt2, $pos2 + $max), $len2 - $pos2 - $max);
            }
        }

        return $sum;
    }

    private static function php_similar_str(string &$txt1, int $len1, string &$txt2, int $len2, int &$pos1, int &$pos2, int &$max, int &$count): void
    {
        //const char *$p, *$q;
        //const char *$end1 = (char *) txt1 + len1;
        //const char *$end2 = (char *) txt2 + len2;

        $end1 = $len1;
        $end2 = $len2;

        $l = 0;

        $max = 0;
        $count = 0;
        for ($p = 0; $p < $end1; $p++) {
            for ($q = 0; $q < $end2; $q++) {
                for ($l = 0; ($p + $l < $end1) && ($q + $l < $end2) && (mb_substr($txt1, $p + $l,1) == mb_substr($txt2, $q + $l, 1)); $l++);
                if ($l > $max) {
                    $max = $l;
                    $count += 1;
                    $pos1 = $p;// - $txt1;
                    $pos2 = $q;// - $txt2;
                }
            }
        }
    }
}
