<?php
require_once './mysql/mysql.class.php';
/**
 * 用户操作类
 * 实现了登录、登出、注册
 * 还有查找
 */
class user{
	/**
	 * 用户登录
	 * @param  array  $userData [0] 用户名 [1]密码
	 * @return [bool]           [成功 TRUE 失败 FALSE]
	 */
	public static function login($userData=array()){
		$mysqli = new mysqlHandler("GoAPP","User");
		$userName = $userData[0];
		$userPassword = $userData[1];
		//$conditions = "`name` = \"$userName \"AND `password` = MD5(\"$userPassword\" )";
		$updateData = array(
								'status' => '1'
								);
		$conditions = array(
								'name' => $userName,
								'password' => $userPassword
								);
		if($result = $mysqli->update($updateData,$conditions)){
			if($mysqli->getLink()->affected_rows==1){
				return true;
			}	
		}
		
		return false;

	}

	/**
	 * 用户登出
	 *  @param  array  $userData [0] 用户名 [1] 密码
	 * @return [bool]           [成功 TRUE 失败 FALSE]
	 */
	public static function logout($userData =array()){
		$mysqli = new mysqlHandler("GoAPP","User");
		$userName = $userData[0];
		$userPassword = $userData[1];
		//$conditions = "`name` = \"$userName \"AND `password` = MD5(\"$userPassword\" )";
		$updateData = array(
							'status' => '0'
							);
		$conditions = array(
							'name' => $userName,
							'password' => $userPassword
							);		
		
		$result = $mysqli->update($updateData,$conditions);
		//var_dump($result);
		if($mysqli->getLink()->affected_rows==1){
			return true;
		}
		return false;

	}

	/**
	 * 用户注册
	 * @param  array  $userData [0] 用户名 [1] 密码
	 * @return [bool]           [成功 TRUE 失败 FALSE]
	 */
	public static function signIn($userData = array()){
		$mysqli = new mysqlHandler("GoAPP","User");
		$userName = $userData[0];
		$col = "COUNT(*)";
		//$conditions =  "`name` = \"".$userName ."\"";
		$conditions = array(
							'name' => $userName
							);
		
		if($result = $mysqli->select($col,$conditions)){
			$colnum=$result->fetch_assoc()["COUNT(*)"];
			echo $colnum;
			if($colnum==0){		
				$userPassword = $userData[1];
				$insertData = array(
							'name' => $userName,
							'password' => $userPassword
							);

				$result = $mysqli->insert($insertData);
				return $result;
			}else{
				return false;
			}
		}
		return false;
	}

	
	
}

//test
$user1 = new user;

if($user1->login(array("root","root")))
	echo "login succeed\n";
else
	echo "login failed\n";

if($user1->logout(array("Hxuhao","12345")))
	echo "logout succeed";
else
	echo "logout failed\n";


if($user1->signIn(array("Hxuhao233","12345")))
	echo "sign in succeed\n";
else
	echo "sign in failed\n";

?>