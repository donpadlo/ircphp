<?php

/* 
 * (с) 2018 Грибов Павел
 * http://грибовы.рф * 
 * Если исходный код найден в сети - значит лицензия GPL v.3 * 
 * В противном случае - код собственность ГК Яртелесервис, Мультистрим, Телесервис, Телесервис плюс * 
 */

class Tirc{
    var $debug=false;
    var $server="localhost";
    var $port=6667;    
    var $errno0;
    var $errstr="";
    var $timeout=10;
    var $socket;
    var $nick="noname";
    
    /**
     *  Внутрення функция для логов
     */
    private function putlog($st){
        if ($this->debug==true) echo date("H-i-s")." : ".trim($st)."\n";
    }
    
    /**
     * Конструктор класса. Вызывается при создании экземпляра
     * @param type $server  - хост сервера
     * @param type $port    - порт сервера
     */
    function __construct($server, $port,$debug=false) {
            $this->server = $server;
            $this->port = $port;
            $this->debug=$debug;
            $this->putlog("лог в режиме DEBUG");
            $this->putlog("сервер $server, порт $port");
    }	    
    /**
     *  Соединение с сервером. Возврат - результат
     * @return type
     */
    function connect(){
        $this->putlog("пробуем соедениться с irc сервером");        
        $this->socket=stream_socket_client("tcp://$this->server:$this->port", $this->errno, $this->errstr,$this->timeout,STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT);
        $this->putlog("возврат: errno: $this->errno, errstr: $this->errstr, socket: $this->socket");              
        return $this->socket;        
    }
    /**
     * Разрыв соединения с сервером. Возврат - результат
     */
    function disconnect(){
        $this->putlog("пробуем закрыть соединение с сервером $this->socket");
        $ret=fclose($this->socket);
        $this->putlog("результат: $ret");
    }
    /**
     *  Послать команду серверу. Возврат false или количество посланый байт
     * @param type $command
     * @return type
     */
    function send($command){
        $this->putlog("пробуем послать команду сервер $command");
        $ret=fwrite($this->socket, $command);
        $this->putlog("результат: $ret");
        return $ret;
    }
    function is_ping($line){         
        if(strstr($line, 'PING')) {
            $this->putlog("получен PING");
            return true;         
        }            
        
    }
    function pong(){         
        $this->send("PONG :".$this->server."\r\n");         
        $this->putlog("отправлен PONG");
    }    
    function is_msg($line){ 
        if(strstr($line, 'PRIVMSG')) return true;         
    }        
    function SetNick($nick){
        $ret=$this->send("NICK $nick\r\n");
        return $ret;
    }
    function User($name){        
        $ret=$this->send("USER $name $this->server bla :$name\r\n");        
        return $ret;
    }
    function listChans(){
        $this->send("LIST\r\n");         
    }    
    function sendmessage($target, $msg){ 
        $this->send("PRIVMSG $target :$msg\r\n");         
    }    
    function loop($onMessage){
        while (!feof($this->socket)) {
             $res = fgets($this->socket, 256); // ждем сообщений
             $onMessage($res);
             if($this->is_ping($res)) $this->pong();
        };
    }
}