<?php
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
      	return "true";
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


  }


// echo json_encode($core->logging_in("Admin","123"));





?>
