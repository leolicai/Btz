<?php
/**
 * Navgation service
 *
 * User: leo
 */

namespace Application\Service;


use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\Url;

class NavManager
{

    /**
     * @var AuthenticationService
     */
    private $authService;


    /**
     * @var Url
     */
    private $urlHelper;


    /**
     * NavManager constructor.
     *
     * @param AuthenticationService $authService
     * @param Url $urlHelper
     */
    public function __construct(AuthenticationService $authService, Url $urlHelper)
    {

        $this->authService = $authService;
        $this->urlHelper = $urlHelper;

    }


    public function getMenuItems()
    {
        $url = $this->urlHelper;
        $items = [];
        array_push($items, ['id' => 'home', 'label' => 'Home', 'link' => $url('home')]);
        array_push($items, ['id' => 'contact', 'label' => 'Contact Us', 'link' => $url('contact')]);
        //array_push($items, ['id' => 'test', 'label' => 'Test', 'link' => $url('app/index', ['action' => 'test', 'suffix' => '.html'])]);


        //**
        if (!$this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'guest',
                'label' => 'Hi: Guest!',
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'login',
                        'label' => '<i class="fa fa-sign-in" aria-hidden="true"></i> Sign in',
                        'title' => 'Sign in',
                        'link' => $url('user/auth', ['action'=>'login', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'sign-up',
                        'label' => '<i class="fa fa-user-plus" aria-hidden="true"></i> Sign up',
                        'title' => 'Sign up',
                        'link' => $url('user/auth', ['action'=>'sign-up', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'forgot-password',
                        'label' => '<i class="fa fa-support" aria-hidden="true"></i> Password',
                        'title' => 'Forgot password',
                        'link' => $url('user/auth', ['action' => 'forgot-password', 'suffix' => '.html'])
                    ],
                ]
            ];
        } else {

            $items[] = [
                'id' => 'profile',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'profile',
                        'label' => '<i class="fa fa-home" aria-hidden="true"></i> Preface',
                        'title' => 'My Preface',
                        'link' => $url('user/profile', ['suffix' => '.html'])
                    ],
                    [
                        'id' => 'update',
                        'label' => '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Profile',
                        'title' => 'Update profile',
                        'link' => $url('user/profile', ['action' => 'update', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'email',
                        'label' => '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> E-mail',
                        'title' => 'Update E-mail address',
                        'link' => $url('user/profile', ['action' => 'email', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'password',
                        'label' => '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Password',
                        'title' => 'Update password',
                        'link' => $url('user/profile', ['action' => 'password', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'logout',
                        'label' => '<i class="fa fa-sign-out" aria-hidden="true"></i> Sign out',
                        'title' => 'Sign out',
                        'link' => $url('user/auth', ['action'=>'logout', 'suffix' => '.html'])
                    ],
                ]
            ];
        }
        //*/
        return $items;
    }

}