<?

/////////// include the basecode ///////////////

include("class_base.php");
// OR
// require_once("class_base.php");

////////////////////////////////////////////////

//////// create a new users table //////////////

$config = new CONFIGURATION;
echo $config->createUsersTable("users");

///////////////////////////////////////////////

/////////// simple login example //////////////

$login = new ACCESS;
$login->login();
echo $login->newLogin();

///////////////////////////////////////////////

////////// simple registration example ////////

$signup = new REGISTER;
echo $signup->newMember();

///////////////////////////////////////////////

///////// simple captcha check example ////////

$captcha = new CAPTCHA;
  if($captcha->check == 1){
     // your code here
  }

///////////////////////////////////////////////

/////////// simple account activation /////////

$activate = new ACCOUNT;
echo $activate->ActivateAccount($token);

///////////////////////////////////////////////

///////// simple get account information //////

$info = new ACCOUNT;
echo $info->getUserByName($username)['rank'];
echo $info->getUserByEmail($email)['username'];
echo $info->getUserByEmailToken($token)['password'];

///////////////////////////////////////////////



/* 

     --->  UPDATE 1.0.2 SQL  <---
               
*/

////////////// update a table /////////////////
$sql = new SQL;
$my_values = array("user_id" => "3", "plan" => "3","time" => time());
$a = $sql->update($my_values, "table_name", "id=113"); // example, where id=113
echo $a;
///////////////////////////////////////////////

///////// insert your value in a table //////
$sql = new SQL;
$my_values = array("NULL", "1", "234", "34", "test");
$a = $sql->insert($my_values, "table_name");
echo $a;
///////////////////////////////////////////////





?>
