<?php
date_default_timezone_set("Asia/Bangkok");

require_once("config.php");

if (PHP_MAJOR_VERSION >= 7) {
    set_error_handler(function ($errno, $errstr) {
       return strpos($errstr, 'Declaration of') === 0;
    }, E_WARNING);
}

  if(!defined('CORE')){
    exit("Access denied");
  }
  require '../src/Medoo.php';

  use Medoo\Medoo;

class Core extends Medoo {
  public $database;

  public function __construct() {
    $this->database = new Medoo([
    // required
      'database_type' => 'mysql',
      'database_name' => constant("DBNAME"),
      'server' =>  constant("DBHOST"),
      'username' =>  constant("DBUSER"),
      'password' =>  constant("DBPASS"),
      'charset' => 'utf8',
    ]);
  }

    public function logging_in($username,$password) {
      if ($this->database->has("member", [
      	"AND" => [
      		"username" => "$username",
      		"password" => md5("$password")
      	]
      ]))
      {
        $newSession = md5(strtotime("now")."|".md5($password));
        $this->database->update("member",["key_remember"=>$newSession],["username"=>$username]);
      	return json_encode(["status"=>"true","session"=>$newSession,"username"=>$username]);
      }
      else
      {
      	return "false";
      }

    }

    public function loadNode() {
      $datas = $this->database->query("SELECT node_active.id,node_active.name,node_active.status,mcu.ip,pin.pin_id FROM node_active LEFT JOIN mcu ON mcu.id = node_active.mcu_id LEFT JOIN pin ON pin.id = node_active.pin_id")->fetchAll();;
      return json_encode($datas);
    }

    public function loadPin($node) {
      $datas = $this->database->select("pin","*",["node_id"=>$node]);
      return json_encode($datas);
    }

    public function updateMCU($data) {
      $chkErr = true;
      $test = "";
      foreach ($data->mcu as $key => $value) {
        if(isset($value->id)) {
          if(isset($value->delete) && $value->delete == true) {
            $result = $this->database->query("DELETE FROM `pin` WHERE `node_id` = $value->id");
            if($result) {
              $chkErr = $this->database->delete("mcu", ["id"=>$value->id]);
            }
            continue;
          }
          $chkErr = $this->database->update("mcu",
          [
            "name" => $value->name,
            "ip"=> $value->ip,
            "update_time" => date('Y-m-d H:i:s')
          ]
          ,["id"=> $value->id]);
        }else {
          $this->database->insert("mcu",
          [
            "name" => $value->name,
            "ip"=> $value->ip,
            "update_time" => date('Y-m-d H:i:s')
          ]);
          $i = 0;
          $idLastest = $this->database->id();
          while($i < 8) {
            $this->database->insert("pin",
            [
              'pin_id'=> $i,
              'node_id'=> $idLastest,
              'status' => 0,
              'switch' => 1
          ]);
          $i++;
          }
        }
        // $test .= $value->id;

      }
      // return json_encode($idLastest);
      if(!$chkErr) {
        return "false";
      }
      return "true";
    }

    public function loadMCU() {
      $datas = $this->database->select("mcu","*");
      return json_encode($datas);
    }

    public function update($data) {
      // return json_encode($data);
      $finish = 0;
      foreach ($data->node as $key => $value) {
        $this->database->update("node_active",["name"=>$value->name],["id"=>$value->id]);
      }

      return "true";
    }

    public function getSession($session) {
      if ($this->database->has("member", ["key_remember" => "$session"]))
      {
        return "true";
      }
      else
      {
      	return "false";
      }
    }

    public function removeNode($node_id) {
      $chkErr = $this->database->delete("node_active", ["id"=>$node_id]);
      if(!$chkErr) {
        return "false";
      }
      return "true";
    }

    public function switchNode($item) {
      $item->status = !$item->status;
      $chkErr = $this->database->update("node_active",["status" => $item->status],["id"=>$item->id]);
      if(!$chkErr) {
        return "false";
      }
      return "true";
      // return json_encode($item->status);
    }

    public function addNode($name) {
      $chkErr = true;
      $number = $this->database->select("pin","id",["status[!]" => 1]);
      $id = $number[array_rand($number,1)];

      $mcu_id = $this->database->get("pin","node_id",["id[=]" => $id]);

      $chkErr = $this->database->insert("node_active",[
        "name" => $name,
        "status" => 1,
        "mcu_id" => $mcu_id,
        "pin_id" => $id
      ]);


      return json_encode($mcu_id);

    }

    function getMemberID($user) {
      $userID = $this->database->get("member",[ 'id'] ,[
        	"username" => $user
        ]);
        return $userID;

    }

    function changePassword($data) {
      if($data->password->new != $data->password->renew) {
        echo "NOT_MATCH";
        return;
      }else if($data->password->new ==  "" || $data->password->renew == "") {
        echo "NOT_NULL";
        return;
      }else {
        $password = md5($data->password->old);
        if ($this->database->has("member", [
          	"AND" => [
              "username" => $data->username,
          		"password" => $password
          	]
          ])) {
            $data = $this->database->update("member", [
              	"password" => md5($data->password->new)
              ], [
              	"id" => $this->getMemberID($data->username)
              ]);
              if($data == true) {
                echo "SUCCESS";
              }else {
                echo "ERROR";
              }
          } else {
          	echo "Password error.";
          }
      }
    }


  }


// echo json_encode($core->logging_in("Admin","123"));





?>
