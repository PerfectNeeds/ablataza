<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

    const webRoot = 'public_html/';
    const AdminMail = 'peter.nassef@gmail.com';

    public function init() {
        date_default_timezone_set('Africa/Cairo');
        parent::init();
    }

    public function registerBundles() {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            new RaulFraile\Bundle\LadybugBundle\RaulFraileLadybugBundle(),
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new \MD\Bundle\AdminTemplateBundle\AdminTemplateBundle(),
            new \MD\Bundle\MediaBundle\MediaBundle(),
            new \MD\Bundle\ServiceBundle\ServiceBundle(),
            new \MD\Bundle\CMSBundle\CMSBundle(),
            new \MD\Bundle\FileUploaderBundle\FileUploaderBundle(),
            new \MD\Bundle\FEBundle\FEBundle(),
            new \MD\Bundle\UserBundle\UserBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
