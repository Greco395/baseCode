<?

/*
   PHP baseCode by Greco395 ( http://kinguser.me )
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
];

?>

<?php

// DATABASE
try {
    $dbh = new PDO('mysql:host='.$settings['db_host'].';dbname='.$settings['db_name']'', $settings['db_user'], $settings['db_pass']);
    foreach($dbh->query('SELECT * from FOO') as $row) {
        print_r($row);
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
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
