<?php


namespace App\Services\Bench;


class BenchConsole extends Bench
{
    public static function mark($id)
    {
        $seconds = parent::mark($id);
        echo $id . ': ' . static::prettySeconds((float)$seconds) . "\r\n";
        return $seconds;
    }

    public static function stop()
    {
        $seconds = parent::stop();
        echo static::prettySeconds((float)$seconds) . "\r\n";
        return $seconds;
    }

    protected static function prettySeconds(float $seconds): string
    {
        $days = (int)gmdate('j', $seconds) - 1;
        $hours = (int)gmdate('H', $seconds);
        if ($days > 0) {
            $hours += $days * 24;
        }

        return $hours . gmdate('\h i\m s\s', $seconds);
    }
}
