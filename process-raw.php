<?php
require_once 'make.php';

$str = file_get_contents('raw-proxy.json');
$str = str_replace("wrmelmwxlf.gktevlrqznwqqozy.fabpfs66gizmnojhcvqxwl.kytrcfzqla87gvgvs6c7kjnrubuh.cc",
    "ya.ru", $str);
$list = json_decode($str, false);
if(is_object($list)){$list=[$list];}
$list = array_map(function($config){
    foreach($config->outbounds as $proxy){
        if($proxy->tag==='proxy') return $proxy;
    }
    return null;
}, $list);

$n=0;
$proxyNames=[];
foreach($list as $proxy){
    $proxy->domain_strategy='prefer_ipv4';
    $proxy->tag="p_{$n}";
    $proxyNames[]=$proxy->tag;
    ++$n;
}


$urlTest = (object)[
    "type" => "urltest",
    "tag" => "auto",
    "outbounds" => $proxyNames,
    "url" => "https://8.8.8.8/generate_204",
    "interval" => "5m",
//    "tolerance" => 0,
//    "idle_timeout" => "",
    "interrupt_exist_connections" => false
];

$proxyNames[]=$urlTest->tag;


$selector = (object)[
    "type" => "selector",
    "tag" => "proxy",
    "outbounds" => $proxyNames,
    "default" => $urlTest->tag,
    "interrupt_exist_connections" => false
];


$list[]=$selector;
$list[]=$urlTest;

makeJsonEx(false,$list,'tv.json');
//file_put_contents('raw-proxy-result.json', json_encode($list, JSON_PRETTY_PRINT));

