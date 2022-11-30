<?php declare(strict_types=1);

namespace Life\Facade;

use Life\Component\Game\GameFactory;
use Life\Exception\InvalidInputException;

class GameFacade
{
    private GameFactory $gameFactory;

    public function __construct(GameFactory $gameFactory)
    {
        $this->gameFactory = $gameFactory;
    }

    /**
     * @throws InvalidInputException
     */
    public function run(string $inputFile, string $outputFile): void
    {
        $game = $this->gameFactory->create($inputFile, $outputFile);
        $game->run();
    }
}
