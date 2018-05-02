<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('registerImg', array($this, 'registerImgFilter')),
            new \Twig_SimpleFilter('validImg', array($this, 'validImgFilter'))
        );
    }

    public function registerImgFilter($number)
    {
        switch ($number) {
            case 0:
                return '';

            case 1:
                $href = '/websport/web/img/canRegister.png';
                break;

            case 2:
                $href = '/websport/web/img/race_start.png';
                break;

            default:
                $href = '';
        }

        $html = "<img src=" . $href . " class='img-circle'>";
        return new \Twig_Markup($html, 'UTF-8');
    }

    public function validImgFilter($number)
    {
        switch ($number) {
            case true:
                $href = '/websport/web/img/checked.png';
                break;

            case false:
                $href = '/websport/web/img/cancel.png';
                break;

            default:
                $href = '';
        }

        $html = "<img src=" . $href . " class='img-circle pull-right'>";
        return new \Twig_Markup($html, 'UTF-8');
    }
}