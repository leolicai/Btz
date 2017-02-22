<?php
/**
 * FeedbackController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\Feedback;
use Admin\Entity\Member;
use Admin\Form\FeedbackForm;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Doctrine\ORM\NonUniqueResultException;
use Ramsey\Uuid\Uuid;
use Zend\Mvc\Exception\RuntimeException;
use Zend\View\Model\ViewModel;

class FeedbackController extends AdminBaseController
{


    /**
     * Member personal feedback list
     *
     * @return ViewModel
     */
    public function indexAction()
    {

        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) {
            $page = 1;
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        $feedbackManager = $this->getFeedbackManager();

        $size = 1;
        $count = $feedbackManager->getMemberFeedbackCount($myself);

        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/feedback', ['action' => 'index', 'key' => '%d']));

        $rows = $feedbackManager->getMemberFeedbackByLimitPage($myself, $page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Add new feedback
     */
    public function addAction()
    {
        $form = new FeedbackForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $myself = $this->getMemberManager()->getCurrentMember();
                $feedback = new Feedback();
                $feedback->setId(Uuid::uuid1()->toString());
                $feedback->setContent($data['content']);
                $feedback->setCreated(new \DateTime());
                $feedback->setUpdated(new \DateTime());
                $feedback->setReply('');
                $feedback->setSender($myself);
                $feedback->setReplier($myself);
                $this->getFeedbackManager()->saveModifiedEntity($feedback);

                return $this->getMessagePlugin()->show(
                    '反馈已接收',
                    '感谢您宝贵的反馈意见! 我们已将您的意见分发到相关的负责人, 他们收到后会尽快进行回应. 谢谢!',
                    $this->url()->fromRoute('admin/feedback'),
                    '返回',
                    3
                );
            }
        }


        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Cancel self feedback
     */
    public function cancelAction()
    {
        $feedbackId = (string)$this->params()->fromRoute('key');

        $feedbackManager = $this->getFeedbackManager();
        $feedback = $feedbackManager->getFeedback($feedbackId);

        if (!$feedback instanceof Feedback) {
            throw new \Exception('反馈的信息编号失效了!');
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        if (!($myself instanceof Member) || $myself->getMemberId() != $feedback->getSender()->getMemberId()) {
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        $feedbackManager->removeEntity($feedback);

        return $this->getMessagePlugin()->show(
            '反馈已删除',
            '您已经删除您的反馈意见! 如果有任何想法, 请让我们知道. 谢谢!',
            $this->url()->fromRoute('admin/feedback'),
            '返回',
            3
        );
    }


    /**
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '建议反馈', 'admin/feedback', 1, 'commenting-o', 12);

        $item['actions']['index'] = self::CreateActionRegistry('index', '我的反馈', 1, 'comments-o', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '发起反馈', 1, 'comment-o', 1);

        $item['actions']['cancel'] = self::CreateActionRegistry('cancel', '删除反馈');

        return $item;
    }


}