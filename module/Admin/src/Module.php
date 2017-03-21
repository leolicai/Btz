<?php
/**
 * Module class
 */

namespace Admin;


use Admin\Controller\DashboardController;
use Admin\Controller\IndexController;
use Admin\Controller\MessageController;
use Admin\Controller\ProfileController;
use Admin\Controller\SearchController;
use Admin\Service\AclManager;
use Admin\Service\AuthService;
use Zend\Mvc\MvcEvent;


class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }



    /**
     * Module bootstrap listener.
     *
     * Called after the MVC bootstrapping is completed.
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Get shared event manager
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();

        // Register listener,
        // attach identifier is __NAMESPACE__, only response admin module event handler.
        // if want response all module event handler, use AbstractActionController::class instead __NAMESPACE__
        //$sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_ROUTE, [$this, 'onRouteListener'], 100);
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatchListener'], 100);
        //$sharedEventManager->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchErrorListener'], 100);
        //$sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchErrorListener'], 100);
        //$sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_RENDER_ERROR, [$this, 'onDispatchErrorListener'], 100);
    }


    /**
     * Force use https protocol
     *
     * @param MvcEvent $event
     * @return ResponseInterface
     */
    public function onRouteListener(MvcEvent $event)
    {
        if (php_sapi_name() == "cli") {
            return;
        }

        //**
        $uri = $event->getRequest()->getUri();
        $scheme = $uri->getScheme();
        if('https' != $scheme) {
            $uri->setScheme('https');
            $response=$event->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $uri);
            $response->setStatusCode(301);
            $response->sendHeaders();
            return $response;
        }
        //*/
    }


    public function onDispatchErrorListener(MvcEvent $event)
    {
        $sm = $event->getApplication()->getServiceManager();

        $logger = $sm->get('Logger');

        $error = $event->getError();
        $exception = $event->getParam('exception');
        //die($error);
        $logger->err($error . '=>' . $exception->getMessage());

        return true;

        /**
        $response = $event->getApplication()->getResponse();

        $request = $event->getApplication()->getRequest();

        if ($request instanceof \Zend\Http\Request) {
            if ($request->isXmlHttpRequest()) {
                $exception = $event->getError();
                $logger->info("AJAX call error: " . $exception);
            } else {
                $logger->info("html call error");
            }
        }
        //*/

    }


    /**
     * @param MvcEvent $event
     * @throws \Exception
     */
    public function onDispatchListener(MvcEvent $event)
    {
        // Application running env flag
        $serviceManager = $event->getApplication()->getServiceManager();

        $appConfig = $serviceManager->get('ApplicationConfig');
        $appEnv = isset($appConfig['application']['env']) ? $appConfig['application']['env'] : 'development';

        $viewModel = $event->getViewModel();
        $viewModel->setVariable('appEnv', $appEnv);


        // Get controller name which was dispatched.
        $controller = $event->getRouteMatch()->getParam('controller', null);
        if($controller == IndexController::class) { // Allow all access
            return ;
        }

        // Login status validate
        $authService = $serviceManager->get(AuthService::class);
        if (!$authService->hasIdentity()) {
            $request = $event->getTarget()->getRequest();
            if($request->isXmlHttpRequest()) {
                $viewModel->setTemplate(true);
                $response = $event->getTarget()->getResponse();
                $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
                $response->setContent(json_encode(['success' => false, 'code' => 1001, 'message' => 'Unauthorized']));
                $response->send();
                exit(1);
            } else {
                return $event->getTarget()->redirect()->toRoute('admin/index', ['action' => 'login', 'suffix' => '.html']);
            }
        }

        // Set module default template
        $viewModel->setTemplate('layout/admin_layout');

        $whiteList = [
            ProfileController::class => ['*'],
            DashboardController::class => ['*'],
            SearchController::class => ['*'],
            MessageController::class => ['in', 'out', 'read', 'delete', 'unread', 'send'],
        ];

        $action = $event->getRouteMatch()->getParam('action', null);
        // Convert action name to camel-case form dash-style
        //$action = str_replace('-', '', lcfirst(ucwords($action, '-')));

        if (array_key_exists($controller, $whiteList) &&
            (in_array('*', $whiteList[$controller]) || in_array($action, $whiteList[$controller]) )) {
            return ;
        }

        $aclManager = $serviceManager->get(AclManager::class);
        if (!$aclManager->isValid($controller, $action)) {
            $request = $event->getTarget()->getRequest();
            if($request->isXmlHttpRequest()) {
                $viewModel->setTemplate(true);
                $response = $event->getTarget()->getResponse();
                //$response->setStatusCode(500);
                $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
                $response->setContent(json_encode(['success' => false, 'code' => 1111, 'message' => 'Unauthorized']));
                $response->send();
                exit(1);
            } else {
                throw new \Exception('我们找遍了整个宇宙也没发现谁给了你权利使用这个功能哦!');
            }
        }
    }

}
