<?php declare(strict_types = 1);

namespace Tests\Life;

use DI\Container;
use DOMDocument;
use Life\RunGameCommand;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use function array_map;
use function file_exists;
use function range;
use function unlink;

class IntegrationTest extends TestCase
{
    private const OUTPUT_FILE = 'output.xml';


    /**
     * @dataProvider getInputAndExpectedOutputFiles
     *
     * @param string $inputFile
     * @param string $expectedOutputFile
     */
    public function testGame(string $inputFile, string $expectedOutputFile): void
    {
        $container = new Container();
        $command = $container->get(RunGameCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                '--input' => $inputFile,
                '--output' => self::OUTPUT_FILE,
            ]
        );

        $output = $this->loadXmlForComparison(self::OUTPUT_FILE);

        Assert::assertXmlStringEqualsXmlFile(
            $expectedOutputFile,
            $output,
            'Expected XML and output XML should be same'
        );
    }


    public function getInputAndExpectedOutputFiles(): array
    {
        $scenarios = range(1, 9);

        return array_map(
            static function (int $scenario) {
                return [
                    __DIR__ . '/__fixtures__/scenario-' . $scenario . '/input.xml',
                    __DIR__ . '/__fixtures__/scenario-' . $scenario . '/output.xml',
                ];
            },
            $scenarios
        );
    }


    private function loadXmlForComparison(string $filePath): string
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->load($filePath);

        return $dom->saveXML();
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $outputFilePath = self::OUTPUT_FILE;
        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }
    }
}
