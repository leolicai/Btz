<?php
/**
 * WeChatService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat;


use Admin\WeChat\Exception\ExpiredException;
use Admin\WeChat\Exception\InvalidArgumentException;
use Admin\WeChat\Exception\RuntimeException;
use Application\Service\AppLogger;


class WeChatService
{

    /**
     * @var integer
     */
    private $wxId;

    /**
     * @var Local
     */
    private $local;

    /**
     * @var AppLogger
     */
    private $logger;


    public function __construct($wxId, Local $local, AppLogger $logger)
    {
        $this->wxId = $wxId;
        $this->local = $local;
        $this->logger = $logger;
    }


    /**
     * 获取微信基础 Access Token
     *
     * @return string
     */
    public function getAccessToken()
    {
        try {
            return $this->local->getAccessToken($this->wxId);
        } catch (InvalidArgumentException $e) {
            $this->logger->excaption($e);
        } catch (ExpiredException $e) {
            $this->logger->excaption($e);
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
        }

        return '';
    }


    /**
     * 查询微信服务器 IP 地址
     *
     * @return array
     */
    public function getCallbackHosts()
    {
        try {
            return $this->local->getCallbackHosts($this->wxId);
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
        }

        return [];
    }


    /**
     * 生成带参数微信二维码
     *
     * @param string $type
     * @param string|int $scene
     * @param int $expired
     * @return array
     */
    public function getQrCode($type, $scene, $expired = 0)
    {
        try {
            return $this->local->createQrCode($this->wxId, $type, $scene, $expired);
        } catch (RuntimeException $e) {
            $this->logger->excaption($e);
        }
        return [];
    }





}