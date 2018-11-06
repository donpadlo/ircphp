<?php

/* 
 * (с) 2018 Грибов Павел
 * http://грибовы.рф * 
 * Если исходный код найден в сети - значит лицензия GPL v.3 * 
 * В противном случае - код собственность ГК Яртелесервис, Мультистрим, Телесервис, Телесервис плюс * 
 */

include_once 'irc_class.php';

$irc=new Tirc('куацку.ышгапрукшгпк.ru',6667,true);
$irc->connect();
$irc->SetNick("padlopavel");
$irc->User("padlopavel");
$irc->send("JOIN :#tviinet\r\n");
$irc->listChans();
$irc->loop("OnMessage");
$irc->disconnect();

function OnMessage($msg){
global $irc;
    var_dump($msg);    
    if(strstr($msg,"PRIVMSG")){
        //получаю кто прислал сообщение?
        $ma=  explode("!",$msg);
        $user=  str_replace(":", "", $ma[0]);
        $irc->sendmessage("$user","Hello $user!");
    };
};