<?php
function makeJson($debug){
    var_dump($debug);
    $file = file_get_contents("tv-config-template.json");
    $template = json_decode($file, false);
    $file = file_get_contents("proxy-config-source.json");
    $source = json_decode($file,false);
    $template->outbounds=$source->outbounds + $template->outbounds;
    if($debug){
        $file = file_get_contents("tv-ruleset.json");
        $ruleset = json_decode($file, false);
        $localRuleSet = array("type"=>"inline", "tag"=>"tv", "rules"=> $ruleset->rules);
        $template->route->rule_set[]=$localRuleSet;
        file_put_contents("tv-config-debug.json", json_encode($template, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));
    } else {
        $remoteRuleSet = array("type"=>"remote", "tag"=>"tv", "update_interval"=>"2h",
        "download_detour"=> "proxy", "url"=>"https://raw.githubusercontent.com/dimzon/tvbox-singbox-config/main/tv-ruleset.srs", "format"=>"binary");
        $template->route->rule_set[]=$remoteRuleSet;
        file_put_contents("tv-config-release.json", json_encode($template, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));
    }
}
