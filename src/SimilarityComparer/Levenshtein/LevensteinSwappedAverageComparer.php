<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\SimilarityComparer\Levenshtein;


use Zing\Matcher\SimilarityComparer\SimilarityResultInterface;

final class LevensteinSwappedAverageComparer extends LevensteinComparer
{
    public function compare(string $needle, string $hay): SimilarityResultInterface
    {
        $result1 = parent::compare($needle, $hay);
        $result2 = parent::compare($hay, $needle);

        return new LevensteinSimilarityResult(
            (int)(($result1->getScore() + $result2->getScore()) / 2)
        );
    }
}
