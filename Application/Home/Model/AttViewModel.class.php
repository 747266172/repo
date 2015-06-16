<?php
namespace Home\Model;
use Think\Model\ViewModel;
class AttViewModel extends ViewModel{   
	public $viewFields = array(
		'attendance'=>array('usr_id','att_state','att_time'),
		'users'=>array('users.usr_name'=>'stu_name','_on'=>'attendance.usr_id=users.usr_id'),
		'teacherlog'=>array('tea_id','tea_weeks','tea_signtime','_on'=>'teacherlog.tea_id=attendance.tea_id'),
		'schedules'=>array('s_id','s_teacher','s_weekday','s_section','s_section+s_sectioncount-1'=>'s_sectionend','_on'=>'schedules.s_id=teacherlog.s_id'),
		'schoolclass'=>array('sc_name','_on'=>'schoolclass.sc_id=schedules.sc_id'),
		'course'=>array('c_name','_on'=>'course.c_id=schedules.c_id'),
		'classroom'=>array('cr_name','_on'=>'classroom.cr_id=schedules.cr_id'),
	);
}
?>