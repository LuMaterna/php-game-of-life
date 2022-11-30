<?php declare(strict_types = 1);

namespace Life\Component\Game;

use Life\Exception\OutputWritingException;
use Life\Helper\XmlFileWriter;
use Life\Service\GameService;

class Game
{

    private GameService $gameService;

    private int $iterationsCount;

    private int $size;

    private int $species;

    /**
     * Array of available cells in the game with size x size dimensions
     * Indexed by y coordinate and then x coordinate
     *
     * @var int[][]|null[][]
     */
    private array $cells;

    private XmlFileWriter $output;

    /**
     * @param GameService $gameService
     * @param int $size
     * @param int $species
     * @param int[][]|null[][] $cells
     * @param int $iterationsCount
     * @param XmlFileWriter $output
     */
    public function __construct(
        GameService $gameService,
        int $size,
        int $species,
        array $cells,
        int $iterationsCount,
        XmlFileWriter $output
    ) {
        $this->gameService = $gameService;
        $this->size = $size;
        $this->species = $species;
        $this->cells = $cells;
        $this->iterationsCount = $iterationsCount;
        $this->output = $output;
    }

    /**
     * @throws OutputWritingException
     */
    public function run(): void
    {
        for ($i = 0; $i < $this->iterationsCount; $i++) {
            $this->cells = $this->iterateEvolution();
        }

        $this->output->saveWorld($this->size, $this->species, $this->cells);
    }

    /**
     * @return int[][]|null[][]
     */
    private function iterateEvolution(): array
    {
        $iteratedCells = [];
        for ($y = 0; $y < $this->size; $y++) {
            for ($x = 0; $x < $this->size; $x++) {
                $iteratedCells[$y][$x] = $this->gameService->evolveCell(
                    $this->cells,
                    $this->size,
                    $this->species,
                    $x,
                    $y
                );
            }
        }
        return $iteratedCells;
    }
}
