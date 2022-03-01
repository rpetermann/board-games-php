<?php

namespace App\Tests\Functional;

use App\Entity\AbstractEntity;
use App\EventSubscriber\DatabaseSubscriber;
use App\Tests\Common\MockTrait;
use App\Tests\Common\AssertTrait;
use Doctrine\Common\EventManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

/**
 * AbstractWebTestCase
 */
abstract class AbstractWebTestCase extends WebTestCase
{
    use MockTrait;
    use AssertTrait;

    const SUBSCRIBERS_TO_DISABLE = [
        DatabaseSubscriber::class,
    ];

    const ENABLE_ACTION = 'enable';
    const DISABLE_ACTION = 'disable';
    const ALLOWED_MANAGE_SUBSCRIBER_ACTIONS = [
        self::ENABLE_ACTION,
        self::DISABLE_ACTION
    ];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->client = $this->createClient();

        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * sendRequest
     *
     * @param  string $method
     * @param  string $url
     * @param  array  $data
     * @param  array  $headers
     * @return void
     */
    protected function sendRequest(string $method, string $url, array $data = [], array $headers = []): void
    {
        $headers = $headers + $this->getDefaultHeaders();

        $this->client->request(
            $method,
            $url,
            [],
            [],
            $headers,
            json_encode($data)
        );
    }

    /**
     * getJsonResponse
     *
     * @return \stdClass[]|\stdClass
     */
    protected function getJsonResponse()
    {
        return json_decode($this->client->getResponse()->getContent());
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function getDataFromFile(string $fileName)
    {
        return json_decode(file_get_contents(__DIR__."/data/".$fileName.".json"), true);
    }

    /**
     * getAccessTokenHeader
     *
     * @param string $accessToken
     * @return array
     */
    protected function getAccessTokenHeader(string $accessToken): array
    {
        return [
            'HTTP_ACCESS-TOKEN' => $accessToken,
        ];
    }

    /**
     * getDefaultHeaders
     *
     * @return array
     */
    protected function getDefaultHeaders(): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json', 
        ];
    }

    /**
     * save
     *
     * @param AbstractEntity $entity
     * @return void
     */
    protected function save(AbstractEntity $entity): void
    {
        $this->manageSubscribers(self::DISABLE_ACTION);

        $this->em->persist($entity);
        $this->em->flush();

        $this->manageSubscribers(self::ENABLE_ACTION);
    }

    /**
     * manageSubscribers
     *
     * @param string $action
     * @return void
     */
    protected function manageSubscribers(string $action): void
    {
        if (!in_array($action, self::ALLOWED_MANAGE_SUBSCRIBER_ACTIONS)) {
            return;
        }

        foreach (self::SUBSCRIBERS_TO_DISABLE as $subscriber) {
            $subscriberService = $this->getContainer()->get($subscriber);

            'disable' === $action ? $this->removeEventSubscriber($subscriberService) : $this->addEventSubscriber($subscriberService);
        }
    }

    /**
     * removeEventSubscriber
     *
     * @param EventSubscriberInterface $subscriber
     * @return void
     */
    protected function removeEventSubscriber(EventSubscriberInterface $subscriber): void
    {
        $eventManager = $this->getEventManager();

        $eventManager->removeEventSubscriber($subscriber);
    }

    /**
     * addEventSubscriber
     *
     * @param EventSubscriberInterface $subscriber
     * @return void
     */
    protected function addEventSubscriber(EventSubscriberInterface $subscriber): void
    {
        $eventManager = $this->getEventManager();

        $eventManager->addEventSubscriber($subscriber);
    }

    /**
     * getEventManager
     *
     * @return EventManager
     */
    protected function getEventManager(): EventManager
    {
        return $this->em->getEventManager();
    }
}
