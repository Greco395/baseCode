<?
$subject = 'BaseCode Account Activation';

// to customize the template you can use the token variable: $token, the users mail: $mail and the activation link: $link_to_activate.

$html = '<html>
<head>
  <title>BaseCode - Confirm your account!</title>
  <style>
    @font-face {
          	font-family: \'Avenir\';
          	src: url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-heavy-webfont.eot\');
          	src: url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-heavy-webfont.eot?#iefix\') format(\'embedded-opentype\'),
              	 url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-heavy-webfont.woff2\') format(\'woff2\'),
              	 url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-heavy-webfont.woff\') format(\'woff\'),
              	 url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-heavy-webfont.ttf\') format(\'truetype\'),
              	 url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-heavy-webfont.svg#webfontregular\') format(\'svg\');
          	font-weight: bold;
          	font-style: normal;
      	}
      
      	@font-face {
          	font-family: \'Avenir\';
          	src: url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-medium-webfont.eot\');
          	src: url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-medium-webfont.eot?#iefix\') format(\'embedded-opentype\'),
              	url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-medium-webfont.woff2\') format(\'woff2\'),
         	     	url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-medium-webfont.woff\') format(\'woff\'),
               	url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-medium-webfont.ttf\') format(\'truetype\'),
          	    url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-medium-webfont.svg#webfontregular\') format(\'svg\');
          	font-weight: normal;
          	font-style: normal;
      
      	}
      
      	@font-face {
         	font-family: \'Avenir\';
      	    src: url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-light-webfont.eot\');
      	    src: url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-light-webfont.eot?#iefix\') format(\'embedded-opentype\'),
      	         url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-light-webfont.woff2\') format(\'woff2\'),
      	         url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-light-webfont.woff\') format(\'woff\'),
      	         url(\'https://www.letsgohatch.com/assets/fonts/avenirltstd-light-webfont.ttf\') format(\'truetype\'),
      	         url(\'https://www.letsgohatch.com/fonts/avenirltstd-light-webfont.svg#webfontregular\') format(\'svg\');
      	    font-weight: 100;
      	    font-style: normal;
      	}
      	body{
      		background: #ffffff;
      		margin: 0px;
      		text-align: center;
      		font-family: \'Avenir\', \'Open Sans\', Arial, sans-serif;
      	}
      	.head{
      		background: #488dfb;
      		color: #ffffff;
      	}
      
      	.head h1{
      		font-size: 50px;
      		font-weight: normal;
      		line-height: 100px;
      		margin-top: 100px;
      	}
      
      	.button{
      		background: #39ce00;
      		color: #ffffff;
      		line-height: 50px;
      		text-decoration: none;
      		text-align: center;
      		margin-top: 50px;
      		margin-bottom: 50px;
      	}
      
      	.button a{
      		color: #ffffff;
      		text-decoration: none;
      	}
      	a{ color: #fff; } 
      	p a{ color: #565656; } 
      	.black a{ color: #000; }
  </style>
  <link href=\'https://fonts.googleapis.com/css?family=Open+Sans\' rel=\'stylesheet\' type=\'text/css\'>
</head>

<body bgcolor="#ffffff">
  <table bgcolor="#efefef" cellpadding="0" cellspacing="0" border-collapse="collapse" width="100%">
    <tr>
      <td align="center" style="padding: 30px;">
        <table bgcolor="#efefef" cellpadding="0" cellspacing="0" border-collapse="collapse" width="700px">
          <tr>
            <td align="center">
              <table bgcolor="#488dfb" class="head" style="background: #488dfb;" cellpadding="0" cellspacing="0" border="0" border-collapse="collapse" width="100%">
                <tr>
                  <td style="text-align: center;" colspan="3">
                    <h1>Welcome to <a href="https://github.com/Greco395/baseCode/">BaseCode</a></h1>

                  </td>
                </tr>
                <tr>
                  <td colspan="3" style="padding: 0px 80px; font-size: 20px; text-align: center;">Hi '.$username.', Welcome to <a href="https://github.com/Greco395/baseCode/">BaseCode</a>, please confirm your email address to get started.</td>
                </tr>
                <tr>
                  <td width="30%">&nbsp;</td>
                  <td style="text-align: center;" width="40%">
                    <table cellpadding="0" cellspacing="0" border-collapse="collapse" class="button" width="100%">
                      <tr>
                        <td>
                          <a href="'.CONFIG['LINKS']['ACCOUNT_CONFIRM'].'?token='.$token.'" style="font-size: 15px;">Confirm my email</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td width="30%">&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </table>
              <table cellpadding="0" cellspacing="0" border="0" border-collapse="collapse" width="100%">
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
        </table> 
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table width="100%" bgcolor="#ffffff" style="background: #ffffff;" cellpadding="0" cellspacing="0" border="0" border-collapse="collapse" width="100%">
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center">
        <table width="600">
          <tr>
            <td style="font-size: 14px;" class="black">This email was sent to '.$to.'</td>
            <td style="text-align: right;"></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
</body>';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';

// Additional headers
$headers[] = 'To: '.$username.' <'.$to.'>';
$headers[] = 'From: BaseCode <domain@example.com>';

// Mail it
mail($to, $subject, $html, implode("\r\n", $headers));
return true;
?>
</html>
