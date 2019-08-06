<?php
require_once("config.php");
require_once("PDOMysql.class.php");
session_start();

if(!empty($_POST["security"])){

    if($_SESSION["security"]  != $_POST["security"]) { $errors[] = "验证码输入错误请重新输入。"; }

}

$security = rand(10000, 100000);
$_SESSION["security"] = $security;
$errors[] = "正常";
if(!empty($_POST["accountname"]) && !empty($_POST["oldpassword"]) && !empty($_POST["password"]) && !empty($_POST["security"])){

	$post_accountname = addslashes(trim($_POST["accountname"]));
	$post_password = addslashes($_POST["password"]);
	$post_oldpassword = addslashes($_POST["oldpassword"]);

    if(strlen($post_accountname) < 3) { $errors[] = "账户名称不能少于3个字符，请重新输入。"; }
    if(strlen($post_accountname) > 32) { $errors[] = "账户名称不能多于32个字符，请重新输入。"; }
    if(strlen($post_password) < 6) { $errors[] = "账户密码不能少于6个字符，请重新输入。"; }
    if(strlen($post_password) > 32) { $errors[] = "账户密码不能多于32个字符，请重新输入。"; }
    if(!preg_match("/[0-9a-zA-Z]/", $post_accountname)) { $errors[] = "账户名称只能用字母或者数字，请重新输入。"; }
    if(!preg_match("/[0-9a-zA-Z]/", $post_password)) { $errors[] = "账户密码只能用字母或者数字，请重新输入。"; }
    if($post_accountname == $post_password) { $errors[] = "账户密码不能与账户名称相同，请重新输入。"; }
	
    $db=new PDOMysql($mysql["host"], $mysql["username"], $mysql["password"], $mysql["port"], $mysql["realmd"], "UTF8");
    //$mysql_connect = mysql_connect($mysql["host"], $mysql["username"], $mysql["password"]) or $errors[] = "<font color='red'>数据库连接失败,请联系GM注册！</font>";
    //mysql_select_db($mysql["realmd"],$mysql_connect) or $errors[] = "<font color='red'>数据库连接失败,请联系GM注册！</font>";
    //$check_account_query = mysql_query("SELECT COUNT(*) FROM account WHERE username = '$post_accountname'",$mysql_connect);
    //$check_account_query=$db->fetRowCount("account","*","username = '$post_accountname'");
    $db->connect();
    $old=$db->fetOne("account","sha_pass_hash","username = '$post_accountname'");
    if(is_array($old)){
    	$old=$old["sha_pass_hash"];
    	//print $old;
    	//print SHA1(strtoupper($post_accountname.":".$post_oldpassword));
    }
    if($old!=SHA1(strtoupper($post_accountname.":".$post_oldpassword))) { $errors[] = "账号或原密码不正确。"; }
    if(count($errors)==1){
    	$args=array(
    			//SHA1(CONCAT(UPPER('$post_accountname'),':',UPPER('$post_password')))
    			"sha_pass_hash"=>SHA1(strtoupper($post_accountname.":".$post_password)),
    			"v"=>" "
			
    	);
    	;
    	if($db->update("account", $args,"username = '$post_accountname'")){
            $errors[] = '恭喜!账户: <font color="yellow">'.$post_accountname.'</font>修改成功!';
        }
        else{
            $errors[] = "<font color='red'>数据库连接失败,请联系GM注册！</font>";
        }
    }
    //mysql_close($mysql_connect);
	$db->close();
}

function error_msg(){

    global $errors;
    
    if(is_array($errors)){
    
        foreach($errors as $msg){
        
            echo '<div class="errors">'.$msg.'</div>';
        
        }
    
    }

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="shortcut icon" href="img/favicon.png" type="image/png" />
<title><?php echo $site["title"]."密码修改"; ?></title>
</head>
<body>

 <script type="text/javascript">
 function checkform ( form )
 {
 
     if (form.accountname.value == "") { alert( "账户名称不能为空，请重新输入。" ); form.accountname.focus(); return false; } else { if (form.accountname.value.length < 3) { alert( "账户密码不能少于3个字符，请重新输入。" ); form.accountname.focus(); return false; } }
     if (form.oldpassword.value == "") { alert( "原密码不能为空，请重新输入。" ); form.oldpassword.focus(); return false; }
     if (form.password.value == "") { alert( "账户密码不能为空，请重新输入。" ); form.password.focus(); return false; } else { if (form.password.value.length < 6) { alert( "账户密码不能少于6个字符，请重新输入。" ); form.password.focus(); return false; } }
     if (form.password2.value == "") { alert( "确认密码不能为空，请重新输入。" ); form.password2.focus(); return false; }
     if (form.password.value == form.accountname.value) { alert( "账户密码不能与账户名称相同，请重新输入。" ); form.password.focus(); return false; }
     if (form.password.value != form.password2.value) { alert( "账户密码两次输入不同，请重新输入。" ); form.password.focus(); return false; }
     //if (form.email.value == "") { alert( "电子邮件不能为空，请重新输入。" ); form.email.focus(); return false; } else { if (form.email.value.length < 8) { alert( "电子邮件不能少于8个字符，请重新输入。" ); form.email.focus(); return false; } }
     if (form.security.value == "") { alert( "验证码不能为空，请重新输入。" ); form.security.focus(); return false; }
 
 return true ;
 }
 </script>

<table class="reg">
    <tr>
        <td>
            <a href="<?php echo $_SERVER["PHP_SELF"]; ?>"><img src="img/logo.png" alt="<?php echo $site["title"]; ?>" /></a>
        </td>
    </tr>
    <tr>
        <td>
        </td>
    </tr>
    <tr>
        <td>
        
        <?php error_msg(); ?>
            
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" onsubmit="return checkform(reg);" name="reg">
            
            <table class="form">
                <tr>
                    <td align="right">
                        账号名称:
                    </td>
                    <td align="left">
                        <input name="accountname" type="text" maxlength="32" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        原密码:
                    </td>
                    <td align="left">
                        <input name="oldpassword" type="password" maxlength="32" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        新密码:
                    </td>
                    <td align="left">
                        <input name="password" type="password" maxlength="32" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        确认密码:
                    </td>
                    <td align="left">
                        <input name="password2" type="password" maxlength="32" />
                    </td>
                </tr>
                
                <tr>
                    <td align="right">
                        验证码: <font style="color:#00b0f2;"><?php echo $security; ?></font>
                    </td>
                    <td align="left">
                        <input name="security" type="text" maxlength="5" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" class="sbm" value="修改密码" />
                    </td>
                </tr>
            </table>
            
            </form>
            

        </td>
    </tr>
</table>

</body>
</html>