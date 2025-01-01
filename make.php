<?php

function writeJson($name, $object){
    $str=<<<HOWDOC
//profile-title: base64:RG1pdHJ5IEAgdmRzaW5hIEAgcmx9
//profile-update-interval: 60
//subscription-userinfo: upload=0; download=0; total=10737418240000000; expire=2546249531
//support-url: https://t.me/dimzon541
//profile-web-page-url: https://github.com/dimzon/tvbox-singbox-config/
{$object}    
HOWDOC;
    file_put_contents($name, $str);
}

function makeJson($debug){
    var_dump($debug);
    $file = file_get_contents("tv-config-template.json");
    $template = json_decode($file, false);
    $file = file_get_contents("proxy-config-source.json");
    $source = json_decode($file,false);
    var_dump($template->outbounds);
    $template->outbounds=array_merge($source->outbounds,$template->outbounds);
    var_dump($template->outbounds);
    if($debug){
        $template->log->level='info';
        $template->inbounds=array_values(array_filter($template->inbounds,function($e){return $e->type!=='tun';}));
        $file = file_get_contents("tv-ruleset.json");
        $ruleset = json_decode($file, false);
        $localRuleSet = array("type"=>"inline", "tag"=>"tv", "rules"=> $ruleset->rules);
        $template->route->rule_set[]=$localRuleSet;
        writeJson("tv-config-debug.json", json_encode($template, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));
    } else {
        $remoteRuleSet = array("type"=>"remote", "tag"=>"tv", "update_interval"=>"2h",
        "download_detour"=> "proxy", "url"=>"https://raw.githubusercontent.com/dimzon/tvbox-singbox-config/main/tv-ruleset.srs", "format"=>"binary");
        $template->route->rule_set[]=$remoteRuleSet;
        writeJson("tv-config-release.json", json_encode($template, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));
    }
}
