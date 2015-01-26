<?php

/**
 * Description of RoutingLoader
 *
 * Cette classe charge les routing Yml pour un client donné
 *
 * @author akambi
 * @author Kévin Ziemianski <kevin.ziemianski@canaltp.fr>
 */
namespace CanalTP\SamEcoreApplicationManagerBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class ApplicationRoutingLoader extends Loader
{
    private $loaded = false;
    private $aBundles;
    private $routePrefix;
    private $container;

    public function __construct(
        $aBundles, $routePrefix, $container
    )
    {
        $this->aBundles = $aBundles;
        $this->routePrefix = $routePrefix;
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $this->loaded = true;

        $collection = new RouteCollection();
        $applications = $this->container->getParameter('applications');

        foreach ($applications as $application) {

            $resource = '@' . $application[0] . $application[1] . 'BridgeBundle/Resources/config/routing.yml';
            $type     = 'yaml';

            try {
                $importedRoutes = $this->import($resource, $type);
            
            
                //@TODO : #IUS-162
                //$appRoutes : business routes, but redirect all to sam controller
                //$importedRoutes : business routes renamed, but still with the good business controller
                //$appRoutes = clone $importedRoutes;
                //$appRoutes->addDefaults(array('_controller' => 'CanalTPSamCoreBundle:Sam:AppRender'));
                //$appRoutes->addPrefix('/'. strtolower($application));

                //Change sam to admin for url
                if (strtolower($application[1]) == 'samcore') {
                    $importedRoutes->addPrefix('/admin');
                } else {
                    $importedRoutes->addPrefix('/'. strtolower($application));
                }





    //            foreach ($importedRoutes as $routeName => $route) {
    //                $importedRoutes->add('sam_' . $this->routePrefix . '_' . $routeName, clone $route);
    //                $importedRoutes->remove($routeName);
    //            }

    //            $importedRoutes->addPrefix('/' . $this->routePrefix . '-'. strtolower($application));
                $collection->addCollection($importedRoutes);
    //            $collection->addCollection($appRoutes);
            
            } catch(\InvalidArgumentException $e) {
                //No routing for this bundle, skip
            }
        }

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'sam' === $type;
    }
}
