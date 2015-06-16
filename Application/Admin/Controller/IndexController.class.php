<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
	/*public function admintable(){
      $db = M();
      $dbarr = $db -> query($sql= 'show tables');
      //$field = $db->getDbFields(); 
      //dump($field); 
      $this -> assign('bb',$dbarr);
      $this -> display();
     //dump($dbarr);
	}

	function adminsql($table){
		//echo $table;
		$db = M($table);		
		$table = $db -> select();
		$th = $db->getDbFields();
		$this -> assign("th",$th);
		$this -> assign('table',$table);
		$this -> display();
		//dump($ob);
	}*/
	
	public function index(){
		$this->display('adminindex');
	}
	
	public function adminuser(){
		$db = M();
		$xz = "SELECT usr_no as 学号,usr_name as 姓名,wxno as 微信号,sc_name as 班级,m_name as 专业
from users,student,schoolclass,major
where users.usr_id = student.usr_id and student.sc_id = schoolclass.sc_id and schoolclass.m_id = major.m_id and usr_role = 1";
		$xx=$db->query($xz);
	    $this->assign('xzxx',$xx);
		$this->display();
		//dump($xx);
	}

	public function adminls(){
		$db = M();
		$ls ="SELECT usr_no as 工号,usr_name as 姓名,wxno as 微信号
from users
WHERE usr_role = 2;";
        $lsxx=$db->query($ls);
	    $this->assign('xzxx',$lsxx);
		$this->display();

	}

	public function adminbj(){
		$db = M();
		$bj = "SELECT sc_name as 班级,m_name as 专业
from schoolclass,major
where schoolclass.m_id = major.m_id;";
       $bjxx = $db -> query($bj);
       $this -> assign('bjxx',$bjxx);
       $this -> display();
	}

	public function adminkc(){
		$db = M();
		$kc = "SELECT c_name as 课程名称,c_type as 课程性质,c_credit as 学分,c_info as 课程介绍
from course"; 
       $kcxx = $db -> query($kc);
       $this -> assign('kcxx',$kcxx);
       $this -> display();
	}

	public function adminjs(){
		$db = M();
		$js = "SELECT cr_name as 教室名称,b_name as 所属教学楼,cr_count as 容纳人数 
from classroom,builder
where classroom.b_id = builder.b_id"; 
       $jsxx = $db -> query($js);
       $this -> assign('jsxx',$jsxx);
       $this -> display();
	}
    
	public function adminzy(){
		$db = M();
		$zy = "SELECT m_name as 专业名称,m_remark as 专业介绍
from major"; 
       $zyxx = $db -> query($zy);
       $this -> assign('zyxx',$zyxx);
       $this -> display();
	}

	 public function adminkcb(){
		$db = M();
		$kcb = "SELECT usr_name as 授课教师,sc_name as 上课班级,cr_name as 教室,
		c_name as 课程名
from schedules,classroom,course,users,schoolclass
where schedules.cr_id = classroom.cr_id and schedules.c_id = course.c_id and
 schedules.sc_id = schoolclass.sc_id and schedules.s_teacher = users.usr_id"; 
       $kcbxx = $db -> query($kcb);
       $this -> assign('kcbxx',$kcbxx);
       $this -> display();
	}   

	public function admincj(){
		$db = M();
        $cj = 'SELECT usr_name,per_name,per_score
from users,performance
where users.usr_id = performance.usr_id;';
       $cjxx = $db -> query($cj);
       $this -> assign('cjxx',$cjxx);
       $this -> display();
	} 

	public function useradd(){
		$xz = M('users');
		if(!empty($_POST)){
		$xz ->create();
		$b = $xz -> add();
	  if($b){
				echo "添加成功。";

			}
		}else {
	   $this -> assign('xzxx',$xz);
       $this -> display();
		}
		//$data['name'] = 'ThinkPHP';
        //$data['email'] = 'ThinkPHP@gmail.com';
        //$xz->add($data);
        /*$xz = D('student');
        $xz = $xz -> relation(true) -> find(1);
        dump($xz);*/
	}  

	public function tzadd(){
		$tz = M('notice');
		if(!empty($_POST)){
		$tz ->create();
		$m = $tz -> add();
	  if($m){
				echo "添加成功。";

			}
		}else {
	      $this -> assign('nrxx',$tz);
          $this -> display();
		}
 }

     public function admintz($tid){
		$db = M('notice');
		$tzxx = "SELECT j_title,j_time,j_content,j_photo from notice";
       $tzxx_da = $db -> query($tzxx);
       $this -> assign('nrxx',$tzxx_da);
       $dbt = M('notice');
       if(!empty($_POST)){
       $news = $dbt->find($tid);
       $b = $news -> delete();
       if($b){
       	echo "删除成功！";
       }
   }else{
	  $this -> assign('new',$news);
       //dump($tzxx_da);
	}
	$this -> display();
}

	public function login() {
		if(!empty($_POST)){	
			$usr_name = "admin";
			$password = "admin";
			if($usr_name==strtolower($_POST["usr_name"])){	
				if($password==$_POST["password"]){
					$this ->redirect("Index/adminindex");
				}else{
					$this->assign("error2","密码错误！");
					$this->display();
				}				
			}else{
				$this->assign("error1","用户名不存在！");
				$this->display();
			}
		}else{			
			$this->display();
		}
	}
}