# Rocket.Chat Monolog Handler

A monolog handler that sends logs to a Rocket.Chat WebHook URL.

``` php
use Monolog\Logger;
use PROCERGS\Handler\RocketChatHandler;

$url = 'https://demo.rocket.chat/hooks/p2FPjabaKRMucOPxV/eyN8tKpRII2H62Z8k189O7DP9nznU1I2xr0DmmkyeWTkQqs0';
$channel = 'my-channel';
$username = 'Some.User';

$logger = new Logger('my_logger');
$logger->pushHandler(new RocketChatHandler($url, $channel, $username));

$logger->crit('Something');
```
