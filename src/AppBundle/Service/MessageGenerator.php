<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 16:56
 */

namespace AppBundle\Service;


class MessageGenerator
{
    public function getHappyMessage($index)
    {
        $messages = [
            'premier test',
            'second test',
            'dernier test',
        ];

        //$index = array_rand($messages);

        return $messages[$index];
    }
}