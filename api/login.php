<?php
session_start();

/*Wylaczenie bledow*/
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );


function keySearch($array,$finds){
	$arr = [];
	foreach($finds as $find)
	{
		foreach ($array as $key => $value) {
			if($key == $find)
				$arr[$key] = $value;
		}
	}

	return $arr;
}



/*zwracany obiekt zawiera error oraz data*/
$response = [];
/*obiekt z danymi zapisywany do $response['data']*/
$res = [];

try{
	/*pobranie konfguracji bazy*/
	if(!include_once 'database.php')
		throw new Exception("incluide error");
	
	/*Odebranie danych*/
	$data = json_decode(file_get_contents("php://input"),true);
	
	/*======================================================================================*/
	/*Poprawnosc loginu*/
	if(
		!(
		($data['login']) &&
		(strlen($data['login']) >= 5) &&
		(strlen($data['login']) <= 10)
	))throw new Exception("data error");
	/*======================================================================================*/

	/*======================================================================================*/
	/*poprawnosc hasla*/
	if(
		!(
		($data['password']) &&
		(strlen($data['password']) >= 5)
	))throw new Exception("data error");
	/*======================================================================================*/


	/*======================================================================================*/
	/*czy taki login istnieje juz w bazie*/
	if(!$user = $database->get("users",['id','login','password','email'],["login" => $data['login']]))
		throw new Exception("data error");

	/*czy wystapil blad*/
	if($database->error()[2])
		throw new Exception($database->error()[2]);
	/*======================================================================================*/

	$_SESSION['id'] = $user['id'];


	$res['user'] = keySearch($user,['login','email']);

}catch(Exception $e){
	/*przechywcenie bledu*/
	$response['error'] = $e->getMessage();
}

/*dadanie danych do odpowiedzi*/
$response['data'] = $res;

// sleep(2);

/*Wysłanie odpowiedzi*/
echo json_encode($response);
?>