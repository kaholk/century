<?php
/*Wylaczenie bledow*/
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );

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
	/*poprawnosc adresu email*/
	if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
		throw new Exception("data error");
	/*======================================================================================*/



	/*======================================================================================*/
	/*czy taki login istnieje juz w bazie*/
	if($database->get("users",['id'],["login" => $data['login']]))
		throw new Exception("same data");

	/*czy wystapil blad*/
	if($database->error()[2])
		throw new Exception($database->error()[2]);
	/*======================================================================================*/


	
	/*======================================================================================*/
	/*czy taki email istnieje juz w bazie*/
	if($database->get("users",['id'],["email" => $data['email']]))
		throw new Exception("same data");

	/*czy wystapil blad*/
	if($database->error()[2])
		throw new Exception($database->error()[2]);
	/*======================================================================================*/



	/*======================================================================================*/
	/*wstawienie danych do bazy*/
	$database->insert("users", [
		"login" => $data['login'],
		"email" => $data['email'],
		"password" => password_hash($data['password'],PASSWORD_DEFAULT)
	]);

	/*jesli wystapil bload przy wstawianiu*/
	if($database->error()[2])
		throw new Exception($database->error()[2]);
	/*======================================================================================*/

}catch(Exception $e){
	/*przechywcenie bledu*/
	$response['error'] = $e->getMessage();
}

/*Jesli wystapil blad*/
if(isset($response['error']))
	$res['status'] = false;
else
	$res['status'] = true;


/*dadanie danych do odpowiedzi*/
$response['data'] = $res;

// sleep(2);

/*Wysłanie odpowiedzi*/
echo json_encode($response);
?>