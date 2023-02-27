<?php

/*
 * (c) Zing Studios LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zing\Matcher\Tests\Preprocessor;

use PHPUnit\Framework\TestCase;
use Zing\Matcher\Preprocessor\MetaphonePreprocessor;

/**
 * @covers \Zing\Matcher\Preprocessor\MetaphonePreprocessor
 */
class MetaphonePreprocessorTest extends TestCase
{
    /**
     * Verifies that we get 0 for an exact match.
     * @return void
     */
    public function testMetaphonePreprocessor()
    {
        //Verify that we produce an exact match result for strings
        //with multibyte characters.
        $this->assertEquals('IM0WLRS', (new MetaphonePreprocessor())('I am the walrus'));
    }

    /**
     * Verifies that we get 0 for an exact match.
     * @return void
     */
    public function testMetaphonePreprocessorMaxPhonemes()
    {
        //Verify that we produce an exact match result for strings
        //with multibyte characters.
        $this->assertEquals('IM', (new MetaphonePreprocessor(2))('I am the walrus'));
    }
}
