<?php

  define('CORE', '1');

  require '../src/flight/Flight.php';
  require '../lib/core.php';



  Flight::route('POST /login', function(){
      $rawData = file_get_contents("php://input");
      $encode = json_decode($rawData);
      $core = new Core();
      echo $core->logging_in($encode->username,$encode->password);
  });

  Flight::route('POST /node', function(){
      $core = new Core();
      echo $core->loadNode();
  });

  Flight::route('POST /pin', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->loadPin($encode->node);
  });

  Flight::route('POST /update', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->update($encode);
  });

  Flight::route('POST /session', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->getSession($encode->session);
  });

  Flight::route('POST /addNode', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->addNode($encode->name);
  });

  Flight::route('POST /removeNode', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->removeNode($encode->node_id);
  });

  Flight::route('POST /switch', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->switchNode($encode->item);
  });

  Flight::route('POST /mcu', function(){
    $core = new Core();
    echo $core->loadMCU();
  });

  Flight::route('POST /updateMachine', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->updateMCU($encode);
  });

  Flight::route('POST /changePassword', function(){
    $rawData = file_get_contents("php://input");
    $encode = json_decode($rawData);
    $core = new Core();
    echo $core->changePassword($encode);
  });




  Flight::start();

 ?>
