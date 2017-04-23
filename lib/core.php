<?php

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
      'database_name' => 'dk_ct',
      'server' => 'localhost',
      'username' => 'root',
      'password' => '',
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
      $datas = $this->database->select("node","*");
      return json_encode($datas);
    }

    public function loadPin($node) {
      $datas = $this->database->select("pin","*",["node_id"=>$node]);
      return json_encode($datas);
    }
    public function update($data) {
      // return json_encode($data);
      $finish = 0;
      foreach ($data->node as $key => $value) {
        $this->database->update("node",["name"=>$value->name,"ip"=>$value->ip,"update_time"=> strtotime("now")],["id"=>$value->id]);
        foreach ($data->pin[$value->id] as $key => $value) {
          $this->database->update("pin",["status"=>$value->status,"switch"=>$value->switch],["id"=>$value->id]);
        }
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
      $chkErr = $this->database->delete("pin", ["node_id"=>$node_id]);
      if(!$chkErr) {
        return "false";
      }
      $chkErr = $this->database->delete("node", ["id"=>$node_id]);
      if(!$chkErr) {
        return "false";
      }
      return "true $node_id";
    }

    public function addNode($name,$ip,$number) {
      $chkErr = true;
      $chkErr = $this->database->insert("node", [
      	"name" => "$name",
      	"ip" => "$ip"
      ]);
      if(!$chkErr) {
        return "false";
      }
      $node_id = $this->database->id();
      $i=0;
      while($i < $number) {
        $chkErr = $this->database->insert("pin", [
          "pin_id" => $i++,
          "node_id"=> $node_id,
          "status"=> 0,
          "switch"=> 0,
        ]);
        if(!$chkErr) {
          return "false";
        }
      }
      return "true";
    }


  }


// echo json_encode($core->logging_in("Admin","123"));





?>
