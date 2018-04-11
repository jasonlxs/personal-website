<?php




class WebS {
    protected $master;
    protected $sockets = [];//存放客户端连接
    protected $debug = false;//true为调试模式，输出log日志
    protected $uname=[];   //存放用户名
    protected $uid=[];   //存放用户ID
    protected $handshake = [];
    protected $user='jasonlxs';
    protected $pass='Lxs-1@.@1';
    protected $dns="mysql:dbname=mydb;host:127.0.0.1;charset=utf8";

    function __construct($address, $port){
        $this->master=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)     or die("socket_create() failed");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1)  or die("socket_option() failed");
        socket_bind($this->master, $address, $port)                    or die("socket_bind() failed");
        socket_listen($this->master,20)                                or die("socket_listen() failed");

        $this->sockets[] = $this->master;
        $this->uname[]='主链接';
        $this->uid[]=0;
        $this->say("Server Started : ".date('Y-m-d H:i:s'));
        $this->say("Listening on   : ".$address." port ".$port);
        $this->say("Master socket  : ".$this->master."\n");


        while(true){
            $socketArr = $this->sockets;
            $write = NULL;
            $except = NULL;
            socket_select($socketArr, $write, $except, NULL);  //筛选socketArr中活跃的连接，自动选择来消息的socket 如果是握手 自动选择主机

            foreach ($socketArr as $socket){
                if ($socket == $this->master){  //主机
                    //接收一个客户端连接,通过本类创建的一个socket实例监听多个客户端
                    $client = socket_accept($this->master);
                    if ($client < 0){
                        $this->log("socket_accept() failed");
                        continue;
                    } else{
                        /*accept连接成功之后，代表TCP建立成功已经完成3次握手*/
                        $this->connect($client);

                        /*******接下来websocket协议或http协议传输*************/
                        /*接收客户端以http形式请求开启websocket*/
                         $bytes = @socket_recv($client,$buffer,2048,0);
                         $key = array_search($client, $this->sockets);
                         if (empty($this->handshake) || !isset($this->handshake[$key]) || !$this->handshake[$key]){
                             //websocket开启确认
                            $this->doHandShake($client, $buffer, $key);
                         }

                    }
                } else {
                    //接收socket发送的数据帧,并将数据存放于$buffer  4096代表接收了12位进制数
                    $bytes = @socket_recv($socket,$buffer,4096,0);
                    //此处是判断是否断开websocket,websocket发送的断开请求6位数据码，开始TCP断开的四次握手协议
                    //开始了断开握手，此socket连接已不能发送信息，虽然该连接还维持在数组中，但是不能再被广播
                    if ($bytes <= 6){
                        //断开之前websocket广播
                        $key = array_search($socket, $this->sockets);
                        $mes="<span style='color:red;'>".$this->uname[$key].' 自动断开</span>';
                        $this->broadcast($mes);
                        //提前清空名称和链接的维护组
                        if ($key > 0){
                            echo 'unset index is:'.PHP_EOL;
                            unset($this->sockets[$key]);
                            unset($this->uname[$key]);
                            unset($this->uid[$key]);
                        }
                        //断开websocket

                        $this->disConnect($socket);
                    }
                    else{
                        $key = array_search($socket, $this->sockets);
                        //判断是否开启websocket
                        if (empty($this->handshake) || !isset($this->handshake[$key]) || !$this->handshake[$key]){
                            $this->doHandShake($socket, $buffer, $key);
                        }else{
                            //解析二进制数据
                            $buffer = $this->decode($buffer);
                            //判断是否第一次进入
                            $username=JSON_decode($buffer);
                            if($username->uname!=null){
                                $this->uname[$key]=$username->uname;
                                $this->uid[$key]=$username->uid;
                                $buffer="<span style='color:red;'>".$username->uname.' come in</span>';
                                if(substr($username->uname,0,6)!='游客'){
                                    $this->saveData($username->uid,$buffer);
                                }
                            }else{
                                $content="<span border-radius:8px;padding:8px 12px;'>".$buffer."</span> ";
                                $buffer=$this->uname[$key].'：'.$content;
                                //将内容保存到数据库
                                $this->saveData($this->uid[$key],$buffer);
                            }

                            echo $buffer.PHP_EOL;
                            //针对所有连接进行广播
                            $this->broadcast($buffer);
                        }
                    }
                }
            }
        }
    }


    //广播函数
    protected function broadcast($mes){
          $socketArr=$this->sockets;
          array_shift($socketArr);
          foreach ($socketArr as $s) {
              $this->send($s,$mes);
          }
    }
    //将数据保存到数据库
    protected function saveData($id,$content){
        //数据库连接
        try {
            $_opts_values = array(PDO::ATTR_PERSISTENT=>true,PDO::ATTR_ERRMODE=>2);
            $db = new PDO($this->dns,$this->user,$this->pass);
        } catch (PDOException $e) {
            $this->say('Connection failedx: ' . $e->getMessage());
        }
        //设置字符集
        $db->query("SET NAMES utf8");
        //插入数据
        $sql = "insert into lxs_chat set uid=:val1,chatContent=:val2";
        $sth = $db->prepare($sql);
        $sth->bindParam(':val1',$id);
        $sth->bindParam(':val2',$content);

        $result = $sth->execute();
        if($result)return $db->lastInsertId();
    }
    //向客户端推送信息
    protected function send($client, $msg){
        $msg = $this->frame($msg);
        socket_write($client, $msg, strlen($msg));
    }
    //将每一个连接保存到一个数组$this->sockets
    protected function connect($socket){
        array_push($this->sockets, $socket);
        $this->say("\n" . $socket . " CONNECTED!");
        $this->say(date("Y-n-d H:i:s"));
    }

    //关闭websocket连接
    protected function disConnect($socket){
        socket_close($socket);
        $this->say($socket . " DISCONNECTED!");
    }
    //websocket开启确认
    protected function doHandShake($socket, $buffer, $handKey){
        $this->log("\nRequesting handshake...");
        $this->log($buffer);
        list($resource, $host, $origin, $key) = $this->getHeaders($buffer);
        $this->log("Handshaking...");
        $upgrade  = "HTTP/1.1 101 Switching Protocol\r\n" .
                    "Upgrade: websocket\r\n" .
                    "Connection: Upgrade\r\n" .
                    "Sec-WebSocket-Accept: " . $this->calcKey($key) . "\r\n\r\n";  //必须以两个回车结尾
        $this->log($upgrade);
        $sent = socket_write($socket, $upgrade, strlen($upgrade));
        $this->handshake[$handKey]=true;
        $this->log("Done handshaking...");
        return true;
    }

    protected function getHeaders($req){
        $r = $h = $o = $key = null;
        if (preg_match("/GET (.*) HTTP/"              ,$req,$match)) { $r = $match[1]; }
        if (preg_match("/Host: (.*)\r\n/"             ,$req,$match)) { $h = $match[1]; }
        if (preg_match("/Origin: (.*)\r\n/"           ,$req,$match)) { $o = $match[1]; }
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/",$req,$match)) { $key = $match[1]; }
        return array($r, $h, $o, $key);
    }

    protected function calcKey($key){
        //基于websocket version 13
        $accept = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        return $accept;
    }

    //解析截取数据帧的数据,数据帧中的数据是以二进制形式表示
    //$buffer数组，每一个下标存放一个字节  & 127就是按位与运算进行掩码解码
    protected function decode($buffer) {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;

        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        }
        else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        }
        else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }

        //截取发送的内容
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }
    //将数据格式化成数据帧
    protected function frame($s){
        //根据数据长度返回数据
        $len = strlen($s);
        if($len<=125){
            return "\x81".chr($len).$s;
        }else if($len<=65535){
            return "\x81".chr(126).pack("n", $len).$s;
        }else{
            return "\x81".chr(127).pack("xxxxN", $len).$s;
        }


        /*将数据字符串按125长度分割成数组
        $a = str_split($s, 125);
        $count=count($a);
        if ($count == 1){
            return "\x81" . chr(strlen($a[0])) . $a[0];// \x81是转义的16进制，chr是根据参数转成ascii
        }

        $ns = "";
        foreach ($a as $k=>$o){
            // if($k==$count-1){
            //    $ns .= "0x81" . chr(strlen($o)) . $o;
            // }
            // $ns .= "0x01" . chr(strlen($o)) . $o;
            $ns .= "\x81" . chr(strlen($o)) . $o;
        }
        return $ns;
        */
    }


    protected function say($msg = ""){
        echo $msg . "\n";
    }
    protected function log($msg = ""){
        if ($this->debug){
            echo $msg . "\n";
        }
    }
}
//关闭错误报告
error_reporting(0);
//设置脚本时间无限制
set_time_limit(0);

new WebS('0.0.0.0', 8080);
