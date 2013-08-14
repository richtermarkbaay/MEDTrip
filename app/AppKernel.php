<?php
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
/**
 * When you make changes to this class please modify the appKernelTransformer.php
 * That file is used by the build script for deploying to staging.
 */
class AppKernel extends Kernel
{
    // code from: http://kriswallsmith.net/post/27979797907/get-fast-an-easy-symfony2-phpunit-optimization

    protected function initializeContainer()
    {
        static $first = true;

        if ('test' !== $this->getEnvironment()) {
            parent::initializeContainer();
            return;
        }

        $debug = $this->debug;

        if (!$first) {
            // disable debug mode on all but the first initialization
            $this->debug = false;
        }

        // will not work with --process-isolation
        $first = false;

        try {
            parent::initializeContainer();
        } catch (\Exception $e) {
            $this->debug = $debug;
            throw $e;
        }

        $this->debug = $debug;
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
//             new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            //new FOQ\ElasticaBundle\FOQElasticaBundle(),
            new Rezzza\MailChimpBundle\RezzzaMailChimpBundle(),
            new HealthCareAbroad\PagerBundle\PagerBundle(),
            new HealthCareAbroad\AdminBundle\AdminBundle(),
            new HealthCareAbroad\UserBundle\UserBundle(),
            new HealthCareAbroad\HelperBundle\HelperBundle(),
            new HealthCareAbroad\FrontendBundle\FrontendBundle(),
            new HealthCareAbroad\MediaBundle\MediaBundle(),
            new HealthCareAbroad\MailerBundle\MailerBundle(),
            new HealthCareAbroad\InstitutionBundle\InstitutionBundle(),
            new HealthCareAbroad\TreatmentBundle\TreatmentBundle(),
            new HealthCareAbroad\SearchBundle\SearchBundle(),
            new HealthCareAbroad\LogBundle\LogBundle(),
            new HealthCareAbroad\AdvertisementBundle\AdvertisementBundle(),
            new HealthCareAbroad\MemcacheBundle\MemcacheBundle(),
            new HealthCareAbroad\DoctorBundle\DoctorBundle(),
            new HealthCareAbroad\TermBundle\TermBundle(),
            new HealthCareAbroad\StatisticsBundle\StatisticsBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new HealthCareAbroad\ApiBundle\ApiBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            //$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();

            if ($this->getEnvironment() === 'dev') {
                //$bundles[] = new JMS\DebuggingBundle\JMSDebuggingBundle($this);
            }
        }

        return $bundles;
    }

    /**
     * Overridden to provide extended debugging capabilities for JMSDebuggingBundle
     * @see \Symfony\Component\HttpKernel\Kernel::getContainerBaseClass()
     */
    protected function getContainerBaseClass()
    {
        if ($this->getEnvironment() === 'dev') {
            return '\JMS\DebuggingBundle\DependencyInjection\TraceableContainer';
        }

        return parent::getContainerBaseClass();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
