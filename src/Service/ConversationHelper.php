<?php

namespace App\Service;

use App\Entity\Flowxo\Conversation;
use App\FlowXo\FlowXoClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ConversationHelper
{
    private $count = 0;
    private FlowXoClient $flowXoClient;

    public function __construct(FlowXoClient  $flowXoClient)
    {
        $this->flowXoClient = $flowXoClient;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return array|Conversation
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function fetchMany(int $limit = null, int $offset = null, array $criteria = []) {

        $url = sprintf('https://flowxo.com/api/conversations/?skip=%s&limit=%s', $offset, $limit);

        if (array_key_exists('from', $criteria) && $criteria['from']) {
            $url .= sprintf('&start=%s', $criteria['from']->format('Y-m-d'));
        }

        if (array_key_exists('to', $criteria) && $criteria['to']) {
            $url .= sprintf('&end=%sT23:59:59', $criteria['to']->format('Y-m-d'));
        }

        $response = $this->flowXoClient->request('GET', $url);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $content = json_decode($response->getContent());

            $apiConversations = $content->conversations;
            $this->count = $content->total;

            $conversations = [];

            foreach ($apiConversations as $apiConversation) {
                $conversation = new Conversation();
                $date = date_format(new \DateTime($apiConversation->created_at),"Y/m/d");
                $conversation
                    ->setId($apiConversation->id)
                    ->setIdUrlEncoded($apiConversation->id_url_encoded)
                    ->setBotId($apiConversation->bot_id)
                    ->setBotName($apiConversation->bot_name)
                    ->setPlatform($apiConversation->platform)
                    ->setUserId($apiConversation->user_id)
                    ->setUserName((string)$apiConversation->user_name)
                    ->setCreatedAt(new \DateTime($apiConversation->created_at))
                    ->setDate($date);

                try {
                    $conversation->setCreatedAt(new \DateTime($apiConversation->created_at));
                } catch (\Exception $e) {
                }

                $conversations[] = $conversation;
            }



            return $conversations;
        } else {
            return [];
        }
    }

    public function count(): int
    {
        return $this->count;
    }
}