<?php

namespace CanalTP\SamEcoreApplicationManagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of CanalTPBusinessComponentCompilerPass
 *
 * @author akambi fagbohoun <contact@akambi-fagbohoun.com>
 */
class CanalTPBusinessComponentCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $applications = $container->getParameter('applications');

        $factoryDefinition = $container
            ->register('sam.business_component', 'CanalTP\SamEcoreApplicationManagerBundle\Component\BusinessComponentRegistry')
            ->addArgument(new Reference('doctrine.orm.entity_manager'))
            ->addArgument(new Reference('session'))
            ->addArgument(new Reference('canal_tp_sam.application.finder'));

        foreach ($applications as $application) {
            $applicationName = strtolower($application[1]);
            
            // @todo Remove
            if (!$container->has('sam.business_component.' . $applicationName)) {
                continue;
            }

            $factoryDefinition->addMethodCall(
                'addBusinessComponent',
                array(
                    $applicationName,
                    new Reference('sam.business_component.' . $applicationName)
                )
            );
        }
    }
}
