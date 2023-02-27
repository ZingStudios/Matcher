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
 * @covers ::mb_similar_text
 * @covers \Zing\Matcher\Polyfill\MbSimilarText
 */
class MbSimilarTextTest extends TestCase
{
    /**
     * Verifies that we get the number of characters in the string and 100.0% for an exact match.
     * @return void
     */
    public function testExactMatch()
    {
        $word = $this->randomString(10);

        $percent1 = 0;
        $percent2 = 0;
        $percent3 = 0;

        //Verify that we produce the same result for an exact match
        //as the core levenshtein function
        $this->assertEquals(
            similar_text($word, $word, $percent1),
            mb_similar_text($word, $word, $percent2)
        );

        $this->assertEquals($percent1, $percent2);

        //Verify that we produce an exact match result for strings
        //with multibyte characters.
        $this->assertEquals(5, mb_similar_text('nôtre', 'nôtre', $percent3));
        $this->assertEquals(100.0, $percent3);
    }

    /**
     * Verifies that for some known weird cases we get the same results
     * as the built in similar_text function
     * @dataProvider words
     * @param $word1
     * @param $word2
     * @return void
     */
    public function testSimilar($word1, $word2)
    {
        $percent1 = 0;
        $percent2 = 0;

        //Verify that we produce the same result for an exact match
        //as the core levenshtein function
        $this->assertEquals(
            similar_text($word1, $word2, $percent1),
            mb_similar_text($word1, $word2, $percent2)
        );

        $this->assertEquals($percent1, $percent2);
    }

    /**
     * Verifies that we handle multi byte characters correctly for some cases
     * where the built in function fails
     * @return void
     */
    public function testUtf8CharacterCountsOnce()
    {
        $this->assertEquals(4, similar_text('notre', 'nitre')); //Prove that 4 our of 5 characters that are the same = 4
        $this->assertEquals(4, similar_text('notre', 'nôtre')); //Prove that 4 our of 5 characters that are the same = 4, sometimes multibyte is ok
        $this->assertEquals(4, mb_similar_text('notre', 'nôtre')); //Prove that 4 our of 5 characters that are the same = 4, sometimes multibyte is ok

        $this->assertEquals(3, similar_text('土橋勇樹', '東日刷株式')); //Prove that other multibyte characters produce incorrect results, this should be 0
        $this->assertEquals(0, mb_similar_text('土橋勇樹', '東日刷株式')); //Prove new function determines the correct similarity

        $this->assertEquals(1, mb_similar_text('土刷橋勇樹', '東日刷株式')); //Prove new function determines the correct similarity
        $this->assertEquals(4, mb_similar_text('株土橋勇樹', '土橋勇樹日')); //Prove new function determines the correct similarity
    }

    /**
     * Verifies that if the first string is empty, we get the same
     * result as the core function
     * @return void
     */
    public function testEmptyString1()
    {
        $string2 = $this->randomString(rand(5, 10));

        $percent1 = 0;
        $percent2 = 0;

        $this->assertEquals(
            similar_text('', $string2, $percent1),
            mb_similar_text('', $string2, $percent2)
        );

        $this->assertEquals($percent1, $percent2);
    }

    /**
     * Verifies that if the second string is empty, we get the same
     * result as the core levenshtein function
     * @return void
     */
    public function testEmptyString2()
    {
        $string1 = $this->randomString(rand(5, 10));

        $percent1 = 0;
        $percent2 = 0;

        $this->assertEquals(
            similar_text($string1, '', $percent1),
            mb_similar_text($string1, '', $percent2)
        );

        $this->assertEquals($percent1, $percent2);
    }


    /**
     * Verifies that if the second string is empty, we get the same
     * result as the core levenshtein function
     * @return void
     */
    public function testEmptyStrings()
    {
        $percent1 = 0;
        $percent2 = 0;

        $this->assertEquals(
            similar_text('', '', $percent1),
            mb_similar_text('', '', $percent2)
        );

        $this->assertEquals($percent1, $percent2);
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

    private function words()
    {
        return
        [
            ['PHP IS GREAT', 'WITH MYSQL'],
            ['WITH MYSQL', 'PHP IS GREAT'],
            ['test', 'wert'],
            ['wert', 'test'],
        ];
    }
}
