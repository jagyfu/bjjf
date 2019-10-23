<?php


include_once("HttpSSL.class.php");

$http_client = new HttpSSL();

$page_size = 100;

$info_data = array();

//10, 14, 36, 49, 59
for ($i = 0; $i<=60; $i++){

    $post_data = array(
        "name" => "",
        "rows" => $page_size,
        "page" => $i * $page_size
    );

    $url = "http://rsj.beijing.gov.cn/integralpublic/settlePerson/tablePage";
    $referer = "http://rsj.beijing.gov.cn/integralpublic/settleperson/settlePersonTable";
    $ua = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/71.0.3578.80 Chrome/71.0.3578.80 Safari/537.36";

    for($j=0; $j<5; $j++){
        $res = $http_client->curlPost($url, $post_data, $ua, 1);
        $res = iconv("GBK", "UTF8", $res);
        if(!empty($res)){
            break;
        }    
    }
    
    $res_data = parse_name($res);
    if(!empty($res_data)){
        //$info_data = array_merge($info_data, $res_data);
        //print_r($res_data);
        write_file("jf2019-4.csv", $res_data);
    }
    else{
        print_r($res);
    }   

    echo $i . " count: " . count($res_data) . "\n";
    sleep(3);
}



function write_file($file, $content){
    $fp = fopen($file, "a+");
    if(empty($fp)){
        return false;
    }
    $res = "";
    foreach($content as $v){
        $res .= join(", ", $v) . "\n";
    }    
    fwrite($fp, $res);
    fclose($fp);
    return true;
}

function parse_name($res){
    $info_data = array();
    if (!preg_match("#tbody>.*</tbody>#ims", $res, $match)){
        var_dump( "1" , $res);
        return false;
    }

    $mathches = $match[0];
    
    if(!preg_match_all("#<tr>.*?</tr>#is", $mathches, $m)){
        var_dump("2", $mathches);
        return false;
    }
    
    foreach($m[0] as $v){
        if(preg_match_all("#<td.*?>(.*?)</td>#is", $v, $mm)){
            //print_r($mm);
            $info = array(
                "id" => $mm[1][0],
                "name" => $mm[1][1],
                "year" => $mm[1][2],
                "company" => $mm[1][3],
                "points" => $mm[1][4]
            );
            $info_data[] = $info;
        } else{
            var_dump("3" . $v);
        }
    }    
    return $info_data;
    
}