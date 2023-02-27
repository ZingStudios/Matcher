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
use Zing\Matcher\Preprocessor\StrToLowerPreprocessor;
use Zing\Matcher\Preprocessor\MetaphonePreprocessor;
use Zing\Matcher\SimilarityComparer\SimilarText\SimilarTextSwappedAverageComparer;
use Zing\Matcher\TieBreaker\ThrowException;

/**
 * @coversNothing
 */
class ProofOfConceptTest extends TestCase
{
    private const W2_FIELDS = [
        'employersnameaddressandzipcode',
        'wagestipsothercompensation',
        'medicarewagesandtips',
        'localwagestipsetc',
        'localincometax',
        'localityname',
        'bemployeridentificationnumberein',
        'aemployeessocialsecuritynumber'
    ];

    /**
     * @dataProvider w2TestData
     */
    public function testMatching(string $expected_field, string $input_field, array $dictionary)
    {
        try {
            $matcher = new Matcher(
                new SimilarTextSwappedAverageComparer(),
                [
                    new StrToLowerPreprocessor(),
                    //new MetaphonePreprocessor()
                ],
                new ThrowException()
            );

            $result = $matcher->match(
                $input_field,
                $dictionary
            );
            /*$matcher2 = new Matcher(
                new SimilarTextComparer(SimilarityComparerInterface::MULTIBYTE)
            );

            $result2 = $matcher2->match(
                $input_field,
                $dictionary
            );

            $this->assertEquals($result, $result2);*/
        } catch (TieException $e) {
            $this->fail(
                sprintf(
                    "%s expected %s   :   TIED  %s" . PHP_EOL,
                    $input_field,
                    $expected_field,
                    implode(
                        ', ',
                        array_map(function ($tie) {
                            return $tie->getString();
                        },
                            $e->getTiedResults()
                        )
                    )
                )
            );
        } catch (LengthException $e) {
            throw $e;
        }

        $this->assertEquals(
            $expected_field,
            $result->getString(),
            sprintf(
                "%s = %s but expected %s",
                $input_field,
                $result->getString(),
                $expected_field
            )
        );

        /*$this->assertGreaterThanOrEqual(
            70,
            $result->getPercent(),
            sprintf(
                "%s expected %s   :   BELOW THRESHOLD  %f" . PHP_EOL,
                $input_field,
                $expected_field,
                $result->getPercent()
            )
        );*/

        echo(
        sprintf(
            "%s = %s   :   %d - %f" . PHP_EOL,
            $input_field,
            $result->getString(),
            $result->getSimilarityResult()->getScore(),
            $result->getSimilarityResult()->getPercent()
        )
        );
    }

    public function w2TestData()
    {
        for ($test = 0; $test < 1; ++$test) {
            $input_fields = [];

            //Pull 1 random input for each of the expected input fields
            foreach (self::W2_FIELDS as $expected_field) {
                $contents = file(__DIR__ . '/Fixtures/' . $expected_field . '.txt');
                $input_fields[$expected_field] = str_replace("\n", '', $contents[rand(0, count($contents) - 1)]);
            }

            $dictionary = self::W2_FIELDS;


            //Sort the dictionary so the longest are first
            usort($dictionary, function ($a, $b) {
                return (strlen($b) <=> strlen($a));
            });


            //Sort the input fields so the longest are first
            uasort($input_fields, function ($a, $b) {
                return (strlen($b) <=> strlen($a));
            });


            foreach ($input_fields as $expected_field => $input_field) {
                yield [$expected_field, $input_field, $dictionary];
                /*$dictionary = array_filter(
                    $dictionary,
                    function ($e) use ($expected_field) {
                        return ($e !== $expected_field);
                    }
                );*/
            }
        }
    }
}
