<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher;

use Zing\Matcher\Exception\InvalidArgumentException;
use Zing\Matcher\Exception\LengthException;
use Zing\Matcher\Preprocessor\PreprocessorInterface;
use Zing\Matcher\SimilarityComparer\SimilarityComparerInterface;
use Zing\Matcher\TieBreaker\FirstMatch;
use Zing\Matcher\TieBreaker\TieBreakerInterface;

/**
 * This is a wrapper around the levenshtein function to allow finding
 * the closest match in a string array.
 *
 * This also has support for case-insensitive matching,
 * multibyte character support, and custom tie-breaking.
 *
 * @see https://www.php.net/manual/en/function.levenshtein.php#example-4827
 */
final class Matcher implements MatcherInterface
{
    private int $options = 0;
    private SimilarityComparerInterface $comparer;
    /**
     * @var PreprocessorInterface[]
     */
    private array $preprocessors;

    private ?TieBreakerInterface $tieBreaker;

    /**
     * @param SimilarityComparerInterface $comparer
     * @param PreprocessorInterface[] $preprocessors
     * @param TieBreakerInterface|null $tieBreaker [optional]
     * Determines what to do in case of a tie.
     * FirstMatch is used if none is provided.
     * @param int $options [optional]
     * ALLOW_EXACT_MATCH_TIES - all exact matches to tie and be sent to the
     * tiebreaker, instead of being returned immediately
     * @throws InvalidArgumentException
     */
    public function __construct(
        SimilarityComparerInterface $comparer,
        array $preprocessors = [],
        ?TieBreakerInterface $tieBreaker = null,
        int $options = 0
    ) {
        $this->comparer = $comparer;

        foreach ($preprocessors as $preprocessor) {
            if (!$preprocessor instanceof PreprocessorInterface) {
                throw new InvalidArgumentException(
                    'All preprocessors must implement ' . PreprocessorInterface::class
                );
            }
        }

        $this->preprocessors = $preprocessors;

        if (is_null($tieBreaker)) {
            $tieBreaker = new FirstMatch();
        }
        $this->tieBreaker = $tieBreaker;

        $this->options = $options;

    }

    /**
     * Returns the word from the haystack that is the closest match
     * to the needle.
     *
     * @param string $needle
     * @param array $haystack
     * @return MatchResultInterface
     * @throws LengthException
     */
    public function match(
        string $needle,
        array $haystack
    ): MatchResultInterface {
        if (count($haystack) === 0) {
            throw new LengthException('Argument $haystack cannot be an empty array.');
        }
        $originalNeedle = $needle;

        $needle = $this->preprocess($needle);

        $bestMatch = null;

        /**
         * @var $matches MatchResultInterface[]
         */
        $matches = [];

        foreach ($haystack as $hay) {
            $result = new MatchResult(
                $hay,
                $this->comparer->compare(
                    $needle,
                    $this->preprocess($hay)
                )
            );

            if ($result->getSimilarityResult()->isExactMatch()
                && !$this->isOptionEnabled(self::ALLOW_EXACT_MATCH_TIES)
            ) {
                return $result;
            }

            if (is_null($bestMatch)
                || $this->comparer->compareResults(
                    $result->getSimilarityResult(),
                    $bestMatch->getSimilarityResult()
                ) === 1
            ) {
                // new winner, clear the matches and record the new winner
                $matches = [$result];
                $bestMatch = $result;
            } elseif ($this->comparer->compareResults(
                    $result->getSimilarityResult(),
                    $bestMatch->getSimilarityResult()
                ) === 0
            ) {
                // we have a tie, add to the matches
                $matches[] = $result;
            }
        }

        if (count($matches) === 1) {
            return $matches[0];
        } else {
            return ($this->tieBreaker)($originalNeedle, $matches);
        }
    }

    private function isOptionEnabled(int $option): bool
    {
        return $this->options & $option;
    }

    private function preprocess(string $string): string
    {
        foreach ($this->preprocessors as $preprocessor) {
            $string = ($preprocessor)($string);
        }
        return $string;
    }
}
