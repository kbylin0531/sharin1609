<?php
namespace {

    use Workerman\Worker;
    use Workerman\WebServer;
    use GatewayWorker\Register;
    use GatewayWorker\BusinessWorker;
    use GatewayWorker\Gateway;

    include 'Events.php';
    ini_set('display_errors', 'on');

// 加载所有Applications/*/start.php，以便启动所有服务
//foreach(glob(__DIR__.'/start_*.php') as $start_file) {
//    require_once $start_file;
//}

    class Chat {

        public static function startBusinessWorker(){
            //bussinessWorker 进程
            $worker = new BusinessWorker();
            // worker名称
            $worker->name = 'ChatBusinessWorker';
            // bussinessWorker进程数量
            $worker->count = 4;
            // 服务注册地址
            $worker->registerAddress = '127.0.0.1:1236';
        }

        public static function startGateway(){
            $gateway = new Gateway("Websocket://0.0.0.0:7272");
            // 设置名称，方便status时查看
            $gateway->name = 'ChatGateway';
            // 设置进程数，gateway进程数建议与cpu核数相同
            $gateway->count = 4;
            // 分布式部署时请设置成内网ip（非127.0.0.1）
            $gateway->lanIp = '127.0.0.1';
            // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
            // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
            $gateway->startPort = 2300;
            // 心跳间隔
            $gateway->pingInterval = 10;
            // 心跳数据
            $gateway->pingData = '{"type":"ping"}';
            // 服务注册地址
            $gateway->registerAddress = '127.0.0.1:1236';

            /*
            // 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
            $gateway->onConnect = function($connection)
            {
                $connection->onWebSocketConnect = function($connection , $http_header)
                {
                    // 可以在这里判断连接来源是否合法，不合法就关掉连接
                    // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
                    if($_SERVER['HTTP_ORIGIN'] != 'http://chat.workerman.net')
                    {
                        $connection->close();
                    }
                    // onWebSocketConnect 里面$_GET $_SERVER是可用的
                    // var_dump($_GET, $_SERVER);
                };
            };
            */
        }

        public static function startRegister(){
            // register 服务必须是text协议
            return new Register('text://0.0.0.0:1236');
        }

        public static function startWebServer(){
            // WebServer
            $web = new WebServer("http://0.0.0.0:55151");
            // WebServer进程数量
            $web->count = 2;
            // 设置站点根目录
            $web->addRoot('www.your_domain.com', __DIR__.'/Web');
        }

    }

    // 运行所有服务
    Chat::startBusinessWorker();
    Chat::startGateway();
    Chat::startRegister();
    Chat::startWebServer();
    Worker::runAll();
}
