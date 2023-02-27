<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\SimilarityComparer\Levenshtein;


use Zing\Matcher\Exception\InvalidArgumentException;
use Zing\Matcher\Exception\LengthException;
use Zing\Matcher\SimilarityComparer\AbstractComparer;
use Zing\Matcher\SimilarityComparer\SimilarityResultInterface;

class LevensteinComparer extends AbstractComparer
{
    private const PHP_7_CHARACTER_LIMIT = 255;

    private int $insertionCost;
    private int $replacementCost;
    private int $deletionCost;

    public function __construct(
        int $options = 0,
        int $insertionCost = 1,
        int $replacementCost = 1,
        int $deletionCost = 1
    ) {
        parent::__construct($options);
        $this->insertionCost = $insertionCost;
        $this->replacementCost = $replacementCost;
        $this->deletionCost = $deletionCost;
    }

    public function compare(string $needle, string $hay): SimilarityResultInterface
    {
        $this->assertStrLen($needle);
        $this->assertStrLen($hay);
        return new LevensteinSimilarityResult(
            $this->levenshtein($needle, $hay)
        );
    }

    /**
     * Prior to PHP 8.0 the levenshtein function
     * would return -1 if either string argument
     * was greater than 255 characters, we cannot
     * determine the best match in this case,
     * so we will throw if this is encountered
     *
     * @param string $string
     * @return void
     * @throws LengthException
     */
    private function assertStrLen(string $string): void
    {
        if (version_compare(PHP_VERSION, '8.0.0') < 0) {
            if (mb_strlen($string) > self::PHP_7_CHARACTER_LIMIT) {
                throw new LengthException(
                    sprintf(
                        "The string '%s' is greater than the character limit of %d.",
                        $string,
                        self::PHP_7_CHARACTER_LIMIT
                    )
                );
            }
        }
    }

    /**
     * Calls the appropriate levenshtein function based
     * on the MULTIBYTE flag.
     *
     * @param string $string1
     * @param string $string2
     * @return int
     */
    private function levenshtein(
        string $string1,
        string $string2
    ): int {
        if ($this->isOptionEnabled(self::MULTIBYTE)) {
            return mb_levenshtein(
                $string1,
                $string2,
                $this->insertionCost,
                $this->replacementCost,
                $this->deletionCost
            );
        } else {
            return levenshtein(
                $string1,
                $string2,
                $this->insertionCost,
                $this->replacementCost,
                $this->deletionCost
            );
        }
    }

    public function compareResults(SimilarityResultInterface $result1, SimilarityResultInterface $result2): int
    {
        $this->assertSimilarityResultType($result1);
        $this->assertSimilarityResultType($result2);

        return -1 * ($result1->getScore() <=> $result2->getScore());
    }

    private function assertSimilarityResultType(SimilarityResultInterface $result)
    {
        if (!$result instanceof LevensteinSimilarityResult) {
            throw new InvalidArgumentException(
                'Expected %s but received object of type %s',
                LevensteinSimilarityResult::class,
                $result::class
            );
        }
    }
}
