<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16/01/2018
 * Time: 17:45
 */

namespace AppBundle\Services;

class ToolsService
{
    /**
     * Method to generate random date between two dates
     * @param $sStartDate
     * @param $sEndDate
     * @param string $sFormat
     * @return bool|string
     */
    function randomDate($sStartDate = '1-1-1960', $sEndDate = '31-12-2010', $sFormat = 'd-m-Y')
    {
        // Convert the supplied date to timestamp
        $fMin = strtotime($sStartDate);
        $fMax = strtotime($sEndDate);
        // Generate a random number from the start and end dates
        $fVal = mt_rand($fMin, $fMax);
        // Convert back to the specified date format
        $date = date($sFormat, $fVal);
        return new \DateTime($date);

    }
}