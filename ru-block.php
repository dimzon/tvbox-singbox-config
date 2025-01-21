<?php
$urls=['https://antizapret.prostovpn.org/domains-export.txt','https://antifilter.download/list/domains.lst'];
$blacklist=[
    'casino','advisor',
    '1win','1bet','pharm','bookmaker','spins','cassino',
    'casyno',
    'cas1no',
    'casin0',
    'cazino',
    'kazino',
    'kasino',
    'diplom',
    'poker',
    '777',
    '888',
    'azino',
    'bitcoin',
    'kraken',
    '-kra',
    'gay',
    'shemale',
    'porn',
    'gambl',
    'kraken',
    'sofosbuvir',"konoply","semyanych", "agro-"
];
$whitelist=['zetflix','zona','zombie',
    'film','jacket','jacred',"shara","episode","seria","ultradox", "pirat","peer","player","mp4","m3u","mkv","rutor","mmn-club",
    'stream',
    'share',
    'sharing',
    'rezka',
    'kino',
    'cinema',
    'sinema',
    'torrent',
    'tracker',
    'season',
    'serial',
    'hd',
    'tracker',
    'movie'
];


function is_in_list($str,$list,$callable='str_contains'){
    foreach($list as $str2)
        if($callable($str,$str2)) return true;
    return false;    
}

ini_set('memory_limit', '2048M');
$file = file_get_contents("tv-ruleset.json");
$rules = json_decode($file, false);
$rules = $rules->rules[0]->domain_suffix;

function is_in_rules($str){
    global $rules;
    foreach($rules as $rule){
        if($str===$rule) return true;
        if(str_starts_with($rule,".")){
          if(str_ends_with($str,$rule)) return true;
        } else if(str_ends_with($str,".{$rule}")) return true;
    }
    return false;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_PROXY, 'socks5://127.0.0.1:2080');
curl_setopt($ch, CURLOPT_URL, $urls[0]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);

//Need to figure out how to  fix this too!
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);



$arr = preg_split("/\s+/", $response);
$arr = array_unique($arr);
$arr=array_values($arr);


$arr = array_filter($arr, function($str)use($whitelist,$blacklist){
    if(strlen($str)<3) return false;
    
    if(is_in_list($str,$whitelist)) return true;
    if(is_in_list($str,$blacklist)) return false;

    if(str_starts_with($str,'.')) return false;
    if(str_starts_with($str,'kra')) return false;

    return true;
});
$arr=array_values($arr);
$orig=json_decode(json_encode($arr));


$response = implode("\n",$arr);
$response = preg_replace('/^(\S+\.)+(\S+\.\S+)$/m','.$2',$response);
$arr = explode("\n",$response);
$arr = array_unique($arr);

$arr=array_values($arr);
$arr=array_map("strrev",$arr);
sort($arr);
$arr=array_map("strrev",$arr);
$before=count($arr);
$a=[];
$prev=false;
foreach($arr as $str)
{
    if($prev===false)
    {
        $prev = $str;
        $a[]=$str;
    }
    else{
        if(str_ends_with($str,$prev)){
            if(!str_starts_with($prev,'.') && !str_ends_with($str,".{$prev}")){
                $prev = $str;
                $a[]=$str;
            } else {
                // skip
            }

        } else {
            $prev = $str;
            $a[]=$str;
        }

    }
}
$arr=$a;

$after=count($arr);
sort($arr);
$full=json_decode(json_encode( $arr));

$arr = array_filter($arr, function($str)use($whitelist){return is_in_list($str,$whitelist);});
$arr = array_values($arr);
$file = file_get_contents("tv-ruleset.json");
$rules = json_decode($file, false);
$arr = array_merge($arr,$rules->rules[0]->domain_suffix);
$arr = array_unique($arr);
$arr = array_values($arr);
sort($arr);
$rules->rules[0]->domain_suffix=$arr;
file_put_contents("tv-ruleset.json", json_encode($rules, JSON_PRETTY_PRINT));


$arr=$full;
$arr=$orig;
$arr = array_filter($arr, function($str)use($whitelist){return !is_in_list($str,$whitelist);});
$arr = array_values($arr);
var_dump(count($full));

$file = file_get_contents("ruleset-template.json");
$rules = json_decode($file, false);
var_dump($rules);
$rules->rules[0]->domain_suffix=$arr;
file_put_contents("tv-ruleset-extra.json", json_encode($rules, JSON_PRETTY_PRINT));
var_dump($after);
return;

/*
$arr = array_filter($arr, function($str){
    if(strlen($str)<5) return false;
    if(str_ends_with($str,'.ua')) return false;
    if(str_ends_with($str,'.gold')) return false;
    if(str_ends_with($str,'.media')) return false;
    if(str_ends_with($str,'.ua')) return false;
    if(str_ends_with($str,'.ua')) return false;
    if(str_contains($str,'casino')) return false;
    if(str_contains($str,'cazino')) return false;
    if(str_contains($str,'kazino')) return false;
    if(str_contains($str,'admiral888')) return false;
    if(str_contains($str,'admiral')) return false;
    if(str_contains($str,'fonbet')) return false;
    if(str_contains($str,'poker')) return false;
    if(str_contains($str,'sofosbuvir')) return false;
    if(str_contains($str,'intimcity')) return false;
    if(str_contains($str,'animebesst')) return false;
    if(str_contains($str,'prostitutki')) return false;
    if(str_contains($str,'individualk')) return false;
    if(str_contains($str,'appspot')) return false;
    if(str_contains($str,'mefedron')) return false;
    if(str_contains($str,'pages.dev')) return false;
    if(str_contains($str,'forex4you')) return false;
    if(str_contains($str,'blogspot.com')) return false;
    if(str_contains($str,'ad888')) return false;
    if(str_contains($str,'diplom')) return false;
    if(str_contains($str,'777')) return false;
    if(str_contains($str,'admiiral')) return false;
    if(str_contains($str,'dosug')) return false;
    if(str_contains($str,'adult')) return false;
    if(str_contains($str,'trade')) return false;
    if(str_contains($str,'vulkan')) return false;
    if(str_contains($str,'azureedge')) return false;
    if(str_contains($str,'agro')) return false;
    if(str_contains($str,'intim')) return false;
    if(str_contains($str,'parimatch')) return false;
    if(str_contains($str,'pariplay')) return false;
    if(str_contains($str,'steroid')) return false;
    if(str_contains($str,'.a.run.app')) return false;
    if(str_contains($str,'gepatit')) return false;
    if(str_contains($str,'girls')) return false;
    if(str_contains($str,'propiska')) return false;
    if(str_contains($str,'belochki')) return false;
    if(str_contains($str,'2ovechki')) return false;
    if(str_contains($str,'slots')) return false;
    if(str_contains($str,'baltplay')) return false;
    if(str_contains($str,'feya')) return false;
    if(str_contains($str,'qqq-ttss.su')) return false;
    if(str_contains($str,'.media')) return false;
    if(str_contains($str,'kinovod')) return false;
    if(str_contains($str,'putana')) return false;
    if(str_contains($str,'fortuna')) return false;
    if(str_contains($str,'kasino')) return false;
    if(str_contains($str,'bongacams')) return false;
    if(str_contains($str,'fortuna')) return false;
    if(str_contains($str,'vulcan')) return false;
    if(str_contains($str,'semenarnia')) return false;
    if(str_contains($str,'semyanich')) return false;
    if(str_contains($str,'1x-bet')) return false;
    if(str_contains($str,'semena')) return false;
    if(str_contains($str,'kasino')) return false;
    if(str_contains($str,'jackpot')) return false;
    if(str_contains($str,'livetv')) return false;
    if(str_contains($str,'selector')) return false;
    if(str_contains($str,'lordfilm')) return false;
    if(str_contains($str,'gubernia')) return false;
    if(str_contains($str,'spravka')) return false;
    if(str_contains($str,'spravki')) return false;
    if(str_contains($str,'prava')) return false;
    if(str_contains($str,'onion')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;
    if(str_contains($str,'advokat')) return false;

    if(str_contains($str,'?')) return false;
    if(str_contains($str,'admiral-official')) return false;
    if(str_contains($str,'1xbet')) return false;
    if(str_contains($str,'azino777')) return false;

    if(is_in_rules($str)) return false;
    return true;
});

*/


$arr = array_filter($arr, function($str){
    if(strlen($str)<5) return false;

    if(str_contains($str,'casino')) return false;
    if(str_contains($str,'cazino')) return false;
    if(str_contains($str,'kazino')) return false;
    if(str_contains($str,'kasino')) return false;
    if(str_contains($str,'diplom')) return false;
    if(str_contains($str,'bitcoin')) return false;

    if(is_in_rules($str)) return false;

    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"film")) return true;
    if(str_contains($str,"video")) return true;
    if(str_contains($str,"movie")) return true;
    if(str_contains($str,"season")) return true;
    if(str_contains($str,"seri")) return true;
    if(str_contains($str,"episod")) return true;
    if(str_contains($str,"tor")) return true;
    if(str_contains($str,"bit")) return true;
    if(str_contains($str,"track")) return true;
    if(str_contains($str,"mp3")) return true;
    if(str_contains($str,"music")) return true;
    if(str_contains($str,"share")) return true;
    if(str_contains($str,"sharing")) return true;
    if(str_contains($str,"stream")) return true;
    if(str_contains($str,"tube")) return true;
    // if(str_contains($str,"porn")) return true;
    // if(str_contains($str,"pron")) return true;
    if(str_contains($str,"peer")) return true;
    if(str_contains($str,"pirat")) return true;
    if(str_contains($str,"warez")) return true;
    if(str_contains($str,"watch")) return true;
    if(str_contains($str,"m3u")) return true;
    if(str_contains($str,"hd")) return true;
    if(str_contains($str,"kinco")) return true;
    if(str_contains($str,"pleer")) return true;

    if(str_contains($str,"cinema")) return true;

    if(str_contains($str,"muzic")) return true;
    if(str_contains($str,"nnm")) return true;
    if(str_contains($str,"jac")) return true;

    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;
    if(str_contains($str,"rezka")) return true;

    return false;
});


$arr=array_values($arr);
$arr=array_map("strrev",$arr);
sort($arr);
$arr=array_map("strrev",$arr);
$before=count($arr);
$a=[];
$prev=false;
foreach($arr as $str)
{
    if($prev===false)
    {
        $prev = $str;
        $a[]=$str;
    }
    else{
        if(str_ends_with($str,$prev)){
            if(!str_starts_with($prev,'.') && !str_ends_with($str,".{$prev}")){
                $prev = $str;
                $a[]=$str;
            } else {
                // skip
            }

        } else {
            $prev = $str;
            $a[]=$str;
        }

    }
}
$arr=$a;
$after=count($arr);

$file = file_get_contents("ruleset-template.json");
$rules = json_decode($file, false);
var_dump($rules);
$rules->rules[0]->domain_suffix=$arr;
file_put_contents("tv-ruleset-extra.json", json_encode($rules, JSON_PRETTY_PRINT));
var_dump($after);