<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbit-bulk-dispatching-request', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('sagittarius-a', 'fanout', durable: true, auto_delete: false);

$channel->queue_declare('sales.information-request-bulk-dispatching', false, true, false, false);

$channel->queue_bind(
    'internal-training.students-academic-synchronization.student_enrolled',
    'sagittarius-a'
);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (AMQPMessage $msg) {
  echo ' [x] Received ', $msg->body,"\n";
  $msg->ack();    // Marks the message as delivered (I've consumed it), so it won't be delivered (consumed) again.
};

$channel->basic_consume(
    'internal-training.students-academic-synchronization.student_enrolled',
    '',
    false,
    false,
    false,
    false,
    $callback);

while ($channel->is_open()) {
    $channel->wait();
}