<?php declare(strict_types=1);

namespace Life\Service;

class GameService
{

    /**
     * @param int[][]|null[][] $cells
     * @param int $size
     * @param int $species
     * @param int $x
     * @param int $y
     * @return int|null
     */
    public function evolveCell(
        array $cells,
        int $size,
        int $species,
        int $x,
        int $y
    ): ?int {
        $cell = $cells[$y][$x];
        $neighbours = $this->populateNeighbours($cells, $size, $x, $y);
        $sameSpeciesCount = $this->countSameSpeciesForNeighbours($cell, $neighbours);

        if ($cell !== null && $sameSpeciesCount >= 2 && $sameSpeciesCount <= 3) {
            return $cell;
        }

        $speciesForBirth = $this->countSpeciesForBirth($species, $neighbours);

        if (count($speciesForBirth) > 0) {
            return $speciesForBirth[array_rand($speciesForBirth)];
        }

        return null;
    }

    /**
     * @param int[][]|null[][] $cells
     * @param int $size
     * @param int $x
     * @param int $y
     * @return int[]|null[]
     */
    private function populateNeighbours(array $cells, int $size, int $x, int $y): array
    {
        $neighbours = [];
        if ($y - 1 >= 0 && $x - 1 >= 0) {
            $neighbours[] = $cells[$y - 1][$x - 1];
        }
        if ($y - 1 >= 0) {
            $neighbours[] = $cells[$y - 1][$x];
        }
        if ($y - 1 >= 0 && $x + 1 < $size) {
            $neighbours[] = $cells[$y - 1][$x + 1];
        }

        if ($x - 1 >= 0) {
            $neighbours[] = $cells[$y][$x - 1];
        }
        if ($x + 1 < $size) {
            $neighbours[] = $cells[$y][$x + 1];
        }

        if ($y + 1 < $size && $x - 1 >= 0) {
            $neighbours[] = $cells[$y + 1][$x - 1];
        }
        if ($y + 1 < $size) {
            $neighbours[] = $cells[$y + 1][$x];
        }
        if ($y + 1 < $size && $x + 1 < $size) {
            $neighbours[] = $cells[$y + 1][$x + 1];
        }

        return $neighbours;
    }

    /**
     * @param int|null $cell
     * @param int[]|null[] $neighbours
     * @return int
     */
    private function countSameSpeciesForNeighbours(?int $cell, array $neighbours): int
    {
        $sameSpeciesCount = 0;
        foreach ($neighbours as $neighbour) {
            if ($neighbour === $cell) {
                $sameSpeciesCount++;
            }
        }
        return $sameSpeciesCount;
    }

    /**
     * @param int $species
     * @param int[]|null[] $neighbours
     * @return int[]|null[]
     */
    private function countSpeciesForBirth(int $species, array $neighbours): array
    {
        $speciesForBirth = [];
        for ($i = 0; $i < $species; $i++) {
            $oneSpeciesCount = 0;

            foreach ($neighbours as $neighbour) {
                if ($neighbour === $i) {
                    $oneSpeciesCount++;
                }
            }

            if ($oneSpeciesCount === 3) {
                $speciesForBirth[] = $i;
            }
        }
        return $speciesForBirth;
    }
}
