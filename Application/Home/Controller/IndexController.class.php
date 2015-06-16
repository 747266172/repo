<?php
namespace Home\Controller;
use Think\Controller;
use Think\Wechat;

class IndexController extends Controller {
	var $wxno;		//微信号
    var $usr_id;    //用户id
    var $sc_id;     //班级id
	var $usr_role;
	var $day;
    var $weekday;   //星期几
    var $weeks;     //当前学期第几周
	var $schoolDate;
	public function init($wxno){
	    $users=M('users');
	    $user=$users->where("wxno='$wxno' and wxno!=''")->select();
		if(empty($user)) $this->redirect("/Home/Index/login/wxno/$wxno");
	    $this->usr_id=$user[0]["usr_id"];
		$this->usr_role=$user[0]["usr_role"];
	    $student=M("student");
	    $stu=$student->where("usr_id='$this->usr_id'")->select();
	    $this->sc_id=$stu[0]["sc_id"];

	    date_default_timezone_set("Etc/GMT-8");
		$this->day=date("Y-m-d H:i:s");
	    $this->weekday=date("w");
		if($this->weekday==0) $this->weekday=7;
	    $xlTab = M("xl");
		$xl = $xlTab->where("x_start <= '$this->day' and x_end >= '$this->day'")->select();
	    $this->schoolDate=strtotime($xl[0]["x_start"])+60*60*24;
	    $this->weeks=date("W")-date("W",$this->schoolDate)+1;
	}

	//今日课表
    public function jrkb(){
		$this->init($_GET["wxno"]);
		//定义星期几
		$weekarray=array("","一","二","三","四","五","六","日");
		$weekb=$weekarray[$this->weekday];
		$this->assign('week',$weekb);
		$op = M();
		if($this->usr_role == 1){
		$sql = "SELECT c_name ,usr_name as object , cr_name ,
			s_weekday ,s_section ,s_sectioncount,s_wbegin ,s_wend
			from
			`users`,`schedules`,course ,classroom
			WHERE
				`schedules`.s_teacher=`users`.usr_id and course.c_id=`schedules`.c_id and `schedules`.cr_id=classroom.cr_id

			and sc_id = $this->sc_id
			AND s_wbegin <= $this->weeks
			AND s_wend >= $this->weeks
			and s_weekday = $this->weekday
			ORDER BY s_section";
		}else{
			$sql = "SELECT c_name ,sc_name as object, cr_name ,
			s_weekday ,s_section ,s_sectioncount,s_wbegin ,s_wend
			from
			`schedules`,course ,classroom,schoolclass
			WHERE
			course.c_id=`schedules`.c_id and `schedules`.cr_id=classroom.cr_id and schedules.sc_id=schoolclass.sc_id
			and s_teacher= $this->usr_id
			AND s_wbegin <= $this->weeks
			AND s_wend >= $this->weeks
			and s_weekday = $this->weekday
			ORDER BY s_section";
		}
		//echo $sql;
		$ob = $op -> query($sql);
		$this->assign("wxno",$_GET["wxno"]);
		$this->assign('bb',$ob);
		$this->assign("wes",$this->weeks);
		$this->assign("role",$this->usr_role);
		$this->show();
    }

    //考试安排
    public function ksap(){
		$this->init($_GET["wxno"]);
    	$ks = M();
    	$ksap_sql = "SELECT exam.e_begin,e_end, datediff(e_begin,NOW()) as s_date,classroom.cr_name , course.c_name
    	 FROM
    		exam ,`schedules` ,classroom,course
    	where
			classroom.cr_id = exam.cr_id and `schedules`.s_id = exam.s_id AND course.c_id=`schedules`.c_id AND
				exam.s_id in(
					SELECT s_id from `schedules` where sc_id
						in (
							SELECT sc_id from student WHERE
								usr_id=$this->usr_id
							)
					)
	ORDER BY e_begin ;";

	$ap = $ks -> query($ksap_sql);
	$this -> assign('show_ksap',$ap);
	$this -> show();

    }

    //可选课程
    public function kxkc(){
		$this->init($_GET["wxno"]);
		$kx = M();
		$kc_sql =' SELECT   course.c_name ,users.usr_name  , classroom.cr_name ,courseschedules.cs_pleft ,course.c_credit ,
			`courseschedules`.cs_weekday ,cs_section ,cs_sectioncount ,cs_wbegin ,cs_wend
			from
			`users`,`courseschedules`,course ,classroom
			WHERE
			`courseschedules`.usr_id=`users`.usr_id and course.c_id=courseschedules.c_id and courseschedules.cr_id=classroom.cr_id
			AND
			 courseschedules.cs_pleft!=0;';
		$ks_da = $kx->query($kc_sql);
		$this-> assign("wxno",$_GET["wxno"]);
		$this -> assign('show_kxkc',$ks_da);
		$this -> show();
    }

    //全周课表
    public function qzkb(){
		$this->init($_GET["wxno"]);
		$data=M();
		if($this->usr_role == 1){
			$sql="
				SELECT course.c_name as 课程名,
				s_weekday as 星期,s_section as 开始节,s_sectioncount as 上课长度,s_wbegin as 起始周,s_wend as 结束周
				from `schedules`,course
				WHERE course.c_id=`schedules`.c_id
				and sc_id = $this->sc_id
				AND s_wbegin <= $this->weeks
				AND s_wend >= $this->weeks
				ORDER BY s_section;
			";
		}else{
			$sql="
				SELECT c_name as 课程名,
				s_weekday as 星期,s_section as 开始节,s_sectioncount as 上课长度,s_wbegin as 起始周,s_wend as 结束周
				from `schedules`,course
				WHERE course.c_id=`schedules`.c_id
				and s_teacher=$this->usr_id
				AND s_wbegin <= $this->weeks
				AND s_wend >= $this->weeks
				ORDER BY s_section;
			";
		}
		$kcb = $data->query($sql);
		$kcbStr = "<table width='100%' cellpadding='0' cellspacing='0'>";
		$kcbStr .= "<tr><th>{$this->weeks}周</th>";
		$weekarray=array("","周一","周二","周三","周四","周五","周六","周日");
		for($i=1; $i<=7; $i++){
		    $kcbStr .= "<th>";
		    if($i==$this->weekday){
		        $kcbStr .="<span style='font-size=10px;'>".date("m-d")."</span><br/>";
		    }else{
		        $cha=time()+($i-$this->weekday)*24*60*60;
		        $kcbStr .="<span style='font-size:10px;'>".date("m-d",$cha)."</span><br/>";
		    }
		    $kcbStr .= $weekarray[$i];
		    $kcbStr .= "</th>";
		}
		$kcbStr .= "</tr>";
		for($rows=1; $rows<=11; $rows++){
			echo'<tr>';
			for($cols=0; $cols<=7; $cols++){
				if ($cols==0){
					$kcbStr .= "<td class='left'>{$rows}</td>";
				}else{
					$isEmpty=true;
					foreach($kcb as $key=>$kc){
						if ($kc['星期'] == $cols){
							if ($kc['开始节'] == $rows){
								$isEmpty=false;
								$kcbStr .= "<td rowspan={$kc['上课长度']}><span class='tdbg'>{$kc['课程名']}</span></td>";
								break;
							}else if($rows > $kc['开始节'] && $rows < $kc['开始节']+$kc['上课长度']){
								$isEmpty=false;
								if($rows == $kc['开始节']+$kc['上课长度']-1){
									unset($kcb[$key]);
								}
								break;
							}
						}
					}
					if ($isEmpty){
						$kcbStr .= "<td></td>";
					}
				}
			}
			$kcbStr .= "</tr>";

		}
		$kcbStr .= "</table>";
		$this->assign("weeks",$this->weeks);
		$this->assign("kcb",$kcbStr);
		$this->display();
	}

	//用户绑定
	public function login(){
		$us=M("users");
		$u=$us->where("wxno='".$_GET["wxno"]."' and wxno != '' and wxno is not null")->find();
		if(!empty($u)){
			$this->redirect("/Home/Index/logoff/wxno/".$_GET["wxno"]);
		}
		if(!empty($_POST)){
			$users = new \Home\Model\UsersModel();
			//验证是否存在这个用户
			/*$data["usr_no"] = $_POST['usr_no'];
			$data["usr_name"] = $_POST["usr_name"];
			$rs = $users->where($data)->find();
			if($rs == false){
				$this->assign('users',$_POST);
				$this->assign('error',$users->getError());
				$this->display();
			}else{
				$rs["wxno"] = $_POST["wxno"];
				$users->save($rs);
			}*/
			$_POST['wxno']=$_GET['wxno'];
			if($users->create()){
				$users->where("usr_no='".$_POST['usr_no']."'")->save();
				$this->redirect("/Home/Index/info/msg/绑定成功！");
			}
			else{
				$this->assign("wxno",$_GET['wxno']);
				$this->assign('users',$_POST);
				$this->assign('error',$users->getError());
				$this->display();
			}
		}else{
			$this->assign("wxno",$_GET['wxno']);
			$this->display();
		}
	}

	//解除绑定
	public function logoff(){
		$users=M("users");
		$user=$users->where("wxno='".$_GET["wxno"]."' and wxno != ''")->find();
		if(!empty($_POST)){
			$user["wxno"]=null;
			dump($user);
			$users->save($user);
			$this->redirect("/Home/Index/info/msg/您已经解除绑定");
		}
		$this->assign("wxno",$_GET["wxno"]);
		$this->assign("users",$user);
		$this->display();
	}
	//自习教室
	public function zxjs(){
		$dq=date("H:i:s");
		if ($dq >= "06:00:00" && $dq < "09:40:00"){
			$shu=1;$jie = "第1—2节";
		}elseif ($dq >= "09:40:00" && $dq < "12:00:00") {
			$shu=3;$jie = "第3—5节";
		}elseif ($dq >= "12:00:00" && $dq < "17:00:00") {
			$shu=6;$jie = "第6—8节";
		}elseif ($dq >= "17:00:00" && $dq < "22:00:00") {
			$shu=9;$jie = "第9—11节";
		}else {
			$shu=1000;
		}
		//echo $dq;
		$this->assign('jie',$jie);
		$om = M();
		//定义星期几
		$weekarray=array("日","一","二","三","四","五","六");
		$weekb=$weekarray[date("w")];
		$this->assign('week',$weekb);
		$w=date("w");
		$this->assign('wes',$zhou);
		$t="select cr_name ,cr_id,b_longitude,b_latitude,cr_count from classroom ,builder WHERE curtime()<'22:00:00' and builder.b_id = classroom.b_id and cr_type = 4 and classroom.cr_id not in
		(SELECT cr_id FROM `schedules`where s_weekday = $w and s_section =$shu);";
	 //	echo $t;
		$qt=$om->query($t);
		$this->assign('zxk',$qt);
		//dump($qt);
		$this->show();
	}

	//地图
	public function map(){
		$dq=date("H:i:s");
		if ($dq >= "06:00:00" && $dq < "09:40:00"){
			$shu=1;$jie = "第1—2节";
		}elseif ($dq >= "09:40:00" && $dq < "12:00:00") {
			$shu=3;$jie = "第3—5节";
		}elseif ($dq >= "12:00:00" && $dq < "17:00:00") {
			$shu=6;$jie = "第6—8节";
		}elseif ($dq >= "17:00:00" && $dq < "22:00:00") {
			$shu=9;$jie = "第9—11节";
		}else {
			$shu=1000;
		}
		//echo $dq;
		$this->assign('jie',$jie);
		$om = M();
		//定义星期几
	$weekarray=array("日","一","二","三","四","五","六");
	$weekb=$weekarray[date("w")];
	$this->assign('week',$weekb);
	$w=date("w");
    $this->assign('wes',$zhou);
    $t="select cr_name ,b_longitude,b_latitude,builder.b_id  from builder,classroom WHERE builder.b_id=classroom.b_id and curtime()<'22:00:00' and cr_type = 4 and classroom.cr_id not in
 (SELECT cr_id FROM `schedules`where s_weekday =$w and s_section =$shu )ORDER BY b_id;";
 //	echo $t;`


	$qt=$om->query($t);
	foreach ($qt as $k => $v) {
		$qt2[$v["b_id"]]=array();
	}
	foreach ($qt as $k => $v) {
		array_push($qt2[$v["b_id"]],$v);
	}

	$this->assign('zxk',$qt2);
	$this->show();
	}

	//请假条
	public function qjt(){
		$this->init($_GET["wxno"]);
		if($this->usr_role!=1) die();
	    $leave = M('sleave');
	    if(!empty($_POST)){
	        $leave ->create();
	        $b = $leave -> add();
	        if($b){
	           $this->redirect("Home/Index/qjqk/wxno/".$_GET["wxno"]);
	        }
	    }else{
	        $this->assign("usr_id",$this->usr_id);
	        $this ->display();
	    }
	}

    //请假情况
    public function qjqk(){
		$this->init($_GET["wxno"]);
        $sleave = M("sleave");
        $sle = $sleave->where("usr_id=$this->usr_id")->order("agree asc,begintime desc")->select();
        $agree = array("不同意","同意","未审批");
        for ($i=0; $i<count($sle); $i++){
            if($sle[$i]["agree"]==null){
                $sle[$i]["agree"]=$agree[2];
            }else{
                $sle[$i]["agree"]=$agree[$sle[$i]["agree"]];
            }
        }
		$this-> assign("wxno",$_GET["wxno"]);
        $this->assign("qjqklist",$sle);
        $this->display();
    }

    //审批
	public function sp(){
		if(!empty($_POST)){
			if($_POST['agree']=='同意'){
				$_POST['agree']=1;
			}else{
				$_POST['agree']=0;
			}
			$sleave = M('sleave');
			$sleave->create();
			$rsl=$sleave->save();
			if($_POST['agree']){
				$sle = $sleave->find($_POST['le_id']);
				$db=M();
				$sql="
					select * from attendance WHERE usr_id = '".$sle['usr_id']."'
					AND tea_id IN(
						SELECT tea_id FROM teacherlog WHERE
						DATE_FORMAT(tea_time,'%Y-%m-%d') >= '".$sle['begintime']."'
						AND DATE_FORMAT(tea_time,'%Y-%m-%d')<='".$sle['endtime']."'
					)
				";
				$attArr = $db->query($sql);
				$att = M("attendance");
				for($i=0; $i<count($attArr);$i++){
					$att->create($attArr[$i]);
					$att->att_state=2;
					$att->att_time=0;
					$att->save();
				}
			}
		}
		$this->assign("wxno",$_GET["wxno"]);
		$sleave = $this->sleave("is null",false);
		$this->assign('splist',$sleave);
		$this->display();
	}

	//审批情况
	public function spqk(){
		$this->init($_GET["wxno"]);
	    $sleave=$this->sleave("is not null");
        $agree = array("不同意","同意");
        for ($i=0; $i<count($sleave); $i++){
            $sleave[$i]["agree"]=$agree[$sleave[$i]["agree"]];
        }
		$this->assign("wxno",$_GET["wxno"]);
	    $this->assign('spqklist',$sleave);
	    $this->display();
	}

    private function sleave($agree){
		$this->init($_GET["wxno"]);
        $sleave = M();
        $sql = "
            select sleave.*, usr_name
            from sleave JOIN users
            USING(usr_id)
            where agree $agree
            and sleave.usr_id in(
                SELECT usr_id from student WHERE sc_id in(
                    SELECT sc_id from schoolclass WHERE usr_id=$this->usr_id
                )
            ) order by begintime desc;";
        return  $sleave->query($sql);
    }

	//成绩查询
	public function cjcx(){
		$this->init($_GET["wxno"]);
		$om = M();
		$terms="";
		$c_types="";
		if(empty($_POST['term'])){
			$terms="1,2,3,4,5,6,7,8";
		}else{
			$termArr = $_POST['term'];
			for($i=0; $i<count($termArr); $i++){
				if($i<count($termArr)-1){
					$terms .= $termArr[$i].",";
				}else{
					$terms .= $termArr[$i];
				}
			}
		}
		if(empty($_POST['c_type'])){
			$c_types="'专业必修','专业选修','公共必修','公共选修'";
		}else{
			$cTypeArr = $_POST['c_type'];
			for($i=0; $i<count($cTypeArr); $i++){
				if($i<count($cTypeArr)-1){
					$c_types .= "'".$cTypeArr[$i]."',";
				}else{
					$c_types .= "'".$cTypeArr[$i]."'";
				}
			}
		}
		$cj= "SELECT * from users,performance
			where `users`.usr_id = performance.usr_id and
			term in($terms) AND c_type in ($c_types)
			AND
			performance.usr_id = $this->usr_id;";
		$cx=$om->query($cj);
		$performance = $om->query("select SUM(c_credit) as sum_credit from performance WHERE usr_id = $this->usr_id");
		$sumCredit = $performance[0]['sum_credit'];
		$this->assign('wxno',$_GET["wxno"]);
		$this->assign('sumCredit',$sumCredit);
		$this->assign('zxk',$cx);
		$this->display();
	}

	//成绩筛选
	public function sx(){
		$this->assign("wxno",$_GET["wxno"]);
		$this->display();
	}

	//课堂考勤
	public function ktkq(){
		$this->init($_GET["wxno"]);
		if($this->usr_role==1){
			header("Location:http://ncvteduwx.duapp.com/weixin/wxscan.php?usrid=$this->usr_id");
		}else{
			$this->redirect("/Home/Index/kqqk/wxno/".$_GET["wxno"]);
		}
	}

	//扫一扫签到
	public function xsqd(){
		$teaid = $_GET["res"];
		$usrid = $_GET["usrid"];
		$db = M();
		$tea = $db->table("teacherlog")->where("tea_id=$teaid")->find();
		$att = $db->table("attendance")->where("tea_id=$teaid and usr_id=$usrid")->find();
		$att["att_time"]=date("Y-m-d H:i:s");
		if(time() <= strtotime($tea["tea_time"])+60*5){
			$att["att_state"]=1;
		}else if(time() <= strtotime($tea["tea_time"])+60*20){
			$att["att_state"]=0;
		}else{
			$att["att_state"]=null;
		}
		$attendance=M("attendance");
		$r = $attendance->save($att);
		if($r){
			//echo "签到成功！";
			$this->redirect("/Home/Index/info/msg/SignSuccess!");
		}else{
			//echo "签到失败！";
			$this->redirect("/Home/Index/info/msg/SignFailure!");
		}
	}
	//考勤情况
	public function kqqk($teaid=0){
		$this->init($_GET["wxno"]);
		$att_view = new \Home\Model\AttViewModel();
		if(!$teaid){
			$att = $att_view
			->where("s_teacher=$this->usr_id and tea_signtime is not null and tea_signtime!='0000-00-00 00:00:00'")->group("teacherlog.tea_id")
			->order("tea_weeks desc,s_weekday desc,s_section desc")
			->select();
			$teaid=$att[0]["tea_id"];
		}
		if($teaid){
			$case = $att_view->where("teacherlog.tea_id=$teaid")->select();
			$normalNum = $att_view->where("teacherlog.tea_id=$teaid and att_state=1")->count();
			$lateNum = $att_view->where("teacherlog.tea_id=$teaid and att_state=0")->count();
			$leaveNum = $att_view->where("teacherlog.tea_id=$teaid and att_state=2")->count();
			$absenteeismNum = $att_view->where("teacherlog.tea_id=$teaid and att_state is null")->count();
			$this->assign("wxno",$_GET["wxno"]);
			$this->assign("case",$case);
			$this->assign("normalNum",$normalNum);
			$this->assign("lateNum",$lateNum);
			$this->assign("leaveNum",$leaveNum);
			$this->assign("absenteeismNum",$absenteeismNum);
			$this -> display();
		}
		$this->assign("wxno",$_GET["wxno"]);
	}
	//考勤记录
	public function kqjl(){
		$this->init($_GET["wxno"]);
		$att_view = new \Home\Model\AttViewModel();
		$attList = $att_view
			->where("s_teacher=$this->usr_id and tea_signtime is not null and tea_signtime !='0000-00-00 00:00:00'")->group("teacherlog.tea_id")
			->order("tea_weeks desc,s_weekday desc,s_section desc")->select();
		$this->assign("wxno",$_GET["wxno"]);
		$this->assign("attList",$attList);
		$this -> display();
	}
	//已选课程
	public function yxkc(){
		$this->init($_GET["wxno"]);
		$y = M();
		$x = "SELECT course.c_name,c_credit,users.usr_name,classroom.cr_name ,courseschedules.cs_pleft,
		`courseschedules`.cs_weekday,cs_section ,cs_sectioncount,cs_wbegin ,cs_wend
		from
		`users`,`courseschedules`,course ,classroom,yxcourse
		WHERE
			`courseschedules`.usr_id=`users`.usr_id and course.c_id=courseschedules.c_id
		and courseschedules.cr_id=classroom.cr_id AND yxcourse.cs_id=courseschedules.cs_id
		and yxcourse.usr_id=$this->usr_id;";
		$yx = $y ->query($x);
		$this-> assign("wxno",$_GET["wxno"]);
		$this -> assign('yxkc',$yx);
		$this -> display();
	}

	//学生签到
	public function qd(){
		$this->init($_GET["wxno"]);
		$db = M();
		$sql="
			select * from teacherlog
			join attendance using(tea_id)
			where att_time is null
			and usr_id=$this->usr_id
			and tea_signtime <= '$this->day'
			and date_add(tea_time,INTERVAL 20 minute) >= '$this->day'
			;
		";
		$course = $db->query($sql);
		if(!empty($course)){
			$att=M("attendance");
			$att->att_time=$this->day;
			$att->where("tea_id=".$course[0]["tea_id"]." and usr_id=$this->usr_id")->save();
			echo "签到成功！";
		}else{
			$this->redirect("jxpj");
		}
		$this-> assign("wxno",$_GET["wxno"]);
	}
	//教学评价
	public function jxpj(){
		$this->init($_GET["wxno"]);
		$db=M();
		if(!empty($_POST)){
			$evaluate=M("evaluate");
			$evaluate->create();
			$evaluate->eva_time=date("Y-m-d H:i:s");
			$evaluate->save();

		}
		$sql="
			SELECT evaluate.eva_id,c_name,usr_name,classroom.cr_id,cr_name,tea_weeks,s_weekday,s_section,s_sectioncount
			from schedules,users,classroom,course,teacherlog,evaluate,attendance
			where
			evaluate.usr_id = $this->usr_id
			and evaluate.tea_id=teacherlog.tea_id
			and teacherlog.s_id=schedules.s_id
			and tea_signtime is not null and tea_signtime != '0000-00-00 00:00:00'
			and schedules.cr_id=classroom.cr_id
			and schedules.s_teacher=users.usr_id
			and schedules.c_id=course.c_id
			and (evaluate.eva_time is null or evaluate.eva_time='0000-00-00 00:00:00')
			and attendance.tea_id=evaluate.tea_id and attendance.usr_id=evaluate.usr_id and attendance.att_time is not null and attendance.att_time!='00:00:00'
			order by teacherlog.tea_time desc;
		";
		$jxpj=$db->query($sql);
		$this -> assign("jxpj",$jxpj);
		$this -> display();
	}
	//老师日志
	public function jsrz(){
		$this->init($_GET["wxno"]);
		$db = M();
		if(!empty($_POST)){
			$teacherlog = M("teacherlog");
			if($_POST["tea_category"]=="正常" || $_POST["tea_category"]==""){
				unset($_POST["tea_bk_cr_id"]);
				$_POST["tea_signtime"]=date("Y-m-d H:i:s");
			}
			$teacherlog->create();
			$teacherlog->save();
		}
		$sql="
			SELECT *FROM teacherlog
			join schedules using(s_id)
			join course using(c_id)
			join users on s_teacher = usr_id
			join schoolclass using(sc_id)
			join classroom using(cr_id)
			WHERE
			s_teacher=$this->usr_id
			AND '$this->day' >= date_sub(tea_time, interval 1 hour)
			and (tea_signtime is null or tea_signtime='0000-00-00 00:00:00')
			order by tea_time desc;
		";
		$course = $db->query($sql);
		foreach($course as $k=>$v){
			if($v["tea_weeks"]==$this->weeks && $v["s_weekday"]>$this->weekday){
				unset($course[$k]);
			}
		}
		$classroom = $db->table("classroom")->select();
		$this->assign("wxno",$_GET["wxno"]);
		$this->assign("course",$course);
		$this->assign("classroom",$classroom);
		$this->display();
	}

	//教师工作量
	public function jsgzl(){
		$this->init($_GET["wxno"]);
		$db = M();
		$sql="
			SELECT SUM(s_sectioncount) as count FROM
			`teacherlog` JOIN schedules USING(s_id)
			WHERE  s_teacher = $this->usr_id
			AND (tea_signtime IS NOT NULL OR tea_signtime != '0000-00-00 00:00:00');
		";
		$count = $db->query($sql);
		$count = $count[0]['count'];
		$this->assign("count",$count);
		if(empty($_POST)){
			$_POST['weeks']="1 AND 20";
		}
		$sql="
			SELECT c_name,sc_name,SUM(s_sectioncount) AS sum FROM
			`teacherlog` JOIN schedules USING(s_id)
			JOIN course USING(c_id)
			JOIN schoolclass USING(sc_id)
			WHERE  s_teacher = $this->usr_id
			AND (tea_signtime IS NOT NULL OR tea_signtime != '0000-00-00 00:00:00')
			AND tea_weeks BETWEEN ".$_POST['weeks']."
			GROUP BY c_id,sc_id
		";
		$workload = $db->query($sql);
		$this->assign("wxno",$_GET["wxno"]);
		$this->assign("workload",$workload);
		$this->assign("weeks","'".$_POST['weeks']."'");
		$this -> display();
	}

	//教务通知
	public function jwtz(){
		$tz = M();
		$tz_sql = 'select *
         from notice order by j_time desc';
        $tz_da = $tz -> query($tz_sql);
        $this -> assign('tz',$tz_da);
		$this -> display();
	}
	//通知内容
	public function tznr($jid=0){
		$db = M('notice');
		if(!$jid){
			$ob=$db->order("j_time desc")->select();
			$jid=$ob[0]["j_id"];
		}
        $news = $db->find($jid);
		$this -> assign('news',$news);
		$this -> display();
	}
	//校历
	public function xl(){
		//$this->init($_GET["wxno"]);
		$xl = M();
		$xl_sql =' SELECT x_year,x_term,x_start,x_end
		from xl';
		$xl_da = $xl->query($xl_sql);
		$this -> assign('xl',$xl_da);
		$this -> display();
	}

	//录入教师任务
	public function creTeaLog(){
		$this->init($_GET["wxno"]);
		date_default_timezone_set("Etc/GMT-8");
		$begdaty=date("w",$this->schoolDate);
		if($begdaty==0) $begdaty = 7;
		$sch=M("");
		$row=0;
		for($i=1; $i<=18;$i++){
			$schArr = $sch->query("select * from schedules join sectiontime using(s_section) where s_wbegin<=$i and s_wend>=$i;");
			foreach($schArr as $v){
				$schArrAll[$row]["s_id"]=$v["s_id"];
				$schArrAll[$row]["sc_id"]=$v["sc_id"];
				$schArrAll[$row]["tea_weeks"]=$i;
				$cha=$this->schoolDate+(($i-1)*7+$v["s_weekday"] - $begdaty)*24*60*60;
				$date=date("Y-m-d",$cha);
				$schArrAll[$row]["tea_time"]=$date." ".$v["s_sectiontime"];
				$schArrAll[$row]["tea_signtime"]=$date." ".$v["s_sectiontime"];
				$row++;
			}
		}
		$teaLog=M("teacherlog");
		$teaLog->addAll($schArrAll);
		//dump($schArrAll);
	}

	//修改教师任务
	public function altTeaLog(){

		date_default_timezone_set("Etc/GMT-8");
		$begdaty=date("w",$this->schoolDate);
		if($begdaty==0) $begdaty = 7;
		$sch=M();
		$tea=M("teacherlog");
		$schArr = $sch->query("select tea_id,tea_time,s_sectiontime,tea_weeks,s_weekday from teacherlog join schedules using(s_id) join sectiontime using(s_section) order by tea_id;");
		for($i=0;$i<count($schArr);$i++){
			$row["tea_id"]=$schArr[$i]["tea_id"];
			$cha=$this->schoolDate+(($schArr[$i]['tea_weeks']-1)*7+$schArr[$i]["s_weekday"] - $begdaty)*24*60*60;
			$date=date("Y-m-d",$cha);
			$row["tea_time"]=$date." ".$schArr[$i]["s_sectiontime"];
			$tea->save($row);
		}
	}
	public function info($msg){
		$this->assign("msg",$msg);
		$this->display();
	}

}
