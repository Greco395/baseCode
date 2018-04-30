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
        "USERS_TABLE" => "your_value"
    ),
    'CAPTCHA' => array(
        "PUBLIC_KEY"  => "your_value",
        "PRIVATE_KEY" => "your_value"
    ),
    'LINKS' => array(
        "LOGOUT"      => "index.php",
        "AFTER_LOGIN" => "NULL" // pagename.ext or NULL
    ),
    'MISC' => array(
        "LOGIN_METHOD"       => "POST",   // GET or POST
        "LOGIN_EMAIL_NAME"   => "email", // input name="?"
        "LOGIN_PASSW_NAME"   => "password", // input name="?"

        "REGISTRATION_METHOD"        => "POST", // GET or POST
        "REGISTRATION_USERNAME_NAME" => "username", // input name="?"
        "REGISTRATION_EMAIL_NAME"    => "email", // input name="?"
        "REGISTRATION_PASSWORD_NAME" => "password", // input name="?"
        "REGISTRATION_REG_IP"        => TRUE, // get the users ip address
        "PASSWORD_ENCR_MT"           => "php", // php or md5

        "USE_BOOTSTRAPcdn"   => false // true or false
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
        $_SESSION['logged_in']=0;
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
                return $this->alert("error", "THIS ACCOUNT NOT EXIST");
            }
            if(password_verify($password,$rows[0]['password'])){
                session_start();
                $_SESSION['logged_in'] = "1";
                $_SESSION['id']        = $rows[0]['id'];
		        $_SESSION['email']     = $rows[0]['email'];
		        if(CONFIG['LINKS']['AFTER_LOGIN'] == "NULL"){
		            return $this->alert("success", "Logged in");
		        }else{
		            return header("Location: ".CONFIG['LINKS']['AFTER_LOGIN']."");
		        }
            }else{
                return $this->alert("error", "Password Errata");
            }
        }
    }
    public function isLogged(){
        if($_SESSION['user_login_session']){
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
        }
    }

    public function bootstrapCDN($a){
        if($a == true){
            echo "<head><link href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4\" crossorigin=\"anonymous\"></head>";
        }
    }
}

class REGISTER{

  public function getVar(){
    $conf_typer_regtype = CONFIG['MISC']['REGISTRATION_METHOD'];
    $conf_typer_reguser_name = CONFIG['MISC']['REGISTRATION_USERNAME_NAME'];
    $conf_typer_regemail_name = CONFIG['MISC']['REGISTRATION_EMAIL_NAME'];
    $conf_typer_regpass_name = CONFIG['MISC']['REGISTRATION_PASSWORD_NAME'];
    if($conf_typer_regtype == "POST"){
     $username = $_POST[$conf_typer_reguser_name];
     $email = $_POST[$conf_typer_regemail_name];
     $password = $_POST[$conf_typer_regpass_name];
    }elseif($conf_typer_regtype == "GET"){
     $username = $_GET[$conf_typer_reguser_name];
     $email = $_GET[$conf_typer_regemail_name];
     $password = $_GET[$conf_typer_regpass_name];
    }
    return array("username" => "".$username."",
                 "email" => "".$email."",
                 "password" => "".$password.""
               );
  }

  public function alert($type, $text){
      if($type == "success"){
          return "<div class=\"alert alert-success\"><strong>Success!</strong><br>".$text."</div>";
      }elseif($type == "error"){
          return "<div class=\"alert alert-danger\"><strong>Error!</strong><br>".$text."</div>";
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
      case "md5":
                 $hashed_password = md5($password);
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
        return $this->alert("error", "Please insert an valid Username!");
    }
    if($this->already_registered($email, $username)){
        return $this->alert("error", "This Username/Email is already registered!\nTry another or if you already registered check your email to confirm the account!");
    } else if (strlen($password) < 6){
        return $this->alert("error", "The password must be 6 characters long or more!");
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return $this->alert("error", "Invalid Email.");
    } else {
        return true;
    }
  }

  public function newMember($username, $email, $password){
    if($this->validate($username, $email, $password) === true){
      global $dbh;
      $conf_typer_reggetip = CONFIG['MISC']['REGISTRATION_REG_IP'];
      if($conf_typer_reggetip == TRUE){
        $ip = $this->get_ip();
      }else{
        $ip = "disabled";
      }
      $hashed_password = $this->password_crypt($this->getVar()['password']);
      $date = time();
      $stmt = $db->prepare("INSERT INTO ".CONFIG['DB']['USERS_TABLE']." (id, username, email, password, ip, rank, date) VALUES (NULL, ?, ?, ?, ?, ?, ?);");
      $stmt->execute(array($this->getVar()['username'], $this->getVar()['email'], $hashed_password, $ip, '0', $date));
      return true;
    }
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
}
$set_config = new ACCESS;
$set_config->bootstrapCDN(CONFIG['MISC']['USE_BOOTSTRAPcdn']);


/*        EXAMPLE login
<?
include("class_base.php");
if($_GET['case'] == "login"){
  $captcha = new CAPTCHA;
  if($captcha->check == 1){
    $login = new ACCESS;
    $login->login();
    echo $login->newLogin();
  }else{
    echo "invalid captcha";
  }
}elseif($_GET['case'] == "logout"){
    $login = new ACCESS;
    echo $login->logout();
}
?>
*/
?>
