<?php
	header('Access-Control-Allow-Origin:*');
	//连接服务器  
	$link=mysql_connect('localhost','root','');  
	//状态  
	if(!$link){  
		echo "connection failed";  
		exit;  
	}  
	$db_exist=mysql_select_db('phptest');
	if(!$db_exist)
	{
		$create_d="create database phptest";
		mysql_query($create_d);
		mysql_select_db('phptest');
		$create_t="create table user
		(id int not null auto_increment,
		 PRIMARY KEY(id),
		 name varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
		 email varchar(32) not null,
		 company varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
		 phone char(11),
		 )";
		mysql_query($create_t);
	}
	//获取post的数据
	$name = $_POST['name'];
	$email = $_POST['email'];
	$company = $_POST['company'];
	$phone = $_POST['phone'];
	//验证
	if(trim($name) && trim($email) && trim($company) && trim($phone)){
		if(!preg_match("/^1[34578]{1}\d{9}$/",$phone)){  
		    echo json_encode(array('status'=>'error','msg'=>'手机号码格式不正确'));
		    exit;
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){  
		    echo json_encode(array('status'=>'error','msg'=>'邮箱格式不正确"'));  
		    exit;
		}
	}else{
		echo json_encode(array('status'=>'error','msg'=>'请填写完整信息！'));
		exit;
	}
	//检查手机号、邮箱是否存在
	$has_phone = mysql_query("select * from users where phone='$phone'");
	if(mysql_num_rows($has_phone)){
		echo json_encode(array('status'=>'error','msg'=>'手机号码已经注册过'));
		exit;
	}
	$has_email = mysql_query("select * from users where email='$email'");
	if(mysql_num_rows($has_email)){
		echo json_encode(array('status'=>'error','msg'=>'邮箱已经注册过'));
		exit;
	}
	//存入数据库
	mysql_query('set names utf8;'); 
	$insert = "insert into user(name,email,company,phone) values('$name','$email','$company','$phone')";
	mysql_query($insert);
	if(mysql_affected_rows()<=0){
		echo json_encode(array('status'=>'error','msg'=>'注册失败，请稍后再试'));
		exit;
	}
	mysql_close($link);
	echo json_encode(array("status"=>"success","msg"=>"感谢注册"));
?>