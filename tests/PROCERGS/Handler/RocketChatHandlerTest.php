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
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Monolog\Logger;
use Monolog\TestCase;

/**
 * RocketChatHandler uses cURL to trigger Rocket.Chat WebHooks
 *
 * @package Monolog\Handler
 */
class RocketChatHandlerTest extends TestCase
{
    public function testConstructorMinimal()
    {
        $requests = [];
        $client = $this->getClient($requests);
        $record = $this->getRecord();

        $handler = new RocketChatHandler('https://my.url', null, null, $client);
        $handler->handle($record);

        $this->assertNotEmpty($requests);
    }

    public function testConstructorComplete()
    {
        new RocketChatHandler('https://my.url', 'channel', 'user', new Client(), Logger::DEBUG, false);
    }

    private function getClient(&$container)
    {
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        $stack->push($history);

        $client = new Client(['handler' => $stack]);

        return $client;
    }
}
