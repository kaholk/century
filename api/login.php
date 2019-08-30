<?php
/*Wylaczenie bledow*/
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );

$error = '';
$response = [];

try{
	/*pobranie api*/
	if(!include_once 'api.php')
		throw new Exception("incluide error");

	/*Odebranie danych*/
	$data = json_decode(file_get_contents("php://input"));

	/*Utworzenie bazy*/
	$db = new Database();

	/*Utworznie uzytkownika*/
	$user = new User($db);

	$response['user'] = $user->login($data->login,$data->password);


}catch(Exception $e){
	/*przechywcenie bledu*/
	$error = $e->getMessage();
}



if($error!='')
	$response['error'] = $error;

/*Wysłanie odpowiedzi*/
echo json_encode($response);
?>