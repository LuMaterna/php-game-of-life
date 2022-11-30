<?php declare(strict_types=1);

namespace Life\Component\Game;

use Life\Exception\InvalidInputException;
use Life\Helper\XmlFileReader;
use Life\Helper\XmlFileWriter;
use Life\Service\GameService;

class GameFactory
{

    private GameService $gameService;

    private XmlFileReader $xmlFileReader;

    public function __construct(
        GameService $gameService,
        XmlFileReader $xmlFileReader
    ) {
        $this->gameService = $gameService;
        $this->xmlFileReader = $xmlFileReader;
    }

    /**
     * @throws InvalidInputException
     */
    public function create(string $inputFile, string $outputFile): Game
    {
        $output = new XmlFileWriter($outputFile);

        /** @var int[][]|null[][] $cells */
        [$size, $species, $cells, $iterationsCount] = $this->readDataFromInputFile($inputFile);

        return new Game($this->gameService, (int) $size, (int) $species, $cells, (int) $iterationsCount, $output);
    }

    /**
     * @param string $inputFile
     * @return array<int|array<int[]|null[]>>
     * @throws InvalidInputException
     */
    private function readDataFromInputFile(string $inputFile): array
    {
        return $this->xmlFileReader->loadFile($inputFile);
    }
}
