<?php

if (! function_exists('addPercentage')) {
    function addPercentage($percent, $total): float|int
    {
        $increaseAmount = ($total * $percent) / 100;

        return $total + $increaseAmount;
    }
}
