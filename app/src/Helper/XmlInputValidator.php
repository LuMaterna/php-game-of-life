<?php declare(strict_types=1);

namespace Life\Helper;

use Life\Exception\InvalidInputException;
use SimpleXMLElement;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class XmlInputValidator
{

    /**
     * @throws InvalidInputException
     */
    public function validate(SimpleXMLElement $life): void
    {
        $constraints = new Assert\Collection([
            'world' => new Assert\Collection([
                'cells' => [new Assert\Positive(
                    [],
                    sprintf('Missing or invalid element %s', 'world.cells')
                )],
                'species' => new Assert\Positive(
                    [],
                    sprintf('Missing or invalid element %s', 'world.species')
                ),
                'iterations' => new Assert\PositiveOrZero(
                    [],
                    sprintf('Missing or invalid element %s', 'world.iterations')
                ),
            ], null, null, true),
            'organisms' => new Assert\Collection([
                'organism' => new Assert\Collection([
                    new Assert\Type('array'),
                    new Assert\Collection([
                        'x_pos' => [
                            new Assert\PositiveOrZero(
                                [],
                                sprintf('Missing or invalid element %s', 'organisms.organism.x_pos')
                            ),
                            new Assert\LessThanOrEqual(
                                $life->world->cells,
                                null,
                                sprintf(
                                    'Value of element %s of element %s must be between 0 and number of cells',
                                    'x_pos',
                                    'organism'
                                )
                            ),
                        ],
                        'y_pos' => [
                            new Assert\PositiveOrZero(
                                [],
                                sprintf('Missing or invalid element %s', 'organisms.organism.y_pos')
                            ),
                            new Assert\LessThanOrEqual(
                                $life->world->cells,
                                null,
                                sprintf(
                                    'Value of element %s of element %s must be between 0 and number of cells',
                                    'y_pos',
                                    'organism'
                                )
                            ),
                        ],
                        'species' => [
                            new Assert\PositiveOrZero(
                                [],
                                sprintf('Missing or invalid element %s', 'organisms.organism.species')
                            ),
                            new Assert\LessThanOrEqual(
                                $life->world->species,
                                null,
                                sprintf(
                                    'Value of element %s of element %s must be between 0 and number of species',
                                    'species',
                                    'organism'
                                )
                            ),
                        ],
                    ], null, null, true),
                ], null, null, true),
            ], null, null, true),
        ], null, null, true);

        $validator = Validation::createValidator();
        $violations = $validator->validate((array) json_decode((string) json_encode($life), true), $constraints);

        if ($violations->count() !== 0) {
            throw new InvalidInputException((string) $violations->get(0)->getMessage());
        }
    }
}
