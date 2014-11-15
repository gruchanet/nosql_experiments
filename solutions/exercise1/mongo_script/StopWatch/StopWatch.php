<?php

namespace StopWatch;

use Exception;

class StopWatch
{
    private $start;
    private $end;
    private $diff;
    private $stopClicks = array();
    private $rounding;

    public function __construct($rounding = 10)
    {
        $this->rounding = $rounding;
    }

    public function getStartTime()
    {
        return $this->start;
    }

    public function getStopTime()
    {
        return $this->end;
    }

    public function getTimeDifference()
    {
        return $this->diff;
    }

    public function getStopClickTime($title)
    {
        if (!isset($this->stopClicks[$title])) {
            throw new Exception('Stop-click with title "' . $title . '" was not set.');
        }

        return $this->stopClicks[$title];
    }

    public function startTimer()
    {
        $this->start = $this->getMicroTime();
    }

    public function stopTimer()
    {
        if (!isset($this->start)) {
            throw new Exception('Database is not set in MongoAdapter instance.');
        }

        $this->end = $this->getMicroTime();
        $this->calculateTimeDiff();
    }

    public function stopClick($title)
    {
        if (isset($this->stopClicks[$title])) {
            throw new Exception('Timer has already stop-click "' . $title . '".');
        }

        $this->stopClicks[$title] = $this->getMicroTime();
    }

    private function getMicroTime()
    {
        return array_sum(explode(' ', microtime(true)));
    }

    private function calculateTimeDiff()
    {
        $this->diff = round(($this->end - $this->start), $this->rounding);
    }
} 