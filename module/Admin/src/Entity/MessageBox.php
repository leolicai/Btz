<?php
/**
 * MessageBox.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class MessageBox
 * @package Admin\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sys_message_box")
 */
class MessageBox
{

    const STATUS_SENDER_SENT = 1;
    const STATUS_SENDER_DELETED = 0;

    const STATUS_RECEIVER_UNREAD = 0;
    const STATUS_RECEIVER_READ = 1;
    const STATUS_RECEIVER_DELETED = 2;

    const MESSAGE_TYPE_PERSONAL = 0;
    const MESSAGE_TYPE_BROADCAST = 1;


    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="sender", type="integer")
     */
    private $sender = 0;

    /**
     * @var integer
     * @ORM\Column(name="sender_status", type="integer")
     */
    private $senderStatus = self::STATUS_SENDER_SENT;


    /**
     * @var string
     * @ORM\Column(name="sender_name", type="string", length=45)
     */
    private $senderName = '';

    /**
     * @var string
     * @ORM\Column(name="receiver", type="integer")
     */
    private $receiver = 0;

    /**
     * @var integer
     * @ORM\Column(name="receiver_status", type="integer")
     */
    private $receiverStatus = self::STATUS_RECEIVER_UNREAD;

    /**
     * @var string
     * @ORM\Column(name="receiver_name", type="string", length=45)
     */
    private $receiverName = '';

    /**
     * @var integer
     * @ORM\Column(name="type", type="smallint")
     */
    private $type = self::MESSAGE_TYPE_PERSONAL;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @var MessageContent
     *
     * @ORM\ManyToOne(targetEntity="Admin\Entity\MessageContent")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private $content;



    /**
     * @return array
     */
    public static function getReceiverStatusList()
    {
        return [
            self::STATUS_RECEIVER_UNREAD => '未读',
            self::STATUS_RECEIVER_READ => '已读',
            self::STATUS_RECEIVER_DELETED => '已删除',
        ];
    }


    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::MESSAGE_TYPE_BROADCAST => '广播',
            self::MESSAGE_TYPE_PERSONAL => '消息',
        ];
    }


    /**
     * @return string
     */
    public function getReceiverStatusAsString()
    {
        $list = self::getReceiverStatusList();
        if (isset($list[$this->receiverStatus])) {
            return $list[$this->receiverStatus];
        }
        return '未知';
    }


    /**
     * @return string
     */
    public function getTypeAsString()
    {
        $list = self::getTypeList();
        if (isset($list[$this->type])) {
            return $list[$this->type];
        }
        return '未知';
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return int
     */
    public function getSenderStatus()
    {
        return $this->senderStatus;
    }

    /**
     * @param int $senderStatus
     */
    public function setSenderStatus($senderStatus)
    {
        $this->senderStatus = $senderStatus;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param string $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return int
     */
    public function getReceiverStatus()
    {
        return $this->receiverStatus;
    }

    /**
     * @param int $receiverStatus
     */
    public function setReceiverStatus($receiverStatus)
    {
        $this->receiverStatus = $receiverStatus;
    }

    /**
     * @return string
     */
    public function getReceiverName()
    {
        return $this->receiverName;
    }

    /**
     * @param string $receiverName
     */
    public function setReceiverName($receiverName)
    {
        $this->receiverName = $receiverName;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return MessageContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param MessageContent $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

}