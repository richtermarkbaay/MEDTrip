<?php
namespace HealthCareAbroad\SearchBundle\Routing;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\RouteCollection;

use Symfony\Component\Config\Loader\LoaderInterface;

class SearchLoader implements LoaderInterface
{
    private $loaded = false;
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {
        $config = $this->loadYamlConfig($resource);

        return $this->buildRoutes($this->buildOptions($config));
    }

    protected function buildRoutes($options)
    {
        extract($options);

        // Collect routes
        $routes = new RouteCollection();

        // $id_master is indicating a master detail table
        if (isset($id_master)) {
            // List
            $pattern = '/{'.$id_master.'}/list';
            $defaults = array('_controller' => $class.':list');
            $requirements = array($id_master => '\d+');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_list', $route);

            // New
            $pattern = '/{'.$id_master.'}/new';
            $defaults = array('_controller' => $class.':new');
            $requirements = array($id_master => '\d+');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_new', $route);

            // Create
            $pattern = '/{'.$id_master.'}/create';
            $defaults = array('_controller' => $class.':create');
            $requirements = array($id_master => '\d+', '_method' => 'post');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_create', $route);
        } else {
            // New
            $pattern = '/new';
            $defaults = array('_controller' => $class.':new');
            $requirements = array();

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_new', $route);

            // Create
            $pattern = '/create';
            $defaults = array('_controller' => $class.':create');
            $requirements = array('_method' => 'post');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_create', $route);
        }

        // prevent null value of $id_entity
        if (isset($id_entity)) {
            // Show
            $pattern = '/{'.$id_entity.'}/show';
            $defaults = array('_controller' => $class.':show');
            $requirements = array($id_entity => '\d+');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_show', $route);

            // Edit
            $pattern = '/{'.$id_entity.'}/edit';
            $defaults = array('_controller' => $class.':edit');
            $requirements = array($id_entity => '\d+');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_edit', $route);

            // Update
            $pattern = '/{'.$id_entity.'}/update';
            $defaults = array('_controller' => $class.':update');
            $requirements = array($id_entity => '\d+', '_method' => 'post');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_update', $route);

            // Delete
            $pattern = '/{'.$id_entity.'}/delete';
            $defaults = array('_controller' => $class.':delete');
            $requirements = array($id_entity => '\d+', '_method' => 'post');

            $route = new Route($pattern, $defaults, $requirements);
            $routes->add($name.'_delete', $route);
        }

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'crud' === $type;
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        // irrelevant to us, since we don't need a resolver
    }

    private function loadYamlConfig($resource)
    {
        $kernel = $this->container->get('kernel');
        $config = Yaml::parse($kernel->locateResource($resource));

        if (null === $config) {
            $config = array();
        }

        if (!is_array($config)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" must contain a YAML array.', $file));
        }

        return $config;
    }

    private function buildOptions($config)
    {
        $options = $config['options'];

        if (!isset($options['id_entity'])) {
            $options['id_entity'] = 'id';
        }

        if (!isset($options['id_master'])) {
            $options['id_master'] = null;
        }

        return $options;
    }


















    public function load($resource, $type = null)
    {
        if ($this->isLoaded === true) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new RouteCollection();

        $pattern ='';
        $defaults = array(
            '_controller' => 'FrontendBundle:Default:actionNameHere'
        );

        $route = new Route($pattern, $defaults);
        $routes->add('searchResultsRoute', $route);

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return $type = 'searchResults';
    }

    public function getResolver()
    {
    }

    public function setResolver()
    {
    }
}