<?
//////////////////////////////////////////////////////////
//                                                      //
//   PHP baseCode by Greco395 ( https://greco395.it )   //
//                                                      //
//////////////////////////////////////////////////////////
/*
   --> change where your_value is written to make everything work <--
*/
define("CONFIG", array(
    'DB' => array(
        "HOST" => "your_value",
        "NAME" => "your_value",
        "USER" => "your_value",
        "PASS" => "your_value",
        "USERS_TABLE" => "users"
    ),
    'CAPTCHA' => array(
        "PUBLIC_KEY"  => "your_value",
        "PRIVATE_KEY" => "your_value"
    ),
    'LINKS' => array(
        "LOGOUT"      => "index.php",
        "AFTER_LOGIN" => "NULL", // pagename.ext or NULL
        "ACCOUNT_CONFIRM" => "your_value" // example: http://your_domain.com/account-confirm.php ( this link will be sent via email with the token to the user to activate his account )
    ),
    'MISC' => array(
        "LOGIN_METHOD"       => "POST",   // [ GET or POST ] ( raccomantated: POST )
        "LOGIN_EMAIL_NAME"   => "email", // input name="?"
        "LOGIN_PASSW_NAME"   => "password", // input name="?"
        
        "REGISTRATION_METHOD"        => "POST", // [ GET or POST ] ( raccomantated: POST )
        "REGISTRATION_USERNAME_NAME" => "username", // input name="?"
        "REGISTRATION_EMAIL_NAME"    => "email", // input name="?"
        "REGISTRATION_PASSWORD_NAME" => "password", // input name="?"
        "REGISTRATION_REG_IP"        => TRUE, // [ true or false ] save the users ip address whe create a new account
        "PASSWORD_ENCR_MT"           => "php", // [ php or none ] ( none is not raccomantated  to real use)
        "USE_EMAIL_CONFIRMATION"     => TRUE, // [ true or false ] send an email to activate the new account ( use the page: email_basecode.php )
        "DEFAULT_ACCOUNT_ACTIVATION" => FALSE, // [ true or false]
	"PERMIT_MULTIPLE_IP_REGISTRATION" => FALSE, // [ true or false ] ( true permit the rigistration of multiple account by an ip address )
        
        "USE_BOOTSTRAPcdn"   => TRUE // true or false
    ),
    'MESSAGES' => array(
      // login messages
      "LOGIN_WRONG_PASSWORD"     => "Password Errata",
      "LOGIN_INESISTENT_ACCOUNT" => "THIS ACCOUNT NOT EXIST",
      "LOGIN_SUCCESS"            => "Logged in successfully",
      // signup messages
      "REGISTRATION_EMPTY_USERNAME"             => "Please insert an valid Username!",
      "REGISTRATION_ACCOUNT_ALREADY_REGISTERED" => "This Username/Email is already registered!\nTry another or if you already registered check your email to confirm the account!",
      "REGISTRATION_PASSWORD_SHORT"             => "The password must be 6 characters long or more!",
      "REGISTRATION_INVALID_EMAIL"              => "Invalid Email!",
      "REGISTRATION_ACCOUNT_CREATED"            => "Your account was successfully created.",
      "REGISTRATION_IP_ALREADY_EXIST"           => "This IP is already registered!",
    )
));

// CONNESSIONE AL DATABASE
if(CONFIG['DB']['HOST'] != "your_value"){
 try {
     $dbh = new PDO('mysql:host='.CONFIG['DB']['HOST'].';dbname='.CONFIG['DB']['NAME'].'', CONFIG['DB']['USER'], CONFIG['DB']['PASS']);
 } catch (PDOException $e) {
     print "Error!: " . $e->getMessage() . "<br/>";
     die();
 }
}else{
    die("PERFAVORE COMPILA LA CONFIGURAZIONE");
}

class ACCESS{
    public function login(){
        if(!isset($_SESSION)){ session_start(); }
        $_SESSION['logged_in']=false;
    }
    public function newLogin(){
        global $dbh;
        if((CONFIG['DB']['USERS_TABLE']) == "your_value"){
            die('Database users table is not specified.');
        }
        if(!$this->isLogged){
            global $dbh;
            $email_name = CONFIG['MISC']['LOGIN_EMAIL_NAME'];
            $passw_name = CONFIG['MISC']['LOGIN_PASSW_NAME'];
            if(CONFIG['MISC']['LOGIN_METHOD'] == "GET"){
                $email    = $_GET[$email_name];
                $password = $_GET[$passw_name];
            }elseif(CONFIG['MISC']['LOGIN_METHOD'] == "POST"){
                $email    = $_POST[$email_name];
                $password = $_POST[$passw_name];
            }else{
                die("Misc config is not specified");
            }
            $stmt = $dbh->prepare("SELECT * FROM ".CONFIG['DB']['USERS_TABLE']." WHERE username = ?");
            $stmt->execute(array($email));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($rows[0] == null){
                return $this->alert("error", CONFIG['MESSAGES']['LOGIN_INESISTENT_ACCOUNT']);
            }
            if(password_verify($password,$rows[0]['password'])){
                session_start();
                $_SESSION['logged_in'] = true;
                $_SESSION['id']        = $rows[0]['id'];
		            $_SESSION['email']     = $rows[0]['email'];
		            if(CONFIG['LINKS']['AFTER_LOGIN'] == "NULL"){
		                return $this->alert("success", CONFIG['MESSAGES']['LOGIN_SUCCESS']);
		            }else{
		                return header("Location: ".CONFIG['LINKS']['AFTER_LOGIN']."");
		            }
            }else{
              return $this->alert("error", CONFIG['MESSAGES']['LOGIN_WRONG_PASSWORD']);
            }
        }
    }
    public function isLogged(){
        if($_SESSION['logged_in'] === true){
            return true;
        }else{
            return false;
        }
    }
    public function logout(){
        unset($_SESSION['logged_in']);
        unset($_SESSION['id']);
        unset($_SESSION['email']);
        header("Location: ".CONFIG['LINKS']['LOGOUT']."");
    }
    public function alert($type, $text){
        if($type == "success"){
            return "<div class=\"alert alert-success\"><strong>Success!</strong><br>".$text."</div>";
        }elseif($type == "error"){
            return "<div class=\"alert alert-danger\"><strong>Error!</strong><br>".$text."</div>";
        }else{
            return $text;
        }
    }
}

class REGISTER{
  public function alert($type, $text){
      if($type == "success"){
          return "<div class=\"alert alert-success\"><strong>Success!</strong><br>".$text."</div>";
      }elseif($type == "error"){
          return "<div class=\"alert alert-danger\"><strong>Error!</strong><br>".$text."</div>";
      }else{
          return $text;
      }
  }
  public function get_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }
  public function password_crypt($password){
    switch(CONFIG['MISC']['PASSWORD_ENCR_MT']){
      case "php":
                 $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      break;
      case "none":
                 $hashed_password = $password;
      break;
    }
    return $hashed_password;
  }
  public function already_registered($email, $username){
    global $dbh;
    $stmt = $dbh->prepare("SELECT * FROM ".CONFIG['DB']['USERS_TABLE']." WHERE email=? OR username=?");
    $stmt->execute(array($email, $username));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($rows[0] != null){
        return true;
    } else {
        return false;
    }
  }
  public function already_registered_ip($ip){
    global $dbh;
    $stmt = $dbh->prepare("SELECT * FROM ".CONFIG['DB']['USERS_TABLE']." WHERE ip=?");
    $stmt->execute(array($this->get_ip()));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($rows[0] != null){
        return true;
    } else {
        return false;
    }
  }
  public function validate($username, $email, $password){
    if(empty($username)){
        return $this->alert("error", CONFIG['MESSAGES']['REGISTRATION_EMPTY_USERNAME']);
    }
    if($this->already_registered($email, $username)){
        return $this->alert("error", CONFIG['MESSAGES']['REGISTRATION_ACCOUNT_ALREADY_REGISTERED']);
    } else if (strlen($password) < 6){
        return $this->alert("error", CONFIG['MESSAGES']['REGISTRATION_PASSWORD_SHORT']);
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return $this->alert("error", CONFIG['MESSAGES']['REGISTRATION_INVALID_EMAIL']);
    }
    return true;
  }
  public function newMember(){
    if(CONFIG['MISC']['REGISTRATION_METHOD'] == "GET"){
      $method = $_GET;
    }else{
      $method = $_POST;
    }
    $username = $method[CONFIG['MISC']['REGISTRATION_USERNAME_NAME']];
    $email    = $method[CONFIG['MISC']['REGISTRATION_EMAIL_NAME']];
    $password = $method[CONFIG['MISC']['REGISTRATION_PASSWORD_NAME']];
    
    if($this->validate($username, $email, $password) === true){
      if(CONFIG['MISC']['PERMIT_MULTIPLE_IP_REGISTRATION'] === true){
	      if($this->already_registered_ip($this->get_ip()) === true){
		   return $this->alert("error", CONFIG['MESSAGES']['REGISTRATION_IP_ALREADY_EXIST']);
	      }
      }   
      global $dbh;
      $conf_typer_reggetip = CONFIG['MISC']['REGISTRATION_REG_IP'];
      if($conf_typer_reggetip == TRUE){
        $ip = $this->get_ip();
      }else{
        $ip = "disabled";
      }
      $token = "".bin2hex(random_bytes(16))."".time()."";
      $hashed_password = $this->password_crypt($password);
      $date = time();
      if(CONFIG['MISC']['USE_EMAIL_CONFIRMATION'] == true){
          $stmt = $dbh->prepare("INSERT INTO ".CONFIG['DB']['USERS_TABLE']." (id, username, email, password, ip, rank, date, email_token, status) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?);");
          $stmt->execute(array($username, $email, $hashed_password, $ip, '0', $date, $token, '0'));
          $mail = new MAIL;
          $mail->AccountActivation($email, $username, $token);
          return $this->alert("success", CONFIG['MESSAGES']['REGISTRATION_ACCOUNT_CREATED']);
      }else{
          if(CONFIG['MISC']['DEFAULT_ACCOUNT_ACTIVATION'] == true){
              $stmt = $dbh->prepare("INSERT INTO ".CONFIG['DB']['USERS_TABLE']." (id, username, email, password, ip, rank, date, email_token, status) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?);");
              $stmt->execute(array($username, $email, $hashed_password, $ip, '0', $date, $token, '1'));
              return $this->alert("success", CONFIG['MESSAGES']['REGISTRATION_ACCOUNT_CREATED']);
          }else{
              $stmt = $dbh->prepare("INSERT INTO ".CONFIG['DB']['USERS_TABLE']." (id, username, email, password, ip, rank, date, email_token, status) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?);");
              $stmt->execute(array($username, $email, $hashed_password, $ip, '0', $date, $token, '0'));
              return $this->alert("success", CONFIG['MESSAGES']['REGISTRATION_ACCOUNT_CREATED']);
          }
      }
    }else{
        return $this->validate($username, $email, $password);
    }
  }
}

class MAIL{
    public function AccountActivation($to, $username, $token){
        $link_to_activate = CONFIG['LINKS']['ACCOUNT_CONFIRM'];
        include("email_basecode.php");
    }
}

class CAPTCHA{
    public function get(){
        $public_key = CONFIG['CAPTCHA']['PUBLIC_KEY'];
        return '<div class="g-recaptcha" data-sitekey="'.$public_key.'"></div>';
    }
    public function check($g_recaptcha_response){
    if(isset($g_recaptcha_response)){
        $private_key = CONFIG['CAPTCHA']['PRIVATE_KEY'];
        $url       = 'https://www.google.com/recaptcha/api/siteverify';
        $data      =  array(
            'secret'   => $private_key,
            'response' => $g_recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR']);
        $options = array(
            'http'     => array(
            'header'   => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'   => 'POST',
            'content'  => http_build_query($data)
            ));
        $context  = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context));
        if($result->success == 1){
            $result = 1;
        }else{ $result = 0; }
    }
    return $result;
    }
}

class HTML{
  public function redirect($url){
    if (!headers_sent()){
        header('Location: '.$url);
        exit;
      }else{
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
  }
  public function bootstrapCDN($a){
        if($a == true){
            echo "<head><link href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4\" crossorigin=\"anonymous\"></head>";
        }
    }
}

class CONFIGURATION{
    public function createUsersTable($table_name){
        global $dbh;
        if(!isset($table_name)){
            if(CONFIG['DB']['USERS_TABLE'] == "your_value"){
                die("invalid table name");
            }else{
                $table_name = CONFIG['DB']['USERS_TABLE'];
            }
        }
        $sql ="CREATE TABLE `".CONFIG['DB']['NAME']."`.`".$table_name."` ( `id` INT(11) NOT NULL AUTO_INCREMENT , 
                                                                           `username` VARCHAR(256) NULL DEFAULT NULL ,
                                                                           `email` VARCHAR(256) NULL DEFAULT NULL , 
                                                                           `password` VARCHAR(256) NULL DEFAULT NULL , 
                                                                           `ip` VARCHAR(256) NULL DEFAULT NULL , 
                                                                           `rank` VARCHAR(256) NULL DEFAULT '0' , 
                                                                           `date` VARCHAR(256) NULL DEFAULT NULL , 
                                                                           `email_token` VARCHAR(256) NULL DEFAULT NULL ,
                                                                           `status` VARCHAR(256) NULL DEFAULT '0' , 
                                                                           PRIMARY KEY (`id`)) ENGINE = InnoDB;" ;
        $dbh->exec($sql);
        return "Users table created with name: ".$table_name;
    }
}

class ACCOUNT{
    // CONFIG['MISC']['USE_EMAIL_CONFIRMATION']
    public function ActivateAccount($token){
        global $dbh;
      $user = $this->getUserByEmailToken($token);
      if(is_null($user)){
          return "INVALID CODE";
      }
      if($user['email_token'] == $token){
          $stmt = $dbh->prepare("UPDATE ".CONFIG['DB']['USERS_TABLE']." SET status = ? WHERE email_token = ? ");
          $stmt->execute(array(1, $token));
	  return true;
      }else{
          return false;
      }
  }
  public function getUserByName($name){
	global $dbh;
    $stmt = $dbh->prepare("SELECT * FROM ".CONFIG['DB']['USERS_TABLE']." WHERE username = ?");
    $stmt->execute(array($name));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $rows[0];
  }
  public function getUserByEmail($email){
	  global $dbh;
      $stmt = $dbh->prepare("SELECT * FROM ".CONFIG['DB']['USERS_TABLE']." WHERE email = ?");
      $stmt->execute(array($email));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	  return $rows[0];
  }
  public function getUserByEmailToken($token){
	  global $dbh;
      $stmt = $dbh->prepare("SELECT * FROM ".CONFIG['DB']['USERS_TABLE']." WHERE email_token = ?");
      $stmt->execute(array($token));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	  return $rows[0];
  }
}
$set_config = new HTML;
$set_config->bootstrapCDN(CONFIG['MISC']['USE_BOOTSTRAPcdn']);
?>
