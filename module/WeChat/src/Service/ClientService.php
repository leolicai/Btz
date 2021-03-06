<?php
/**
 * ClientService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service;


use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Entity\Client;
use WeChat\Exception\InvalidArgumentException;


class ClientService extends BaseEntityService
{

    /**
     * @param string $clientId
     * @return Client
     * @throws InvalidArgumentException
     */
    public function getWeChatClient($clientId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Client::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $clientId);

        $client = $this->getEntityFromPersistence();
        if (!$client instanceof Client) {
            throw new InvalidArgumentException("无效的客户端 ID: " . $clientId);
        }
        return $client;
    }


    /**
     * @param Account $weChat
     * @param string $identifier
     * @return Client
     */
    public function getWeChatClientByWeChatAndIdentifier(Account $weChat, $identifier)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Client::class, 't');
        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('t.weChat', '?1'),
            $qb->expr()->eq('t.identifier',  '?2')
        ));
        $qb->setParameter(1, $weChat)->setParameter(2, $identifier);

        $client = $this->getEntityFromPersistence();
        if (!$client instanceof Client) {
            throw new InvalidArgumentException("无效的客户端 identifier: " . $identifier);
        }
        return $client;

    }


    /**
     * @param Account $weChat
     * @return int
     */
    public function getClientCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Client::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        return $this->getEntitiesCount();
    }


    /**
     * @param Account $weChat
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getClientsWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Client::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.expireTime', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * @param Account $weChat
     * @param string $name
     * @param string $domain
     * @param string $ip
     * @param string $api_list
     * @param integer $start
     * @param integer $end
     */
    public function createWeChatClient($weChat, $name, $domain, $ip, $api_list, $start, $end)
    {
        $client = new Client();
        $client->setId(Uuid::uuid1()->toString());
        $client->setName($name);
        $client->setIdentifier(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890'), 0, 6));
        $client->setDomain($domain);
        $client->setIp($ip);
        $client->setApiList($api_list);
        $client->setActiveTime($start);
        $client->setExpireTime($end);
        $client->setCreated(new \DateTime());
        $client->setWeChat($weChat);

        $this->saveModifiedEntity($client);
    }



}