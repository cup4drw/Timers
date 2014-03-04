<?php

namespace Dell_ESM_Platform;
final class TimeDumper
{
    private $timers;

    public static function Instance()
    {
        static $singleton_instance = null;
        if ($singleton_instance === null)
        {
            $singleton_instance = new TimeDumper();
        }

        return $singleton_instance;
    }

    private function __construct()
    {
        date_default_timezone_set("UTC");
        $this->timers = array();
    }

    // isolate this in case we ever need to change the algorithm
    private function GetOurTime()
    {
        return microtime(true);
    }

    private function getIndicatorString($timers, $pre_string = NULL)
    {
        if($pre_string == NULL)
        {
            $backtrace = debug_backtrace();
            // We want the method 2 up from here
            $caller = $backtrace[2]['function'];
            $count = 1;
        }
        else
        {
            $caller = $pre_string;
            $count = 2;
        }

        $found = true;

        // Make it unique for clarity
        while($found == true)
        {
            $found = false;
            foreach($timers as $indicator => $time)
            {
                if(($caller." - Instance ".$count) == $indicator)
                {
                    $count = $count + 1;
                    $found = true;
                }
            }
        }

        return $caller." - Instance ".$count;
    }

    public function getTimes()
    {
        return $this->timers;
    }

    public function pushTime($indicator = NULL)
    {
        // Allow for some timing reporting

        if((array_key_exists("APPLICATION_ENV", $_SERVER)) and ($_SERVER['APPLICATION_ENV'] == "development"))
        {
            if($indicator == NULL)
            {
                $indicator = $this->getIndicatorString($this->timers);
            }

            if(array_key_exists($indicator, $this->timers))
            {
                $indicator = $this->getIndicatorString($this->timers, $indicator);
            }

            $this->timers[$indicator] = $this->GetOurTime();
        }
    }

    public function resetTimes()
    {
        unset($this->timers);
        $this->timers = array();
    }

}




