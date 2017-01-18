<?php
/**
 * Admin module menu manager
 *
 * User: leo
 */

namespace Admin\Service;


use Admin\Entity\Action;
use Admin\Entity\Component;
use Admin\Entity\Member;
use Doctrine\ORM\EntityManager;
use Zend\View\Helper\Url;

class NavManager
{

    /**
     * @var Url
     */
    private $urlHelper;

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var AclManager
     */
    private $aclManager;

    /**
     * @var array
     */
    private $topRightItems;

    /**
     * @var array
     */
    private $sideTreeItems;


    /**
     * @var array
     */
    private $breadcrumbItems;


    public function __construct(AuthService $authService, MemberManager $memberManager, Url $url, AclManager $aclManager, EntityManager $entityManager)
    {
        $this->authService = $authService;

        $this->memberManager = $memberManager;

        $this->aclManager = $aclManager;

        $this->urlHelper = $url;

        $this->entityManager = $entityManager;

        $this->initSideTreeItem();

        $this->initTopRightItem();

        $this->initBreadcrumbItem();

    }

    /**
     * @return array
     */
    public function getTopRightItems()
    {
        return $this->topRightItems;
    }

    /**
     * @return array
     */
    public function getSideTreeItems()
    {
        return $this->sideTreeItems;
    }

    /**
     * @return array
     */
    public function getBreadcrumbItems()
    {
        return $this->breadcrumbItems;
    }



    /**
     * Quick create nav menu link item array
     *
     * @param string $id
     * @param string $icon
     * @param string $label
     * @param string $link
     * @param string $title
     * @param string $type item|divider
     * @return array
     */
    public function createNavItem($id, $icon, $label, $link = '', $title = '', $type = 'item')
    {
        return [
            'id' => $id,
            'icon' => $icon,
            'label' => $label,
            'link' => $link,
            'title' => empty($title) ? $label : $title,
            'type' => $type
        ];
    }


    /**
     * Init top right bar items
     */
    public function initTopRightItem()
    {
        $this->topRightItems = [];

        if(!$this->authService->hasIdentity()) {
            return ;
        }

        $identity = $this->authService->getIdentity();
        $member = $this->memberManager->getMember($identity);
        if (null == $member) {
            return ;
        }

        $url = $this->urlHelper;

        // Current user profile menu configuration
        $memberItem = $this->createNavItem('profile_menu', 'user', $member->getMemberName());
        $memberItem['dropdown'] = [
            $this->createNavItem('summary', 'user', 'Summary', $url('admin/profile'), $member->getMemberName()),
            $this->createNavItem('password', 'hashtag', 'Password', $url('admin/profile', ['action' => 'password'])),
            $this->createNavItem('profile_detail', 'edit', 'Profiles', $url('admin/profile', ['action' => 'update'])),
            $this->createNavItem('', '', '', '', '', 'divider'),
            $this->createNavItem('profile_logout', 'sign-out', 'Logout', $url('admin/index', ['action' => 'logout', 'suffix' => '.html'])),
        ];
        if (empty($this->getSideTreeItems()) && Member::LEVEL_SUPERIOR == $member->getMemberLevel()) {
            $memberItem['dropdown'][] = $this->createNavItem('', '', '', '', '', 'divider');
            $memberItem['dropdown'][] = $this->createNavItem('init_menu', 'cog', 'Init menu', $url('admin/component', ['action' => 'sync', 'key' => 'init']));
        }

        $this->addTopRightItem($memberItem);

        // Logout menu configuration
        $logoutItem = $this->createNavItem('logout', 'sign-out', 'Logout', $url('admin/index', ['action' => 'logout', 'suffix' => '.html']));
        $this->addTopRightItem($logoutItem);
    }


    /**
     * Add a top item
     *
     * @param array $item
     */
    public function addTopRightItem($item)
    {
        array_push($this->topRightItems, $item);
    }


    /**
     * Init breadcrumbs
     */
    public function initBreadcrumbItem()
    {
        $this->breadcrumbItems = [];
        $url = $this->urlHelper;
        $this->addBreadcrumbItem('Home', $url('admin'));
    }

    /**
     * Add a breadcrumb
     *
     * @param string $label
     * @param string $link
     */
    public function addBreadcrumbItem($label, $link)
    {
        $item = $this->createNavItem('', '', $label, $link);
        array_push($this->breadcrumbItems, $item);
    }


    /**
     * Test tree menu
     */
    public function initSideTreeItem()
    {
        $this->sideTreeItems = [];
        $url = $this->urlHelper;

        $dashboard = $this->createNavItem('dashboard', 'dashboard', 'Dashboard', $url('admin/dashboard'));
        $this->addSideTreeItem($dashboard);






        /**
        $member = $this->createNavItem('member', 'user', 'Administrator');
        $member['dropdown'] = [
            $this->createNavItem('member_list', 'bars', 'Administrators', $url('admin/member')),
            $this->createNavItem('member_add', 'user-plus', 'New Administrator', $url('admin/member', ['action' => 'add'])),
        ];
        $this->addSideTreeItem($member);

        $dept = $this->createNavItem('dept', 'users', 'Department');
        $dept['dropdown'] = [
            $this->createNavItem('dept_list', 'bars', 'Departments', $url('admin/dept')),
            $this->createNavItem('dept_add', 'plus', 'New Department', $url('admin/dept', ['action' => 'add'])),
        ];
        $this->addSideTreeItem($dept);

        $component = $this->createNavItem('component', 'cubes', 'Component');
        $component['dropdown'] = [
            $this->createNavItem('component_list', 'bars', 'Components', $url('admin/component')),
            //$this->createNavItem('dept_add', 'plus', 'New Department', $url('admin/dept', ['action' => 'add'])),
        ];
        $this->addSideTreeItem($component);
        //*/

        $actions = $this->entityManager->getRepository(Action::class)->findBy([
            'actionMenu' => Action::MENU_YES,
            'actionStatus' => Action::STATUS_VALIDITY,
        ], [
            'actionRank' => 'DESC',
            'actionName' => 'ASC',
        ], 100);
        $subMenus = [];
        foreach ($actions as $action) {
            if ($action instanceof Action) {
                $subMenus[$action->getControllerClass()][] = [
                    'id' => $action->getControllerClass() . '::' . $action->getActionKey() . 'Action',
                    'icon' => $action->getActionIcon(),
                    'label' => $action->getActionName(),
                    'action' => $action->getActionKey(),
                ];
            }
        }

        $components = $this->entityManager->getRepository(Component::class)->findBy([
            'comStatus' => Component::STATUS_VALIDITY,
            'comMenu' => Component::MENU_YES,
        ], [
            'comRank' => 'DESC',
            'comName' => 'ASC',
        ]);

        foreach ($components as $component) {
            if ($component instanceof Component) {
                $controller = $component->getComClass();
                $item = $this->createNavItem(
                    $controller,
                    $component->getComIcon(),
                    $component->getComName(),
                    $url($component->getComRoute())
                );
                if (array_key_exists($controller, $subMenus)) {
                    $item['dropdown'] = [];
                    foreach ($subMenus[$controller] as $subMenu) {
                        $item['dropdown'][] = $this->createNavItem(
                            $subMenu['id'],
                            $subMenu['icon'],
                            $subMenu['label'],
                            $url($component->getComRoute(), ['action' => $subMenu['action']])
                        );
                    }

                }
                $this->addSideTreeItem($item);
            }
        }




        $menus = $this->aclManager->getMemberMenus($this->authService->getIdentity());
        if (empty($menus)) {
            return;
        }

        $components = $menus['components'];
        $actions = $menus['actions'];

        foreach ($components as $component) {
            if ($component instanceof Component) {
                $item = $this->createNavItem(
                    $component->getComClass(),
                    $component->getComIcon(),
                    $component->getComName(),
                    $url($component->getComRoute())
                );

                if (array_key_exists($component->getComClass(), $actions)) {
                    foreach ($actions[$component->getComClass()] as $action) {
                        if ($action instanceof Action) {
                            $subItem = $this->createNavItem(
                                $action->getControllerClass() . '::' . $action->getActionKey() . 'Action',
                                $action->getActionIcon(),
                                $action->getActionName(),
                                $url($component->getComRoute(), ['action' => $action->getActionKey()])
                            );
                            $item['dropdown'][] = $subItem;
                        }
                    }
                }

                $this->addSideTreeItem($item);
            }
        }

    }

    /**
     * @param array $item
     */
    public function addSideTreeItem($item)
    {
        array_push($this->sideTreeItems, $item);
    }


}