<?php



class getjf{

    public $list = array();
    public function __construct(){

    }

    public function getUrl($url){

      $content =  file_get_contents($url);
      $content = iconv("GBK", "UTF-8", $content);
      return $content;
    }

    public function fetch($page){

      $start = 0;
      $time = time();

      for($page=0;$page<=602; $page++){
        $start = $page*10;
        $url = "http://www.bjrbj.gov.cn/integralpublic/settlePerson/settlePersonJson?sort=pxid&order=asc&limit=10&offset=$start&name=&rows=10&page=$start&_=$time";
        echo $page . "\t";
        $content = $this->getUrl($url);
        $res = json_decode($content, true);
        //var_dump($res);
        $this->outResult($res);

      } 
      //$url = "http://www.bjrbj.gov.cn/integralpublic/settlePerson/settlePersonJson?sort=pxid&order=asc&limit=10&offset=$start&name=&rows=10&page=$start&_=$time";
      $this->out($this->list);
      file_put_contents("/tmp/aa.txt", json_encode($this->list));
    }

    public function outResult($content){
        foreach($content["rows"] as $v){
            $this->list[] = $v;
        }
    }


    public function out($list){
        foreach($list as $v){
            echo $v['name'] . "\t" . $v["unit"] . "\n";
        }
    }
}


$tmp = new getjf();
$tmp->fetch(0);
