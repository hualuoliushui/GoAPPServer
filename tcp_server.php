<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
require_once './Workerman/Autoloader.php';
require_once './user.class.php';
require_once './getPOI.php';

/**
 * 要不要写成一个类呢= =
 */
	

	// 创建一个Worker监听2347端口，不使用任何应用层协议
	$tcp_worker = new Worker("tcp://0.0.0.0:2347");

	$tcp_worker->connectionsID = array();

	// 启动1个进程对外提供服务
	$tcp_worker->count = 1;

	// $tcp_worker->onWorkerStart = function($worker)
	// {
	//     // 定时，每10秒一次
	//     Timer::add(10, function()use($worker)
	//     {
	//         // 遍历当前进程所有的客户端连接，发送当前服务器的时间
	//         foreach($worker->connections as $connection)
	//         {
	//         	//echo "send time to $connection->id\n";

	//             	//$connection->send(time());
	//         }
	//     });
	// };	

	//当客户端连接时
	$tcp_worker->onConnect = function($connection)
	{
		global $tcp_worker;
	    	echo "new connection from ip " . $connection->getRemoteIp() . "\n";  
	    	// $index = $tcp_worker->id . $connection->id;
	    	// $connection->id = $index;
	    	// $connection->send("id : $index\n");
	};

	// 当客户端发来数据时
	$tcp_worker->onMessage = function($connection, $data) use ($tcp_worker)
	{
		//global $tcp_worker;
		$decode = explode("|", $data);
		$decode =str_replace("\r\n", "", $decode);
		switch ($decode[0]) {
			case 'G':
				# code...
				$returnData =  getPOI::getPOIData($decode[1]);	    	
		    		$connection->send($returnData);
				break;
			case 'L':
				#
				$userData =str_replace("\n", "", $decode);
				$userData = explode("&", $decode[1]);
				//var_dump($userData);
				if(user::login($userData)){
					if(!isset($connection->uid))
						$connection->uid = $connection->id;
					$tcp_worker->connectionsID[$connection->uid] = $connection;		
					$connection->send("login succeed , your id is $connection->uid\n");
				}else{
					$connection->send("login failed");
				}

				break;

			default:
				# code...
				// foreach ($worker->connections as $conn) {
				// 	var_dump($conn->id);
				// 	if($decode[0] == $conn->id){
				// 		$conn->send($decode[1]);
				// 		echo "send $conn->id\n";
				// 	}
				// }	
				sendMessageByUid($decode[0],$decode[1]);
				break;
				
		}

	};

	//当客户端连接错误是
	$tcp_worker->onError = function($connection, $code, $msg)
	{
	    echo "$connection error $code $msg\n";
	};

	//当worker停止时
	$tcp_worker->onWorkerStop = function($worker)
	{
	    echo "Worker  $worker->id stopping...\n";
	};

	//通过connectionID发送消息
	function sendMessageByUid($id, $message)
	{
		global $tcp_worker;	
		if(isset($tcp_worker->connectionsID[$id]))
		{
	        	$connection = $tcp_worker->connectionsID[$id];
	        	$connection->send($message);
	    	}
	}
	// 运行所有worker实例
	Worker::runAll();

?>