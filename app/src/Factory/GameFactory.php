<?php declare(strict_types=1);

namespace Life\Factory;

use Life\Exception\InvalidInputException;
use Life\Game;
use Life\Helper\XmlFileReader;
use Life\Helper\XmlFileWriter;
use Life\Service\GameService;

class GameFactory
{

    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @throws InvalidInputException
     */
    public function create(string $inputFile, string $outputFile): Game
    {
        $input = new XmlFileReader($inputFile);
        $output = new XmlFileWriter($outputFile);

        [$size, $species, $cells, $iterationsCount] = $input->loadFile();

        return new Game($this->gameService, $size, $species, $cells, $iterationsCount, $output);
    }
}
