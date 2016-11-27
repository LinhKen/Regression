<?php

declare(strict_types = 1);

namespace MCordingley\Regression;

use ArrayIterator;
use IteratorAggregate;
use Countable;
use InvalidArgumentException;
use Traversable;

final class Observations implements
    Countable,
    IteratorAggregate
{
    /** @var int */
    private $featureCount = 0;

    /** @var array */
    private $observations = [];

    /**
     * @param array $features
     * @param array $outcomes
     * @return self
     */
    public static function fromArray(array $features, array $outcomes): self
    {
        $observationCount = count($outcomes);

        if (count($features) !== $observationCount) {
            throw new InvalidArgumentException('Must have as many outcomes as observations.');
        }

        $observations = new self;

        for ($i = 0; $i < $observationCount; $i++) {
            $observations->add($features[$i], $outcomes[$i]);
        }

        return $observations;
    }

    /**
     * @param array $features
     * @param float $outcome
     */
    public function add(array $features, float $outcome)
    {
        $this->addObservation(new Observation($features, $outcome));
    }

    /**
     * @param Observation $observation
     */
    public function addObservation(Observation $observation)
    {
        $featureCount = count($observation->getFeatures());

        if (!$this->featureCount) {
            $this->featureCount = $featureCount;
        } elseif ($this->featureCount !== $featureCount) {
            throw new InvalidArgumentException('All observations must have the same number of features.');
        }

        $this->observations[] = $observation;
    }

    /**
     * @return array
     */
    public function getFeatures(): array
    {
        return array_map(function (Observation $observation) {
            return $observation->getFeatures();
        }, $this->observations);
    }

    /**
     * @return array
     */
    public function getOutcomes(): array
    {
        return array_map(function (Observation $observation) {
            return $observation->getOutcome();
        }, $this->observations);
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->observations);
    }

    /**
     * @return int
     */
    public function getFeatureCount(): int
    {
        return $this->featureCount;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->observations);
    }

    /**
     * @param int $index
     * @return Observation
     */
    public function getObservation($index): Observation
    {
        return $this->observations[$index];
    }
}
