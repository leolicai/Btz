<?php
/**
* AclController.php
*
* @author: Leo <camworkster@gmail.com>
* @version: 1.0
*/


namespace Admin\Controller;

use Admin\Entity\AclDepartment;
use Admin\Entity\AclMember;
use Admin\Entity\Department;
use Admin\Entity\Member;
use Admin\Exception\InvalidArgumentException;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


/**
 * 系统权限管理
 *
 * Class AclController
 * @package Admin\Controller
 */
class AclController extends AdminBaseController
{

    /**
     * 可授权用户列表
     */
    public function membersAction()
    {
        // Page information
        $page = (int)$this->params()->fromRoute('key', 1);
        $size = 10;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $memberManager = $this->getMemberManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'members', 'key' => '%d']));
        $paginationHelper->setCount($memberManager->getMembersCount());

        // Render view data
        $members = $memberManager->getMembersByLimitPage($page, $size);

        return new ViewModel([
            'entities' => $members,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 个人权限配置
     */
    public function memberAction()
    {
        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $member_id = (string)array_shift($params);

        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new InvalidArgumentException('禁止操作该成员');
        }

        $member = $this->getMemberManager()->getMember($member_id);
        if (Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            throw new InvalidArgumentException('该成员账号已经失效');
        }

        // Page information
        $page = (int)array_shift($params);
        if ($page < 1) { $page = 1; }
        $size = 4;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $componentManager = $this->getComponentManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'member', 'key' =>  $member_id . '_%d']));
        $paginationHelper->setCount($componentManager->getComponentsCount());

        $components = $componentManager->getComponentsByLimitPage($page, $size);

        $rows = $this->getAclManager()->getMemberAndActionAllAclByMember($member_id);
        $acl = [];
        foreach ($rows as $row) {
            if ($row instanceof AclMember) {
                $acl[$row->getAction()] = $row;
            }
        }

        return new ViewModel([
            'member' => $member,
            'components' => $components,
            'acl' => $acl,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 分配个人权限
     */
    public function memberDispatchAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $member_id = (string)array_shift($params);

        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new InvalidArgumentException('禁止操作该成员');
        }

        $member = $this->getMemberManager()->getMember($member_id);
        if (Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            throw new InvalidArgumentException('该成员账号已经失效');
        }

        $action_id = (string)array_shift($params);
        $this->getComponentManager()->getAction($action_id);

        $status = (int)array_shift($params);
        $list = AclMember::getAclStatusList();
        if (!array_key_exists($status, $list)) {
            throw new InvalidArgumentException('非法的授权类型');
        }

        $this->getAclManager()->setMemberAndActionAcl($member_id, $action_id, $status);

        $result['success'] = true;
        return new JsonModel($result);
    }



    /**
     * 可授权分组列表
     */
    public function departmentsAction()
    {
        // Page information
        $page = (int)$this->params()->fromRoute('key', 1);
        $size = 10;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $deptManager = $this->getDeptManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'departments', 'key' => '%d']));
        $paginationHelper->setCount($deptManager->getDepartmentsCount());

        // Render view data
        $entities = $deptManager->getDepartmentsByLimitPage($page, $size);

        return new ViewModel([
            'entities' => $entities,
            'activeId' => __METHOD__,
        ]);

    }


    /**
     * 分组权限配置
     */
    public function departmentAction()
    {
        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $dept_id = array_shift($params);

        /**
        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止操作基础部门');
            return ;
        }
        //*/

        $dept = $this->getDeptManager()->getDepartment($dept_id);
        if (Department::STATUS_VALID != $dept->getDeptStatus()) {
            throw new InvalidArgumentException('该分组已经无效');
        }

        // Page information
        $page = (int)array_shift($params);
        if ($page < 1) { $page = 1; }
        $size = 4;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $componentManager = $this->getComponentManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'department', 'key' =>  $dept_id . '_%d']));
        $paginationHelper->setCount($componentManager->getComponentsCount());

        $components = $componentManager->getComponentsByLimitPage($page, $size);

        $rows = $this->getAclManager()->getDepartmentAndActionAllAclByDepartment($dept_id);
        $acl = [];
        foreach ($rows as $row) {
            if ($row instanceof AclDepartment) {
                $acl[$row->getAction()] = $row;
            }
        }

        return new ViewModel([
            'dept' => $dept,
            'components' => $components,
            'acl' => $acl,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * 分配分组权限
     */
    public function departmentDispatchAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $dept_id = (string)array_shift($params);

        /**
        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止配置基础部门权限');
            return ;
        }
        //*/

        $dept = $this->getDeptManager()->getDepartment($dept_id);
        if (Department::STATUS_VALID != $dept->getDeptStatus()) {
            throw new InvalidArgumentException('该分组已经无效');
        }

        $action_id = (string)array_shift($params);
        $this->getComponentManager()->getAction($action_id);

        $status = (int)array_shift($params);
        $list = AclDepartment::getAclStatusList();
        if (!array_key_exists($status, $list)) {
            throw new InvalidArgumentException('非法的授权类型');
        }

        $this->getAclManager()->setDepartmentAndActionAcl($dept_id, $action_id, $status);

        $result['success'] = true;
        return new JsonModel($result);
    }


    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '系统权限管理', 'admin/acl', 1, 'cogs', 2);

        $item['actions']['members'] = self::CreateActionRegistry('members', '可授权用户列表', 1, 'user', 9);
        $item['actions']['departments'] = self::CreateActionRegistry('departments', '可授权分组列表', 1, 'users', 19);

        $item['actions']['member'] = self::CreateActionRegistry('member', '个人权限配置', 0, null, 8);
        $item['actions']['member-dispatch'] = self::CreateActionRegistry('member-dispatch', '分配个人权限', 0, null, 7);

        $item['actions']['department'] = self::CreateActionRegistry('department', '分组权限配置', 0, null, 18);
        $item['actions']['department-dispatch'] = self::CreateActionRegistry('department-dispatch', '分配分组权限', 0, null, 17);

        return $item;
    }

}