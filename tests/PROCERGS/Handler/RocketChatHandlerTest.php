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
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Logger;

/**
 * RocketChatHandler uses cURL to trigger Rocket.Chat WebHooks
 *
 * @package PROCERGS\Handler
 */
class RocketChatHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorMinimal()
    {
        $requests = [];
        $client = $this->getClient($requests);
        $record = $this->getRecord(Logger::ERROR);

        $handler = new RocketChatHandler('https://my.url', null, null, $client);
        $handler->handle($record);

        /** @var Request $request */
        $request = reset($requests)['request'];
        $body = $request->getBody();
        $requestData = json_decode($body);

        $this->assertNotEmpty($requests);
        $this->assertObjectNotHasAttribute('username', $requestData);
        $this->assertObjectNotHasAttribute('channel', $requestData);
        $this->assertObjectHasAttribute('text', $requestData);
    }

    public function testConstructorComplete()
    {
        $requests = [];
        $client = $this->getClient($requests);
        $record = $this->getRecord(Logger::ERROR);

        $handler = new RocketChatHandler('https://my.url', 'channel', 'user', $client, Logger::DEBUG, false);
        $handler->handle($record);

        /** @var Request $request */
        $request = reset($requests)['request'];
        $body = $request->getBody();
        $requestData = json_decode($body);

        $this->assertNotEmpty($requests);
        $this->assertObjectHasAttribute('username', $requestData);
        $this->assertObjectHasAttribute('channel', $requestData);
        $this->assertObjectHasAttribute('text', $requestData);
    }

    private function getClient(&$container)
    {
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(200),
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $client = new Client(['handler' => $stack]);

        return $client;
    }

    /**
     * @return array Record
     */
    private function getRecord($level = Logger::WARNING, $message = 'test', $context = array())
    {
        return array(
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => array(),
        );
    }
}
