<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
	

    public function xlstest(){
        $content = file_get_contents("D:\\\\xampp\\htdocs\\wxjwpt\\103.csv");
        $array = explode("\n", $content);
 	echo "";
        $db = M("cours");
        for($i=0;$i<count($array);$i++){
          $a = explode(",",$array[$i]);

		// echo $i." ".count($a)."<br />";
            for($j=1;$j<count($a)-1;$j++){
                if($a[$j] != ""){
                    //echo $a[0]." ".$j." ".$a[$j]."<br />";
                    $b = explode("◇",str_replace('"', '', $a[$j]));
                    $k = intval(($j-1)/4)+1;
                    //echo $j." ".$k." <br>";
                    $c = explode("[",strstr($b[1],'周'));
                    $d = explode("【",$b[3]);
                    $data['ClassName'] = $a[0];
                    $e = ereg_replace("\n","",$b[0]);
                    $data['CourseName'] = ereg_replace(" ","",$e);
                    $data['Teacher'] = $d[0];
                    $data['ClassRoom'] = $b[2];
                    $data['CourseTime'] = $k." ".str_replace(']','',$c[1]);
                    $data['TeachWeek'] = $c[0];
                    //print_r($data);
                  echo $a[0]." ".$k." ".$b[0]." ".$c[0]." ".str_replace(']','',$c[1])." ".$b[2]." ".$d[0]."<br >";
 					$db->add($data);

                }
            }
        }
        echo "finish ";
        //print_r($array);
        //fclose ($f);
    }

 public function csvs(){
        $content = file_get_contents("D:\\\\xampp\\htdocs\\wxjwpt\\102.csv");
        $array = explode("\n", $content);
 
        $db = M("fenshu");
        for($i=0;$i<count($array);$i++){
          $a = explode(",",$array[$i]);
		//echo $i." ".count($a)."<br />";
            for($j=1;$j<count($a)-1;$j++){
                if($a[$j] != ""){
                    //echo $a[0]." ".$j." ".$a[$j]."<br />";
                    $c = $a[$j];
                 // echo "<pre>";
                  //  print_r($a);
                  // echo "</pre>";
                    //echo $j." ".$k." <br>";
                    $data['ming'] = $a[0];
                    $data['c_id'] = $j;
                    $data['fenshu'] = $c;
                    //print_r($data);
                  echo $a[0]." ".$j." ".$c." "."<br >";
 					$db->add($data);

                }
            }
        }
        echo "finish ";
        //print_r($array);
        //fclose ($f);
    }


}