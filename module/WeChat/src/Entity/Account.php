<?php
/**
 * Account.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Entity;


use Admin\Entity\Member;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Account
 * @package WeChat\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="wechat_account")
 */
class Account
{

    const STATUS_UNCHECK = 0;
    const STATUS_CHECKED = 1;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="wx_id", type="integer")
     */
    private $wxId;

    /**
     * @var string
     *
     * @ORM\Column(name="wx_appid", type="string", length=45, nullable=false)
     */
    private $wxAppId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="wx_appsecret", type="string", length=255, nullable=false)
     */
    private $wxAppSecret = '';

    /**
     * @var string
     *
     * @ORM\Column(name="wx_access_token", type="string", length=512)
     */
    private $wxAccessToken = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="wx_access_token_expired", type="integer")
     */
    private $wxAccessTokenExpired = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="wx_jsapi_ticket", type="string", length=512)
     */
    private $wxJsapiTicket = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="wx_jsapi_ticket_expired", type="integer")
     */
    private $wxJsapiTicketExpired = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="wx_card_ticket", type="string", length=512)
     */
    private $wxCardTicket = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="wx_card_ticket_expired", type="integer")
     */
    private $wxCardTicketExpired = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="wx_expired", type="integer")
     */
    private $wxExpired = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="wx_checked", type="integer")
     */
    private $wxChecked = self::STATUS_UNCHECK;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="wx_created", type="datetime")
     */
    private $wxCreated;


    /**
     * @var Member
     *
     * @ORM\OneToOne(targetEntity="Admin\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="member_id")
     */
    private $member;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WeChat\Entity\Client", mappedBy="weChat")
     */
    private $clients;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WeChat\Entity\Tag", mappedBy="weChat")
     */
    private $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WeChat\Entity\Menu", mappedBy="weChat")
     */
    private $menus;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WeChat\Entity\QrCode", mappedBy="weChat")
     */
    private $qrCodes;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WeChat\Entity\Order", mappedBy="weChat")
     */
    private $orders;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WeChat\Entity\Invoice", mappedBy="weChat")
     */
    private $invoices;



    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->menus = new ArrayCollection();
        $this->qrCodes = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getWxId()
    {
        return $this->wxId;
    }

    /**
     * @param int $wxId
     */
    public function setWxId($wxId)
    {
        $this->wxId = $wxId;
    }

    /**
     * @return string
     */
    public function getWxAppId()
    {
        return $this->wxAppId;
    }

    /**
     * @param string $wxAppId
     */
    public function setWxAppId($wxAppId)
    {
        $this->wxAppId = $wxAppId;
    }

    /**
     * @return string
     */
    public function getWxAppSecret()
    {
        return $this->wxAppSecret;
    }

    /**
     * @param string $wxAppSecret
     */
    public function setWxAppSecret($wxAppSecret)
    {
        $this->wxAppSecret = $wxAppSecret;
    }

    /**
     * @return string
     */
    public function getWxAccessToken()
    {
        return $this->wxAccessToken;
    }

    /**
     * @param string $wxAccessToken
     */
    public function setWxAccessToken($wxAccessToken)
    {
        $this->wxAccessToken = $wxAccessToken;
    }

    /**
     * @return int
     */
    public function getWxAccessTokenExpired()
    {
        return $this->wxAccessTokenExpired;
    }

    /**
     * @param int $wxAccessTokenExpired
     */
    public function setWxAccessTokenExpired($wxAccessTokenExpired)
    {
        $this->wxAccessTokenExpired = $wxAccessTokenExpired;
    }

    /**
     * @return string
     */
    public function getWxJsapiTicket()
    {
        return $this->wxJsapiTicket;
    }

    /**
     * @param string $wxJsapiTicket
     */
    public function setWxJsapiTicket($wxJsapiTicket)
    {
        $this->wxJsapiTicket = $wxJsapiTicket;
    }

    /**
     * @return int
     */
    public function getWxJsapiTicketExpired()
    {
        return $this->wxJsapiTicketExpired;
    }

    /**
     * @param int $wxJsapiTicketExpired
     */
    public function setWxJsapiTicketExpired($wxJsapiTicketExpired)
    {
        $this->wxJsapiTicketExpired = $wxJsapiTicketExpired;
    }

    /**
     * @return string
     */
    public function getWxCardTicket()
    {
        return $this->wxCardTicket;
    }

    /**
     * @param string $wxCardTicket
     */
    public function setWxCardTicket($wxCardTicket)
    {
        $this->wxCardTicket = $wxCardTicket;
    }

    /**
     * @return int
     */
    public function getWxCardTicketExpired()
    {
        return $this->wxCardTicketExpired;
    }

    /**
     * @param int $wxCardTicketExpired
     */
    public function setWxCardTicketExpired($wxCardTicketExpired)
    {
        $this->wxCardTicketExpired = $wxCardTicketExpired;
    }

    /**
     * @return int
     */
    public function getWxExpired()
    {
        return $this->wxExpired;
    }

    /**
     * @param int $wxExpired
     */
    public function setWxExpired($wxExpired)
    {
        $this->wxExpired = $wxExpired;
    }

    /**
     * @return int
     */
    public function getWxChecked()
    {
        return $this->wxChecked;
    }

    /**
     * @param int $wxChecked
     */
    public function setWxChecked($wxChecked)
    {
        $this->wxChecked = $wxChecked;
    }

    /**
     * @return \DateTime
     */
    public function getWxCreated()
    {
        return $this->wxCreated;
    }

    /**
     * @param \DateTime $wxCreated
     */
    public function setWxCreated($wxCreated)
    {
        $this->wxCreated = $wxCreated;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @return ArrayCollection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * @param ArrayCollection $clients
     */
    public function setClients($clients)
    {
        $this->clients = $clients;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return ArrayCollection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * @param ArrayCollection $menus
     */
    public function setMenus($menus)
    {
        $this->menus = $menus;
    }

    /**
     * @return ArrayCollection
     */
    public function getQrCodes()
    {
        return $this->qrCodes;
    }

    /**
     * @param ArrayCollection $qrCodes
     */
    public function setQrCodes($qrCodes)
    {
        $this->qrCodes = $qrCodes;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return ArrayCollection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @param ArrayCollection $invoices
     */
    public function setInvoices($invoices)
    {
        $this->invoices = $invoices;
    }

}