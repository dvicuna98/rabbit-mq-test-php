<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbit-bulk-dispatching-request', 5672, 'guest', 'guest');
$channel = $connection->channel();

//$channel->queue_declare('internal-training.students-academic-synchronization.student_enrolled', false, true, false, false);

$channel->exchange_declare('sagittarius-a', 'fanout', durable: true, auto_delete: false);

$messageHeaders = [
    'message_id' => '282b1c5a-8551-42d6-9eb3-b77e38f2f654',
    'type' => 'sales.applicants-management.information_request_created',
    "app_id"=> "internal-training.students-academic-synchronization",
    'content_type' => 'application/json',
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
];

//b98dcc59-48ff-43c8-9ee1-cb51bb5cafda

$msg = new AMQPMessage('{
  "uuid": "string",
  "fired_at": "2019-08-24T14:15:22Z",
  "information_request": {
    "uuid": "c39ff828-3a2c-42aa-a5cb-399e907d52e3",
    "external_reference_uuid": "b98dcc59-48ff-43c8-9ee1-cb51bb5cafda", 
    "bulk_dispatching_request_uuid": "a2251c37-2a0c-40f3-933b-ef8397768880",
    "applicant": {
      "uuid": "47db2085-a847-4780-a6cb-e29bf8be3298",
      "firstname": "John",
      "lastname": "Doe",
      "emails": [
        "john.doe@funiber.org"
      ]
    },
    "product": {
      "uuid": "47db2085-a847-4780-a6cb-e29bf8be3298",
      "type": "PROGRAM",
      "name": "Master formación empresarial"
    },
    "knowledge_channel": {
      "name": "Emailing"
    },
    "submission_channel": {
      "name": "Phone"
    },
    "status": {
      "uuid": "47db2085-a847-4780-a6cb-e29bf8be3298",
      "name": "Solicita información",
      "abbreviation": "SI",
      "incidence": {
        "uuid": "47db2085-a847-4780-a6cb-e29bf8be3298",
        "name": "Información Enviada",
        "abbreviation": "IE"
      }
    },
    "effective_date": "2019-08-24T14:15:22Z",
    "created_at": "2019-08-24T14:15:22Z"
  }
}',$messageHeaders);

$channel->basic_publish($msg, 'sagittarius-a');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();