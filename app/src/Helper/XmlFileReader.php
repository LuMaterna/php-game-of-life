<?php declare(strict_types=1);

namespace Life\Helper;

use Life\Exception\InvalidInputException;
use SimpleXMLElement;

class XmlFileReader
{
    private string $filePath;

    private XmlInputValidator $xmlInputValidator;


    public function __construct(XmlInputValidator $xmlInputValidator)
    {
        $this->xmlInputValidator = $xmlInputValidator;
    }

    /**
     * @param string $filePath
     * @return array<int|array<int[]|null[]>>
     * @throws InvalidInputException
     */
    public function loadFile(string $filePath): array
    {
        $this->filePath = $filePath;

        $life = $this->loadXmlFile();
        $this->xmlInputValidator->validate($life);

        $iterationsCount = (int) $life->world->iterations;
        $worldSize = (int) $life->world->cells;
        $speciesCount = (int) $life->world->species;

        $cells = $this->readCells($life, $worldSize);

        return [$worldSize, $speciesCount, $cells, $iterationsCount];
    }

    /**
     * @throws InvalidInputException
     */
    private function loadXmlFile(): SimpleXMLElement
    {
        if (!file_exists($this->filePath)) {
            throw new InvalidInputException('Unable to read nonexistent file');
        }
        try {
            libxml_use_internal_errors(true);
            $life = simplexml_load_string((string) file_get_contents($this->filePath));
            $errors = libxml_get_errors();
            libxml_clear_errors();
            if (count($errors) > 0) {
                throw new InvalidInputException('Cannot read XML file');
            }
        } catch (\Exception $e) {
            throw new InvalidInputException('Cannot read XML file');
        }

        if ($life === false) {
            throw new InvalidInputException('Cannot read XML file');
        }

        return $life;
    }

    /**
     * @param SimpleXMLElement $life
     * @param int $worldSize
     * @return int[][]|null[][]
     */
    private function readCells(SimpleXMLElement $life, int $worldSize): array
    {
        $cells = [];
        foreach ($life->organisms->organism as $organism) {
            $x = (int) $organism->x_pos;
            $y = (int) $organism->y_pos;
            $species = (int) $organism->species;

            $finalSpecies = $species;
            if (isset($cells[$y][$x])) {
                $existingCell = $cells[$y][$x];
                $availableSpecies = [$existingCell, $species];
                $finalSpecies = $availableSpecies[array_rand($availableSpecies)];
            }
            $cells[$y][$x] = $finalSpecies;
        }

        for ($y = 0; $y < $worldSize; $y++) {
            $cells[$y] = $cells[$y] ?? [];
            for ($x = 0; $x < $worldSize; $x++) {
                if (!isset($cells[$y][$x])) {
                    $cells[$y][$x] = null;
                }
            }
        }

        return $cells;
    }
}
