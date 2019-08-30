<?php

require_once 'vendor/autoload.php';
use Medoo\Medoo;

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
	private $id;
	private $login;
	private $password;
	private $email;
	private $db;
	private $sectator;

	public function __construct(Database $db){
		$this->db = $db;
		$this->spectator = true;
	}

	public function getId(){
		return $this->id;
	}

	public function getLogin(){
		return $this->id;
	}

	public function getPassword(){
		return $this->password;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Ustawia login
     * @throws exceptionclass Wyzuca blad jeśli login nie spełnia kryteriów
	 */
	public function setLogin($login){
		
		/*Poprawnosc loginu*/
		if(!(
			($login) &&
			(strlen($login) >= 5) &&
			(strlen($login) <= 10)
		))throw new Exception("Login rules error");

		/*ustaw login*/
		$this->login = $login;
	}

	public function setPassword($password){
		/*jesli haslo jest niezahashowane*/
		if(password_get_info($password)['algo'] == 0)
		{
			/*Sprawdz poprawnos hasla*/
			if(!(
				($password) &&
				(strlen($password) >= 5)
			))throw new Exception("Password rules error");

			/*zahashuj haslo*/
			$this->password = password_hash($password,PASSWORD_DEFAULT);
		}
		else
			$this->password = $password;
	}

	public function setEmail($email){
		/*Poprawność adresu email*/
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			throw new Exception("Email rules error");
		
		/*Ustaw email */
		$this->email = $email;

	}

	/**
     * @return bool zwraca true jesli login isnieje w bazie
	 */
	public function checkLogin(){
		if($this->db->query("get","users",['id'],["login" => $this->login]))
			return true;
		else
			return false;
	}

	/**
     * @return bool zwraca true jesli email isnieje w bazie
	 */
	public function checkEmail(){
		if($this->db->query("get","users",['id'],["login" => $this->email]))
			return true;
		else
			return false;
	}

	/**
	 * Rejestruje uzytkownika
	 * 
	 * Wymaga wywołania:
	 *	- setLogin();
	 *	- setPassword();
	 *	- setEmail();
	 */
	public function register(){
		if($this->checkLogin())
			throw new Exception("such login already exists");

		if($this->checkEmail())
			throw new Exception("such email already exists");

		$this->db->query("insert","users", [
			"login" => $this->login,
			"email" => $this->email,
			"password" => $this->password
		]);
	}

}


Class Room{
	private $id;
	private $name;

	private $tradeCards;
	private $wealthCards;
	private $users;

	private $db;

	public function __construct(Database $db){
		$this->db = $db;
		$this->users = [];
	}

	public function setId($id){
		$this->id = $id;
	}

	public function createRoom(){

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

		$this->db->query("insert","rooms",[
			'name'=>'sobol',
			'data'=>$this->packData()
		]);
	}

	private function packData(){
		return json_encode([
			'wealthCards'=>$this->wealthCards,
			'tradeCards'=>$this->tradeCards,
			'users'=>$this->users
		]);
	}

	private function unpackData($data){
		$d = json_decode($data);
		$this->wealthCards = $d->wealthCards;
		$this->tradeCards = $d->tradeCards;
		$this->users = $d->users;
	}

	public function load($id=null){
		if($id)
			$this->setId($id);

		$data = $this->db->query("get","rooms",['id','name','data'],["id" => $this->id]);

		$this->name = $data['name'];
		$this->unpackData($data['data']);
	}

	public function update(){
		$this->db->query("update","rooms",['data'=>packData()],["id" => $this->id]);
	}
}

try{
	$db = new Database();
	// $user = new User($db);
	$room = new Room($db);
	// $room->createRoom();
	// $room->setId();
	$room->load(3);

}catch(Exception $e){
	echo $e->getMessage();
}

?>