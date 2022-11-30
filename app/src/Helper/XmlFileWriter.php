<?php declare(strict_types = 1);

namespace Life\Helper;

use DOMDocument;
use Life\Exception\OutputWritingException;
use SimpleXMLElement;

class XmlFileWriter
{
    private const XML_VERSION = '1.0';
    private const OUTPUT_TEMPLATE = '/output-template.xml';

    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param int $worldSize
     * @param int $speciesCount
     * @param int[][]|null[][] $cells
     * @return void
     * @throws OutputWritingException
     */
    public function saveWorld(int $worldSize, int $speciesCount, array $cells): void
    {
        $life = simplexml_load_string((string) file_get_contents(__DIR__ . self::OUTPUT_TEMPLATE));

        if ($life === false) {
            throw new OutputWritingException('Writing XML file failed');
        }

        $life->world->cells = $worldSize;
        $life->world->species = $speciesCount;
        for ($y = 0; $y < $worldSize; $y++) {
            for ($x = 0; $x < $worldSize; $x++) {
                $cell = $cells[$y][$x];
                if ($cell !== null) {
                    /** @var SimpleXMLElement $organism */
                    $organism = $life->organisms->addChild('organism');
                    $organism->addChild('x_pos', (string) $x);
                    $organism->addChild('y_pos', (string) $y);
                    $organism->addChild('species', (string) $cell);
                }
            }
        }
        $this->saveXml($life);
    }

    /**
     * @throws OutputWritingException
     */
    private function saveXml(SimpleXMLElement $life): void
    {
        $dom = new DOMDocument(self::XML_VERSION);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML((string) $life->asXML());
        $result = file_put_contents($this->filePath, $dom->saveXML());
        if ($result === false) {
            throw new OutputWritingException('Writing XML file failed');
        }
    }
}
