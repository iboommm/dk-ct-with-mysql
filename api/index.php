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


  Flight::start();

 ?>
