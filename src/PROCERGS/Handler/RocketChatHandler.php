<?php

/**
 * This file is part of the RocketChat Monolog Handler
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Handler;

use GuzzleHttp\Client;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use GuzzleHttp\ClientInterface;

/**
 * RocketChatHandler uses cURL to trigger Rocket.Chat WebHooks
 *
 * @package Monolog\Handler
 */
class RocketChatHandler extends AbstractProcessingHandler
{
    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $channel;

    /** @var string */
    private $username;

    /** @var string */
    private $webHookUrl;

    /**
     * @param string $channel The name of the channel where the logs should be posted
     * @param string $username The username to be displayed
     * @param string $webHookUrl The WebHook URL obtained from Rocket.Chat
     * @param ClientInterface $client The Guzzle HTTP Client that will be used for the request
     * @param int $level The minomum logging level ar which this handler will be triggered
     * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(
        $webHookUrl,
        $channel = null,
        $username = null,
        ClientInterface $client = null,
        $level = Logger::ERROR,
        $bubble = true
    ) {
        if (!$client instanceof ClientInterface) {
            $client = new Client();
        }

        $this->channel = $channel;
        $this->username = $username;
        $this->webHookUrl = $webHookUrl;
        $this->client = $client;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $formattedMessage = sprintf(
            "Log channel: *%s*\nLog level: *%s*\n```%s```",
            $record['channel'], $record['level_name'], $record['message']
        );

        $postData = array_filter([
            'username' => $this->username,
            'icon_emoji' => '',
            'channel' => $this->channel,
            'text' => $formattedMessage,
        ]);

        $this->client->post($this->webHookUrl, ['json' => $postData]);
    }
}
