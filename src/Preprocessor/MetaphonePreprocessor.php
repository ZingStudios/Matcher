<?php
/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Preprocessor;

class MetaphonePreprocessor implements PreprocessorInterface
{
    private int $maxPhonemes;

    public function __construct(int $maxPhonemes = 0)
    {
        $this->maxPhonemes = $maxPhonemes;
    }

    /**
     * @param string $string
     * @return string
     */
    public function __invoke(string $string): string
    {
        return metaphone($string, $this->maxPhonemes);
    }
}
