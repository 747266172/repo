<?php
namespace Home\Model;

use Think\Model;
class UsersModel extends Model{
    private $rsl;
    //一次性获得全部验证错误
    //protected $patchValidate    =   true;
    
    //实现表单项目验证
    //通过重写父类属性_validate实现表单验证
    protected $_validate        =   array(
    
        //验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        array('usr_no','require','学号/工号必须填写'),
        //验证用户名,require必须填写项目
        array('usr_name','require','姓名必须填写'),
        array('usr_no','usr_no','学号/工号不正确',2,'callback'),
        array('usr_name','usr_name','姓名不正确',2,'callback'),
        array('wxno','wxno','此学号/工号已经被绑定',2,'callback'),
    );
    //自定义方法验证爱好信息
    //$name参数是当前被验证项目的信息
    //$name = $_POST['usr_no']
    public function usr_no($name){
        $this->rsl = $this->where("usr_no='".$name."'")->select();
        if($this->rsl){
            if($this->rsl[0]['usr_no'] == $name){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function usr_name($name){
		if($this->rsl[0]['usr_name'] == $name){
			return true;
		}else{
			return false;
		}
    }
    public function wxno($name){
		if($this->rsl[0]['wxno']){
			return false;
		}else{
			return true;
		}
    }
}

?>