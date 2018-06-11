<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('registerImg', array($this, 'registerImgFilter')),
            new \Twig_SimpleFilter('validImg', array($this, 'validImgFilter')),
            new \Twig_SimpleFilter('googleMapLink', array($this, 'googleMapLinkFilter')),
            new \Twig_SimpleFilter('loader', array($this, 'loaderFilter'))
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

            case 3:
                $href = '/websport/web/img/podium.png';
        }

        $html = "<img src=" . $href . " class='img-circle'>";
        return new \Twig_Markup($html, 'UTF-8');
    }

    public function validImgFilter($number)
    {
        switch ($number) {
            case 1:
                $href = '/websport/web/img/checked.png';
                break;

            default:
                $href = '/websport/web/img/cancel.png';
                break;
        }

        $html = "<img src=" . $href . " class='pull-right'>";
        return new \Twig_Markup($html, 'UTF-8');
    }


    public function googleMapLinkFilter($src,$x,$y){

        $link = " <a href='https://www.google.com/maps/search/?api=1&query=".$x.",".$y."' target='_blank'><img alt='map-link' src='".$src."'></a>";

        return new \Twig_Markup($link, 'UTF-8');
    }

    public function loaderFilter($src){

        $link = "<div id='full-page'><div id='loader-full'></div></div>";

        return new \Twig_Markup($link, 'UTF-8');
    }

}