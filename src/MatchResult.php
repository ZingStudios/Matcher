<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher;

use Zing\Matcher\SimilarityComparer\SimilarityResultInterface;

final class MatchResult implements MatchResultInterface
{
    private string $string;
    private SimilarityResultInterface $similarityResult;

    public function __construct(
        string $string,
        SimilarityResultInterface $similarityResult
    ) {
        $this->string = $string;
        $this->similarityResult = $similarityResult;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function getSimilarityResult(): SimilarityResultInterface
    {
        return $this->similarityResult;
    }
}
