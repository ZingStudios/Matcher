<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Tests;

use PHPUnit\Framework\TestCase;
use Zing\Matcher\Exception\LengthException;
use Zing\Matcher\Exception\TieException;
use Zing\Matcher\Matcher;
use Zing\Matcher\MatcherInterface;
use Zing\Matcher\MatchResult;
use Zing\Matcher\Preprocessor\StrToLowerPreprocessor;
use Zing\Matcher\Result;
use Zing\Matcher\SimilarityComparer\Levenshtein\LevensteinComparer;
use Zing\Matcher\SimilarityComparer\Levenshtein\LevensteinSimilarityResult;
use Zing\Matcher\SimilarityComparer\SimilarityComparerInterface;
use Zing\Matcher\TieBreaker\LastMatch;
use Zing\Matcher\TieBreaker\ThrowException;

/**
 * @covers \Zing\Matcher\Matcher
 */
class MatcherTest extends TestCase
{
    /**
     * Verifies that the default case-sensitive matching,
     * will return a result that matches the case exactly
     * as the best match.
     */
    public function testMatchCaseSensitive()
    {
        $haystack = [
            'apple',
            'pineapple',
            'banana',
            'orange',
            'radish',
            'carrOT',
            'carrot',
            'Carrot',
            'CARROT',
            'pea',
            'bean',
            'potato'
        ];

        $matcher = new Matcher(new LevensteinComparer());
        $result = $matcher->match('Carrot', $haystack,  );

        $this->assertEquals('Carrot', $result->getString());
        $this->assertEquals(0, $result->getSimilarityResult()->getScore());
    }

    /**
     * Verifies that the default case-sensitive matching,
     * will return a result that matches the case exactly
     * as the best match when using the multibyte implementation.
     */
    public function testMatchCaseSensitiveMB()
    {
        $haystack = [
            'apple',
            'pineapple',
            'banana',
            'orange',
            'radish',
            'carrÖT',
            'carröt',
            'Carröt',
            'CARROT',
            'pea',
            'bean',
            'potato'
        ];

        $matcher = new Matcher(
            new LevensteinComparer(SimilarityComparerInterface::MULTIBYTE)
        );

        $result = $matcher->match('Carröt', $haystack,  );

        $this->assertEquals('Carröt', $result->getString());
        $this->assertEquals(0, $result->getSimilarityResult()->getScore());
    }

    /**
     * Verifies that the StrToLowerPreprocessor will make the matcher
     * ignore case when comparing and 2 strings
     */
    public function testMatchCaseInsensitive()
    {
        $haystack = [
            'apple',
            'pineapple',
            'banana',
            'orange',
            'radish',
            'Karrot', //Has 1 for replacement
            'Carrot', //Has 1 for replacement, but should have 0 if we are ignoring case
            'pea',
            'bean',
            'potato'
        ];

        $matcher = new Matcher(
            new LevensteinComparer(),
            [new StrToLowerPreprocessor()],
            null,
        );

        $result = $matcher->match('carroT', $haystack);

        $this->assertEquals('Carrot', $result->getString());
        $this->assertEquals(0, $result->getSimilarityResult()->getScore()); //considered exact match!
    }

    /**
     * Verifies that the StrToLowerPreprocessor will make the matcher
     * ignore case when comparing and 2 strings when using the multibyte implementation.
     */
    public function testMatchCaseInsensitiveMB()
    {
        $haystack = [
            'apple',
            'pineapple',
            'banana',
            'orange',
            'radish',
            'Karröt', //Has 1 for replacement
            'Carröt', //Has 1 for replacement, but should have 0 if we are ignoring case
            'pea',
            'bean',
            'potato'
        ];

        $matcher = new Matcher(
            new LevensteinComparer(SimilarityComparerInterface::MULTIBYTE),
            [new StrToLowerPreprocessor()],
            null,
        );

        $result = $matcher->match(
            'carrÖT',
            $haystack
        );

        $this->assertEquals('Carröt', $result->getString());
        $this->assertEquals(0, $result->getSimilarityResult()->getScore()); //considered exact match!
    }

    /**
     * Verify that we get the best match from the haystack
     * @return void
     * @throws LengthException
     */
    public function testMatchNotExact()
    {
        $haystack = [
            'apple',
            'pineapple',
            'banana',
            'orange',
            'radish',
            'carrot',
            'pea',
            'bean',
            'potato'
        ];

        $matcher = new Matcher(
            new LevensteinComparer(),
            [new StrToLowerPreprocessor()],
            null
        );

        $result = $matcher->match('Carrrot', $haystack);

        $this->assertEquals('carrot', $result->getString());
        $this->assertEquals(1, (string)$result->getSimilarityResult()->getScore());
    }

    /**
     * Verify that we get the best match from the haystack when using the multibyte implementation.
     */
    public function testMatchNotExactMB()
    {
        $haystack = [
            'apple',
            'pineapple',
            'banana',
            'orange',
            'radish',
            'carröt',
            'pea',
            'bean',
            'potato'
        ];

        $matcher = new Matcher(
            new LevensteinComparer(SimilarityComparerInterface::MULTIBYTE),
            [new StrToLowerPreprocessor()],
            null,
        );

        $result = $matcher->match(
            'Carrot',
            $haystack,
        );

        $this->assertEquals('carröt', $result->getString());
        $this->assertEquals(1, (string)$result->getSimilarityResult()->getScore());
    }

    /**
     * Verify that the default behavior in the event of a tie for best match
     * is to return the first element that tied
     * @return void
     */
    public function testTieBreakerDefault()
    {
        $haystack = [
            'fox',
            'cat',
            'fat',
            'eel',
            'rat',
            'owl',
        ];

        $matcher = new Matcher(new LevensteinComparer());
        $result = $matcher->match('bat', $haystack);

        $this->assertEquals('cat', $result->getString());
        $this->assertEquals(1, (string)$result->getSimilarityResult()->getScore());
    }

    /**
     * Verify that a tiebreaker that is passed in will be used instead of the default.
     */
    public function testTieBreakerLast()
    {
        $haystack = [
            'fox',
            'cat',
            'fat',
            'eel',
            'rat',
            'owl',
        ];

        $matcher = new Matcher(
            new LevensteinComparer(),
            [],
            new LastMatch()
        );

        $result = $matcher->match('bat', $haystack);

        $this->assertEquals('rat', $result->getString());
        $this->assertEquals(1, (string)$result->getSimilarityResult()->getScore());
    }

    /**
     * Verifies that a LengthException is throw if an empty array is passed
     * as the $haystack argument
     */
    public function testWillThrowOnEmptyHaystack()
    {
        $this->expectException(LengthException::class);
        $this->expectDeprecationMessage('Argument $haystack cannot be an empty array.');

        $matcher = new Matcher(
            new LevensteinComparer()
        );

        $matcher->match('test', []);
    }

    /**
     * Verify that the ALLOW_EXACT_MATCH_TIES will not return a match immediately
     * and will instaed pass all exact match ties to the tie breaker.
     */
    public function testAllowExactMatchTies()
    {
        $haystack = [
            'fox',
            'cat',
            'BAT',
            'bat',
            'rat',
            'owl',
        ];

        try {
            $matcher = new Matcher(
                new LevensteinComparer(),
                [new StrToLowerPreprocessor()],
                new ThrowException(),
                MatcherInterface::ALLOW_EXACT_MATCH_TIES
            );

            $matcher->match(
                'Bat',
                $haystack,
            );

            $this->fail(
                sprintf(
                    'Expected exception %s was not thrown.',
                    TieException::class
                )
            );
        } catch (TieException $e) {
            $this->assertEquals(
                [
                    new MatchResult('BAT', new LevensteinSimilarityResult( 0)),
                    new MatchResult('bat', new LevensteinSimilarityResult( 0)),
                ],
                $e->getTiedResults()
            );
        }
    }
}
