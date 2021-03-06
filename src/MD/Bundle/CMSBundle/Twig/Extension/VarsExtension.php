<?php

namespace MD\Bundle\CMSBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Extension;
use Symfony\Component\HttpFoundation\Session\Session;

class VarsExtension extends Twig_Extension {

    private $container;
    private $em;
    private $conn;

    public function __construct(\Doctrine\ORM\EntityManager $em, ContainerInterface $container) {
        $this->em = $em;
        $this->conn = $em->getConnection();
        $this->container = $container;
    }

    public function getName() {
        return 'some.extension';
    }

    public function getFilters() {
        return array(
            'class' => new \Twig_Filter_Method($this, 'getClass'),
            'url_decode' => new \Twig_Filter_Method($this, 'urlDecode'),
            'youtubeThumb' => new \Twig_Filter_Method($this, 'youtubeThumb'),
            'youtubeVId' => new \Twig_Filter_Method($this, 'youtubeVId'),
            'localizedDate' => new \Twig_Filter_Method($this, 'localizedDate'),
        );
    }

    public function getClass($object) {
        return (new \ReflectionClass($object))->getShortName();
    }

    public function urlDecode($var) {
        return urldecode($var);
    }

    public function youtubeThumb($var) {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $var, $matches);
        /* Player Background Thumbnail (480x360 pixels)
          http://i1.ytimg.com/vi/G0wGs3useV8/0.jpg
          Start Thumbnail (120x90 pixels)
          http://i1.ytimg.com/vi/G0wGs3useV8/1.jpg
          Middle Thumbnail (120x90 pixels)
          http://i1.ytimg.com/vi/G0wGs3useV8/2.jpg
          End Thumbnail (120x90 pixels)
          http://i1.ytimg.com/vi/G0wGs3useV8/3.jpg
          High Quality Thumbnail (480x360 pixels)
          http://i1.ytimg.com/vi/G0wGs3useV8/hqdefault.jpg
          Medium Quality Thumbnail (320x180 pixels)
          http://i1.ytimg.com/vi/G0wGs3useV8/mqdefault.jpg
          Normal Quality Thumbnail (120x90 pixels)
          http://i1.ytimg.com/vi/G0wGs3useV8/default.jpg */
        if (count($matches) > 0) {
            $videoId = $matches[0];
            return "http://i1.ytimg.com/vi/$videoId/0.jpg";
        }
        return NULL;
    }

    public function youtubeVId($var) {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $var, $matches);
        $pos = strpos($var, '/v/');
        if (count($matches) > 0) {
            return $matches[0];
        } else if ($pos !== FALSE) {
            return str_replace('/', '', substr($var, $pos + 3));
        }

        return NULL;
    }

    public function localizedDate($date) {
        $months = array("Jan" => "??????????", "Feb" => "????????????", "Mar" => "????????", "Apr" => "??????????", "May" => "????????", "Jun" => "??????????", "Jul" => "??????????", "Aug" => "??????????", "Sep" => "????????????", "Oct" => "????????????", "Nov" => "????????????", "Dec" => "????????????");
        $date = strtr($date, $months);
        $days = array("Sat" => "??????????", "Sun" => "??????????", "Mon" => "??????????????", "Tue" => "????????????????", "Wed" => "????????????????", "Thu" => "????????????", "Fri" => "????????????");
        return strtr($date, $days);
    }

}
