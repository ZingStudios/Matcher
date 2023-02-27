<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Zing\Matcher\Polyfill\MbLevenshtein;
use Zing\Matcher\Polyfill\MbSimilarText;

/**
 * This file serves to load mb_levenshtein as a polyfill.
 * In the future, if the mbstring extension ever introduces
 * this method, as long as it has the same signature,
 * the matcher will continue to work and will use that
 * implementation instead of this one.
 */

if (!function_exists('mb_levenshtein') && extension_loaded('mbstring')) {
    /**
     * Calculate Levenshtein distance between two strings
     * @link https://php.net/manual/en/function.levenshtein.php
     * Note: In its simplest form the function will take only the two strings
     * as parameter and will calculate just the number of insert, replace and
     * delete operations needed to transform str1 into str2.
     * Note: A second variant will take three additional parameters that define
     * the cost of insert, replace and delete operations. This is more general
     * and adaptive than variant one, but not as efficient.
     *
     * This is a port of the Levenshtein distance algorithm
     * from php core, with all string functions replaced with multibyte versions.
     *
     * @link https://github.com/php/php-src/blob/php-8.0.0/ext/standard/levenshtein.c
     * @see levenshtein()
     *
     * @param string $string1 <p>
     * One of the strings being evaluated for Levenshtein distance.
     * </p>
     * @param string $string2 <p>
     * One of the strings being evaluated for Levenshtein distance.
     * </p>
     * @param int $insertion_cost [optional] <p>
     * Defines the cost of insertion.
     * </p>
     * @param int $replacement_cost [optional] <p>
     * Defines the cost of replacement.
     * </p>
     * @param int $deletion_cost [optional] <p>
     * Defines the cost of deletion.
     * </p>
     * @return int This function returns the Levenshtein-Distance between the
     * two argument strings.
     */
    function mb_levenshtein(
        string $string1,
        string $string2,
        int $insertion_cost = 1,
        int $replacement_cost = 1,
        int $deletion_cost = 1
    ): int {
        return MbLevenshtein::mb_levenshtein($string1, $string2, $insertion_cost, $replacement_cost, $deletion_cost);
    }
}

if (!function_exists('mb_similar_text') && extension_loaded('mbstring')) {
    /**
     * Calculate the similarity between two strings
     * @link https://php.net/manual/en/function.similar-text.php
     *
     * This is a port of the Levenshtein distance algorithm
     * from php core, with all string functions replaced with multibyte versions.
     *
     * @link https://github.com/php/php-src/blob/master/ext/standard/string.c#L3251
     *
     * @param string $string1 <p>
     * The first string.
     * </p>
     * @param string $string2 <p>
     * The second string.
     * </p>
     * @param float &$percent [optional] <p>
     * By passing a reference as third argument,
     * similar_text will calculate the similarity in
     * percent for you.
     * </p>
     * @return int the number of matching chars in both strings.
     */
    function mb_similar_text(string $string1, string $string2, float &$percent = null): int {
        $compute_percentage = func_num_args() >= 3;

        if ($compute_percentage) {
            return MbSimilarText::mb_similar_text($string1, $string2, $percent);
        } else {
            return MbSimilarText::mb_similar_text($string1, $string2);
        }
    }
}
