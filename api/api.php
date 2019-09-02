<?php
session_start();

require_once 'vendor/autoload.php';
use Medoo\Medoo;

// function keySearch($array,$finds){
// 	$arr = [];
// 	foreach($finds as $find)
// 	{
// 		foreach ($array as $key => $value) {
// 			if($key == $find)
// 				$arr[$key] = $value;
// 		}
// 	}
// 	return $arr;
// }

Class Database{
	private $database;
	public function __construct(){
		$this->database = new Medoo([
			'database_type' => 'mysql',
			'database_name' => 'century',
			'server' => 'localhost',
			'username' => 'root',
			'password' => '',
		
			'charset' => 'utf8mb4_unicode_520_ci',
			'collation' => 'utf8mb4_unicode_520_ci',
		]);
	}
	public function query(){
		$a = func_get_args();
		$type = array_shift($a);
		$data = null;
		switch($type){
			case 'select':
				$data = $this->database->select(...$a);
				break;
			case 'insert':
				$data = $this->database->insert(...$a);
				break;
			case 'update':
				$data = $this->database->update(...$a);
				break;
			case 'delete':
				$data = $this->database->delete(...$a);
				break;
			case 'replace':
				$data = $this->database->replace(...$a);
				break;
			case 'get':
				$data = $this->database->get(...$a);
				break;
			case 'has':
				$data = $this->database->has(...$a);
				break;
			case 'rand':
				$data = $this->database->rand(...$a);
				break;
			default:
				throw new Exception('query type errror');
		}

		if($this->database->error()[2])
			throw new Exception($this->database->error()[2]);
		
		return $data;
	}
}

Class User{
	// private $id;

	// private $tradeCards;

	// public function __construct($id){

	// 	$this->id = $id;
	// 	$this->spectator = true;
	// }

	public static function register(Database $db,string $login,string $password,string $email){
		/*Sprawdza login*/
		if(!(
			($login) &&
			(strlen($login) >= 5) &&
			(strlen($login) <= 10)
		))throw new Exception("Login rules error");

		/*Sprawdza haslo*/
		if(!(
			($password) &&
			(strlen($password) >= 5)
		))throw new Exception("Password rules error");
		$password =  password_hash($password,PASSWORD_DEFAULT);

		/*Sprawdza email*/
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			throw new Exception("Email rules error");

		/**Czy istnieje login*/
		if($db->query("get","users",['id'],["login" => $login]))
			throw new Exception("such login already exists");

		/**Czy istnieje email*/
		if($db->query("get","users",['id'],["email" => $email]))
			throw new Exception("such email already exists");

		/*Wstaw do bazy*/
		$db->query("insert","users", [
			"login" => $this->login,
			"email" => $this->email,
			"password" => $this->password
		]);
	}

	public static function login(Database $db,string $login,string $password){
		/**Czy istnieje login*/
		if($db->query("get","users",['id'],["login" => $login]))
				throw new Exception("such login does not exists");

		$data = $db->query("get","users",['id','email','password'],["login" => $login]);

		if(!password_verify($password,$data['password']))
			throw new Exception("wrong password");
		
		$_SESSION['id'] = $data['id'];

		return [
			'login'=>$login,
			'email'=>$data['email']
		];
	}
}


Class Room{
	
	private $id;
	private $name;

	private $tradeCards;
	private $startTradeCards;
	private $wealthCards;
	private $users;
	private $chat;

	private $turn;

	private $db;
	public function __construct(Database $db,$opt=null){
		$this->db = $db;

		$this->users = [];
		$this->chat = [];
		$this->turn = 0;

		if(is_string($opt))
			$this->create($opt);
		else if(is_numeric($opt))
			$this->load($opt);
	}

	public function create($name){

		$this->wealthCards = $this->db->query('select','wealth_cards',['id','take_yellow','take_green','take_blue','take_red','points','src']);

		$this->tradeCards = $this->db->query('select','trade_cards',[
			'id',
			'take_yellow','give_yellow',
			'take_green','give_green',
			'take_blue','give_blue',
			'take_red','give_red',
			'upgrade',
			'src'
		],['start'=>0]);

		$this->startTradeCards = $this->db->query('select','trade_cards',[
			'id',
			'take_yellow','give_yellow',
			'take_green','give_green',
			'take_blue','give_blue',
			'take_red','give_red',
			'upgrade',
			'src'
		],['start[!]'=>0]);

		$this->chat [] = [
			'time'=>$this->time(),
			'owner'=>'root',
			'message'=>'Pokój został utworzony'
		];

		$this->db->query("insert","rooms",[
			'name'=>$name,
			'data'=>$this->packData()
		]);
	}

	private function packData(){
		return json_encode([
			'wealthCards'=>$this->wealthCards,
			'tradeCards'=>$this->tradeCards,
			'startTradeCards'=>$this->startTradeCards,
			'users'=>$this->users,
			'chat'=>$this->chat,
			'turn'=>$this->turn,
		]);
	}

	private function unpackData($data){
		$d = json_decode($data);
		$this->wealthCards = $d->wealthCards;
		$this->tradeCards = $d->tradeCards;
		$this->startTradeCards = $d->startTradeCards;
		$this->users = (array) $d->users;
		$this->chat = (array) $d->chat;
		$this->turn = $d->turn;
	}

	private function update(){
		$this->db->query("update","rooms",['data'=>$this->packData()],["id" => $this->id]);
	}
	
	public function load($id){
		$this->id = $id;

		$data = $this->db->query("get","rooms",['id','name','data'],["id" => $this->id]);

		$this->name = $data['name'];
		$this->unpackData($data['data']);
	}
	
	private function exist($id):int{
		foreach (($this->users) as $key => $value){
			if($value->id == $id)
				return $key;
		}
		return -1;
	}

	private function time(){
		$time = new DateTime('now',new DateTimeZone('UTC'));
		return $time->format('Y-m-d H:i:s T');
	}

	public function joinRoom($id){
		if($this->exist($id) == -1)
		{
			$this->users []= [
				'id'=>$id,
				'tradeCards'=> $this->startTradeCards,
				'usedTradeCards'=>[],
				'wealthCards'=>[],
				'status'=>'spectator',
				'money'=>[
					'yellow'=>0,
					'green'=>0,
					'blue'=>0,
					'red'=>0
				],
				'time'=> $this->time(),
			];

			$this->chat [] = [
				'time'=>$this->time(),
				'owner'=>'room',
				'user'=>$id,
				'message'=>":name: dołączył do pokoju"
			];

			$this->update();
		}
	}

	public function leaveRoom($id){
		if(($key = $this->exist($id))>-1)
		{
			unset($this->users[$key]);

			$this->chat [] = [
				'time'=>$this->time(),
				'owner'=>'room',
				'user'=>$id,
				'message'=>":name: odszdł z pokoju"
			];

			$this->update();
		}
	}

	public function joinGame($id){
		if(($key = $this->exist($id))>-1)
		{
			$this->users[$key]->status = 'player';
			$this->users[$key]->time = $this->time();

			// count($this->users)
			/*
			Mechanizm rozdawania diamentów startowych
				Pierwszy gracz otrzymuje 3 żółte kostki.
				Drugi gracz otrzymuje 4 żółte kostki.
				Trzeci gracz otrzymuje 4 żółte kostki.
				Czwarty gracz otrzymuje 3 żółte i jedną czerwoną kostkę.
				Piąty gracz otrzymuje 3 żółte i jedną czerwoną kostkę.
			*/

			$this->chat [] = [
				'time'=>$this->time(),
				'owner'=>'room',
				'user'=>$id,
				'message'=>":name: dołączył do gry"
			];

			$this->update();
		}
	}

	public function leaveGame($id){
		if(($key = $this->exist($id))>-1)
		{
			$this->users[$key]->status = 'spectator';
			$this->users[$key]->time = $this->time();

			$this->chat [] = [
				'time'=>$this->time(),
				'owner'=>'room',
				'user'=>$id,
				'message'=>":name: odszedł z gry"
			];

			$this->update();
		}
	}

	public function takeCard($playerId,$type,$cardId){
		if(($playerIndex = $this->exist($playerId))>-1){
			if($type == 'wealth'){

				$wealthCardsIndex = array_search($cardId, array_column($this->wealthCards, 'id'));
				$card = array_splice($this->wealthCards,$wealthCardsIndex,1)[0];
				/* 
					mechanizm pobierania diamentow
				*/
				$this->users[$playerIndex]->wealthCards []= $card;
				$this->update();
			}
			else if($type == 'trade'){
				$tradeCardsIndex = array_search($cardId, array_column($this->tradeCards, 'id'));
				$card = array_splice($this->wealthCards,$tradeCardsIndex,1)[0];
				/* 
					$card->yellow
					$card->green
					$card->blue
					$card->red
					mechanizm pobierania diamentow
				*/

				$this->users[$playerIndex]->tradeCards []= $card;
				$this->update();
			}
		}
	}
}

try{

	$db = new Database();

	// $room = new Room($db,'xd tak');
	$room = new Room($db,9);

	// $room->joinRoom(15);
	// $room->leaveRoom(6);

	// $room->joinGame(5);
	// $room->leaveGame(33);

	// $room->takeCard(5,'wealth',8);

}catch(Exception $e){
	echo $e->getMessage();
}

?>