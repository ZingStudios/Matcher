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
final class Suggester implements SuggesterInterface
{
    private SimilarityComparerInterface $comparer;
    /**
     * @var PreprocessorInterface[]
     */
    private array $preprocessors;

    private ?TieBreakerInterface $tieBreaker;


    private string $originalNeedle;

    /**
     * @param SimilarityComparerInterface $comparer
     * @param PreprocessorInterface[] $preprocessors
     * @param TieBreakerInterface|null $tieBreaker
     * @throws InvalidArgumentException
     */
    public function __construct(
        SimilarityComparerInterface $comparer,
        array $preprocessors = [],
        ?TieBreakerInterface $tieBreaker = null,
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
    }

    /**
     * Returns the word from the haystack that is the closest match
     * to the needle.
     *
     * @param string $needle
     * @param array $haystack
     * @return MatchResultInterface[]
     * @throws LengthException
     */
    public function suggest(
        string $needle,
        array $haystack
    ): array {
        if (count($haystack) === 0) {
            throw new LengthException('Argument $haystack cannot be an empty array.');
        }

        $this->originalNeedle = $needle;

        $needle = $this->preprocess($needle);

        /**
         * @var $matches MatchResultInterface[]
         */
        $results = [];

        foreach ($haystack as $hay) {
            $results[] = new MatchResult(
                $hay,
                $this->comparer->compare(
                    $needle,
                    $this->preprocess($hay)
                )
            );
        }

        usort($results, [$this, 'sortResults']);

        return array_reverse($results);
    }

    private function sortResults(MatchResultInterface $result1, MatchResultInterface $result2): int
    {
        $result = $this->comparer->compareResults($result1->getSimilarityResult(), $result2->getSimilarityResult());

        if ($result === 0) {
           $result = ($this->tieBreaker)($this->originalNeedle, [$result1, $result2]);
           if ($result === $result1) {
                return 1;
           } else {
               return -1;
           }
        } else {
            return $result;
        }
    }

    private function preprocess(string $string): string
    {
        foreach ($this->preprocessors as $preprocessor) {
            $string = ($preprocessor)($string);
        }
        return $string;
    }
}
