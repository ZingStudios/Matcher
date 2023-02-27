<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Tests\Polyfill;

use PHPUnit\Framework\TestCase;

/**
 * @covers ::mb_levenshtein
 * @covers \Zing\Matcher\Polyfill\MbLevenshtein
 */
class MbLevenshteinTest extends TestCase
{
    /**
     * Verifies that we get 0 for an exact match.
     * @return void
     */
    public function testExactMatch()
    {
        $word = $this->randomString(10);

        //Verify that we produce the same result for an exact match
        //as the core levenshtein function
        $this->assertEquals(
            levenshtein($word, $word),
            mb_levenshtein($word, $word)
        );

        //Verify that we produce an exact match result for strings
        //with multibyte characters.
        $this->assertEquals(0, mb_levenshtein('nôtre', 'nôtre'));
    }

    /**
     * Verifies that we get 1 for the replacement of a UTF8 character.
     * Calling the core levenshtein function with these same arguments
     * will return 2, due to it checking every byte and not every character.
     * However, it should be returning 1 for a single replacement
     * @return void
     */
    public function testUtf8CharacterCountsOnce()
    {
        $this->assertEquals(1, levenshtein('notre', 'nitre')); //Prove single replacement cost is 1
        $this->assertEquals(2, levenshtein('notre', 'nôtre')); //Prove single replacement of multibyte is costing 2
        $this->assertEquals(1, mb_levenshtein('notre', 'nôtre')); //Prove new function has correct cost for multibyte replacement
    }

    /**
     * Verifies that if the first string is empty, we get the same
     * result as the core levenshtein function
     * @return void
     */
    public function testEmptyString1()
    {
        $string2 = $this->randomString(rand(5, 10));

        $this->assertEquals(
            levenshtein('', $string2),
            mb_levenshtein('', $string2)
        );
    }

    /**
     * Verifies that if the second string is empty, we get the same
     * result as the core levenshtein function
     * @return void
     */
    public function testEmptyString2()
    {
        $string1 = $this->randomString(rand(5, 10));

        $this->assertEquals(
            levenshtein($string1, ''),
            mb_levenshtein($string1, '')
        );
    }

    /**
     * Verifies that the insertion cost is calculated the same
     * as the core levenshtein function
     * @return void
     */
    public function testInsertionCost()
    {
        $string1 = 'a';

        $string2 = 'aaaa';

        $this->assertEquals(
            levenshtein($string1, $string2, 3, 0, 0),
            mb_levenshtein($string1, $string2, 3, 0, 0)
        );
    }

    /**
     * Verifies that the replacement cost is calculated the same
     * as the core levenshtein function
     * @return void
     */
    public function testReplacementCost()
    {
        $string1 = $this->randomString(12);

        $string2 = $this->randomString(12);
        while($string1 === $string2) {
            $string2 = $this->randomString(12);
        }

        $this->assertEquals(
            levenshtein($string1, $string2, 0, 3, 0),
            mb_levenshtein($string1, $string2, 0, 3, 0)
        );
    }

    /**
     * Verifies that the deletion cost is calculated the same
     * as the core levenshtein function
     * @return void
     */
    public function testDeletionCost()
    {
        $string1 = 'aaaa';

        $string2 = 'a';

        $this->assertEquals(
            levenshtein($string1, $string2, 0, 0, 3),
            mb_levenshtein($string1, $string2, 0, 0, 3)
        );
    }

    private function randomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }
}
