<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

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
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            //====================================================
            // AJOUT
            //====================================================
            // FOSUSER   
            new FOS\UserBundle\FOSUserBundle(),
            // SONATA
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),
            new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Sonata\NewsBundle\SonataNewsBundle(),
            // DEPENDANCES NECESSAIRES   
            // MENUS
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            // APPLICATIONS DE SURCHARGE
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new Application\Sonata\AdminBundle\ApplicationSonataAdminBundle(),
            new Application\Sonata\NewsBundle\ApplicationSonataNewsBundle(),
            // AUTRES EDPENDANCES
            // new Craue\FormFlowBundle\CraueFormFlowBundle(),
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            //MOPA
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            //new Bc\Bundle\BootstrapBundle\BcBootstrapBundle(),
            // new Bc\Bundle\BootstrapBundle\BcBootstrapBundle(),
            //new Braincrafted\BootstrapBundle(),
            // new Braincrafted\BootstrapBundle\BraincraftedBootstrapBundle(),
            //     new Application\TestBundle\ApplicationTestBundle(),
            //====================================================
            // PAYPAL: JMSPAYMENT
            //====================================================
            /* new JMS\Payment\CoreBundle\JMSPaymentCoreBundle(),
              new JMS\Payment\PaypalBundle\JMSPaymentPaypalBundle(),
              new Application\JMSPaymentBundle\ApplicationJMSPaymentBundle(), */
            //new Application\PaypalBundle\ApplicationPaypalBundle(),
            //====================================================
            // PAYPAL: PAYUM
            //====================================================
             new Payum\Bundle\PayumBundle\PayumBundle(),
              new Application\PaymentBundle\ApplicationPaymentBundle(),
              new Application\PaypalExpressCheckoutBundle\ApplicationPaypalExpressCheckoutBundle(), 

            //====================================================
            // FORM FILTER
            //====================================================
            new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
            new Savvy\FilterNatorBundle\SavvyFilterNatorBundle()

                /* new Payum\Bundle\PayumBundle\PayumBundle(),
                  //  new Application\PaymentBundle\ApplicationPaymentBundle(),
                  new Application\PaypalExpressCheckoutBundle\ApplicationPaypalExpressCheckoutBundle(),
                  new Application\PaymentBundle\ApplicationPaymentBundle(), */,
            new Application\RelationsBundle\ApplicationRelationsBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            //    $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
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
