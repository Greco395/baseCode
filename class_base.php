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
        "LOGIN_METHOD"       => "GET",   // GET or POST
        "LOGIN_EMAIL_NAME"   => "email", // input name="?"
        "LOGIN_PASSW_NAME"   => "password", // input name="?"
        "PASSWORD_ENCR_MT"   => "php", // php or md5     
        "USE_BOOTSTRAPcdn"   => "false" // true or false 
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
        if($a == "true"){
            echo "<head><link href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4\" crossorigin=\"anonymous\"></head>";
        }
    }
}
$set_config = new ACCESS;
$set_config->bootstrapCDN(CONFIG['MISC']['USE_BOOTSTRAPcdn']);

class CAPTCHA{
    public function getCaptcha(){
        $public_key = CONFIG['CAPTCHA']['PUBLIC_KEY']
        return '<div class="g-recaptcha" data-sitekey="'.$public_key.'"></div>';
    }

    public function checkcaptcha($g_recaptcha_response){
    if(isset($g_recaptcha_response)){
        $private_key = CONFIG['CAPTCHA']['PRIVATE_KEY']
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

/*        EXAMPLE
<? 
include("class_base.php");

if($_GET['case'] == "login"){
    $login = new ACCESS;
    $login->login();
    echo $login->newLogin();
    
}elseif($_GET['case'] == "logout"){
    $login = new ACCESS;
    echo $login->logout();
}

?>
*/
?>
