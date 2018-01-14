<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11/01/2018
 * Time: 17:49
 */

namespace AppBundle\ServicesArg;


class AntiSpam
{
    private $maxLenght;

    public function __construct($maxLenght)
    {
        $this->maxLenght = $maxLenght;
    }

    public function isTextSpam($text)
    {

        var_dump($this->maxLenght);

        if (strlen($text) > $this->maxLenght)
            return true;

        return false;
    }
}