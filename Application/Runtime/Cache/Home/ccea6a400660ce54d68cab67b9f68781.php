<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width">
<title>帐号绑定</title>
<link href="<?php echo (CSS_URL); ?>layout.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div id="login">
  <div class="logo"></div>
  <div id="error"><?php echo ($error); ?></div>
  <form name="form1" action="" method="post" id="form1">
    <input type="hidden" name="wxno" value="<?php echo ($wxno); ?>">
  	<label class="button">学号</label>
  	<input type="text" name="usr_no" value="<?php echo ($users["usr_no"]); ?>" class="button text"/>
  	<label class="button">姓名</label>
  	<input type="text" name="usr_name" value="<?php echo ($users["usr_name"]); ?>" class="button text"/>
  	<input type="submit" class="button" id="submit" value="立即绑定"/>
  </form>
  </div>
</body>
</html>