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
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        //$index = array_rand($messages);

        return $messages[$index];
    }
}