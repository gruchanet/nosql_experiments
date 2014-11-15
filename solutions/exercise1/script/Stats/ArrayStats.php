<?php

namespace Stats;

class ArrayStats
{
    /* Properties */
    private $keysCounters;
    private $counterStats = array(
        'all' => 0,
        'different' => 0,
        'unique' => 0
    );
    private $uniqueKeys = array();

    public function __construct($keysCounters)
    {
        $this->keysCounters = $keysCounters;
        $this->prepareStats();
    }

    public function getKeysCounters()
    {
        return $this->keysCounters;
    }

    private function prepareStats()
    {
        foreach ($this->keysCounters as $key => $counter) {
            $this->sumAllKeyCounter($counter);

            if ($this->isKeyUnique($counter)) {
                $this->addUniqueKey($key);
            }
        }
        $this->sumDifferentKeys();
        $this->sumUniqueKeys();
        $this->sortArrayDesc();
    }

    private function sumAllKeyCounter($counter)
    {
        $this->counterStats['all'] += $counter;
    }

    private function isKeyUnique($counter)
    {
        return $counter === 1 ? true : false;
    }

    private function addUniqueKey($key)
    {
        $this->uniqueKeys[] = $key;
    }

    private function sumDifferentKeys()
    {
        $this->counterStats['different'] = count($this->keysCounters);
    }

    private function sumUniqueKeys()
    {
        $this->counterStats['unique'] = count($this->uniqueKeys);
    }

    private function sortArrayDesc()
    {
        arsort($this->keysCounters);
    }

    public function getAllKeysCounter()
    {
        return $this->counterStats['all'];
    }

    public function getDifferentKeysCounter()
    {
        return $this->counterStats['different'];
    }

    public function getUniqueKeysCounter()
    {
        return $this->counterStats['unique'];
    }

    public function getUniqueKeys()
    {
        return $this->uniqueKeys;
    }

    public function getTopKeys($limit)
    {
        return array_slice($this->keysCounters, 0, $limit);
    }

    public function getBottomKeys($limit)
    {
        return asort(
            array_slice(
                $this->keysCounters,
                $this->getDifferentKeysCounter() - $limit,
                $this->getDifferentKeysCounter()
            )
        );
    }

    public function getRandomUniqueKey()
    {
        $randomKey = array_rand($this->uniqueKeys);

        return $this->uniqueKeys[$randomKey];
    }
}