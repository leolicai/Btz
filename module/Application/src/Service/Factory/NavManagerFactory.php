<?php
/**
 * Nav manager factory
 *
 * User: leo
 */

namespace Application\Service\Factory;


use Application\Service\NavManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class NavManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');

        $config = $container->get('Config');


        return new NavManager($urlHelper, @$config['api_list']['weixin']);
    }


}