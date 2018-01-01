<?

/*
   PHP baseCode by Greco395 ( https://greco395.it )
*/

session_start();
session_name("secure");

$settings = [
	
  /* 
      --> EDIT THESE SETTINGS TO WORK <--
  */
	
  // DATABASE CONNECTION
  "db_host" => "your_value",
  "db_name" => "your_value",
  "db_user" => "your_value",
  "db_pass" => "your_value",
  // GOOGLE CAPTCHA SETTINGS
  "g_captcha_public_key" => "your_value",
  "g_captcha_private_key" => "your_value",
  // INSERT A RANDOM KEY TO ENCRYPT THE PASSWORD
  "psw_ecrypt_key" => "your_value",
  // INSERT THE NAME OF THE USERS TABLE
  "users_table" => "your_value",
 // INSERT THE PAGE TO USERS REDIRECT AFTER THE LOGOUT
  "logout_link" => "index.php",

  /* 
      --> END EDIT ZONE <--
  */
];

?>

<?php

// DATABASE
if($settings['db_host'] != "your_value"){
 try {
     $dbh = new PDO('mysql:host='.$settings['db_host'].';dbname='.$settings['db_name'].'', $settings['db_user'], $settings['db_pass']);
     foreach($dbh->query('SELECT * from FOO') as $row) {
         print_r($row);
     }
     $dbh = null;
 } catch (PDOException $e) {
     print "Error!: " . $e->getMessage() . "<br/>";
     die();
 }
}

// UNIVERSAL REDIRECT [ result = REDIRECT ]
function redirect($url)
{
    if (!headers_sent())
    {
        header('Location: '.$url);
        exit;
        }
    else
        {
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
}

// GET REAL IP FOR USER [ result = ip ]
function get_ip(){
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
       $ip = $_SERVER['HTTP_CLIENT_IP'];
   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   } else {
       $ip = $_SERVER['REMOTE_ADDR'];
   }
   return $ip;
}

// THIS FUNCTION INSERT THE GOOGLE CAPCHA IN A FORM [ result: google captcha "check box" ]
// ( to use this function insert this on the head ----->        <script src='https://www.google.com/recaptcha/api.js'></script>           <------ ) //
function getCaptcha(){
  global $settings;
  return '<div class="g-recaptcha" data-sitekey="'.$settings['g_captcha_public_key'].'"></div>';
}


// CHECK GOOGLE CAPTCHA [ result = true | false ]
function checkcaptcha($g_recaptcha_response){
  global $settings;
if(isset($g_recaptcha_response)){
    $url       = 'https://www.google.com/recaptcha/api/siteverify';
    $data      =  array(
        'secret'   => $settings['g_captcha_private_key'],
        'response' => $g_recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']);
      $options = array(
        'http'     => array(
        'header'   => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'   => 'POST',
        'content'  => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = json_decode(file_get_contents($url, false, $context));
 if($result->success == 1){
      $result = true;
     }else{ $result = false; }
  }

  return $result;
}

// PASSWORD HASH [ result = SHA256 TEXT ]
function hash_password($password){
  global $settings;
  $key = $settings['psw_ecrypt_key'];
  $hashed_password = hash("SHA256", $password . $key);
  return $hashed_password;
}

// CHECK IF ISSET VALUE IN DATABSE [ result = true | false ]
function issetValue($value, $db_table, $db_column){
   global $dbh;
   $stmt = $dbh->prepare("SELECT * FROM ".$db_table." WHERE ".$db_column." = ?");
   $stmt->execute(array($value));
   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
   if($rows[0] != null){
       return true;
   } else {
       return false;
   }
}

// RETURN ALL INFO FOR AN VALUE (DB) [ result = valueInfo($db_table, $db_column, $value)['name'] ]
function valueInfo($db_table, $db_column, $value){
	global $dbh;
    $stmt = $dbh->prepare("SELECT * FROM ".$db_table." WHERE `".$db_column."` = ?");
    $stmt->execute(array($value));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows[0];
}

// MAKE THE LOGIN [ result = true | false | error ]
function login($username, $password){
    global $dbh;
    global $settings;
    $email = $username;
    $db_table = $settings['users_table'];
    $stmt = $dbh->prepare("SELECT * FROM ".$db_table." WHERE email = ? OR username = ?");
    $stmt->execute(array($email, $username));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($rows[0] == null){
        return "THIS ACCOUNT NOT EXIST";
    }
    $password = hash_password($password);
    $real_password = ($rows[0]['password']);
    if($password == $real_password){
      session_start();
      $_SESSION['logged_in'] = "1";
      $_SESSION['id'] = $rows[0]['id'];
		  $_SESSION['nome'] = $rows[0]['name'];
		  $_SESSION['cognome'] = $rows[0]['surname'];
		  $_SESSION['ufcode'] = $rows[0]['email'];
    return true;
  }
  else
  {
    return false;
  }
}

// CHECK IF USER IS LOGGED [ result = true | false ]
function loggedIn(){
    session_start();
    if(isset($_SESSION['id']) && $_SESSION['logged_in'] == "1"){
        return true;
    } else {
        return false;
    }
}

// MAKE LOGOUT [ result = logout and redirect to index ]
function logout(){
  global $settings;
  session_destroy();
  redirect("Location: ".$settings['logout_link']."");
}

// GENERATE A RANDOM VALUE [ result =  cc_rand(23) = random value length 23 characters ]
function cc_rand($length=6) {
 $rtn = '';
 if (!is_numeric($length)) {
   $rtn = "ONLY NUMBER LENGTH";
 } else {
   for ($x=0;$x<$length;$x++) {
     $rtn .= numToString(rand(1,62));
   }
 }
 return $rtn;
}


// NOT TOUCH THIS
function numToString($number='') {
 $rtn = '';
 if (!isset($number)||$number==''||!is_numeric($number)) {
   $rtn = "<b>Error: </b> an interger 'number' must be passed";
 } elseif ($number > 62 || $number < 1) {
   $rtn = "<b>Error: </b> passed 'number' must be between 1 to 62 (a-zA-Z0-9)";
 } else {
   switch ($number) {
     case 1:
       $rtn = 'a';
       break;
     case 2:
       $rtn = 'b';
       break;
     case 3:
       $rtn = 'c';
       break;
     case 4:
       $rtn = 'd';
       break;
     case 5:
       $rtn = 'e';
       break;
     case 6:
       $rtn = 'f';
       break;
     case 7:
       $rtn = 'g';
       break;
     case 8:
       $rtn = 'h';
       break;
     case 9:
       $rtn = 'i';
       break;
     case 10:
       $rtn = 'j';
       break;
     case 11:
       $rtn = 'k';
       break;
     case 12:
       $rtn = 'l';
       break;
     case 13:
       $rtn = 'm';
       break;
     case 14:
       $rtn = 'n';
       break;
     case 15:
       $rtn = 'o';
       break;
     case 16:
       $rtn = 'p';
       break;
     case 17:
       $rtn = 'q';
       break;
     case 18:
       $rtn = 'r';
       break;
     case 19:
       $rtn = 's';
       break;
     case 20:
       $rtn = 't';
       break;
     case 21:
       $rtn = 'u';
       break;
     case 22:
       $rtn = 'v';
       break;
     case 23:
       $rtn = 'w';
       break;
     case 24:
       $rtn = 'x';
       break;
     case 25:
       $rtn = 'y';
       break;
     case 26:
       $rtn = 'z';
       break;
     case 27:
       $rtn = 'A';
       break;
     case 28:
       $rtn = 'B';
       break;
     case 29:
       $rtn = 'C';
       break;
     case 30:
       $rtn = 'D';
       break;
     case 31:
       $rtn = 'E';
       break;
     case 32:
       $rtn = 'F';
       break;
     case 33:
       $rtn = 'G';
       break;
     case 34:
       $rtn = 'H';
       break;
     case 35:
       $rtn = 'I';
       break;
     case 36:
       $rtn = 'J';
       break;
     case 37:
       $rtn = 'K';
       break;
     case 38:
       $rtn = 'L';
       break;
     case 39:
       $rtn = 'M';
       break;
     case 40:
       $rtn = 'N';
       break;
     case 41:
       $rtn = 'O';
       break;
     case 42:
       $rtn = 'P';
       break;
     case 43:
       $rtn = 'Q';
       break;
     case 44:
       $rtn = 'R';
       break;
     case 45:
       $rtn = 'S';
       break;
     case 46:
       $rtn = 'T';
       break;
     case 47:
       $rtn = 'U';
       break;
     case 48:
       $rtn = 'V';
       break;
     case 49:
       $rtn = 'W';
       break;
     case 50:
       $rtn = 'X';
       break;
     case 51:
       $rtn = 'Y';
       break;
     case 52:
       $rtn = 'Z';
       break;
     case 53:
       $rtn = '0';
       break;
     case 54:
       $rtn = '1';
       break;
     case 55:
       $rtn = '2';
       break;
     case 56:
       $rtn = '3';
       break;
     case 57:
       $rtn = '4';
       break;
     case 58:
       $rtn = '5';
       break;
     case 59:
       $rtn = '6';
       break;
     case 60:
       $rtn = '7';
       break;
     case 61:
       $rtn = '8';
       break;
     case 62:
       $rtn = '9';
       break;
   }
 }
 return $rtn;
}
