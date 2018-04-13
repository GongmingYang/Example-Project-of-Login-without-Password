DROP DATABASE  IF EXISTS `recorder`;
CREATE DATABASE  IF NOT EXISTS `recorder`;
USE `recorder`;

#########################################################################
#uid+uname;
#uid+type_id+peroid_id+count+time;
#type_id+typename+require;
#period_id+period_name
#########################################################################
drop table if exists users;
CREATE TABLE users
(
  `uid` int(11) AUTO_INCREMENT, #  Used to detect duplicated key
  `uname` varchar(32), #  means it isn't been assigned.(free)
  `password` varchar(64),
  `guid` varchar(16),
  `guid_email` varchar(64),
  `guid_nick_name` varchar(64),
  PRIMARY KEY (`uid`),index (`uname`),index(`guid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists pub_key;
CREATE TABLE pub_key
(
  `guid` varchar(16),
  `keyid` varchar(4),
  `kstatus` varchar(2),
  `pub_key` blob,
  PRIMARY KEY (`guid`,`keyid`),index (`guid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists s_type;
CREATE TABLE s_type
(
  `type_id` int(11) AUTO_INCREMENT,
  `typename` Nvarchar(96),
  `type` varchar(2) default 'c',
  `requre_count` int(11) default 0,
  `requre_sum_time` int(11) default 0,#完成时间
  `requre_listen` int(11) default 0,#听课
  `requre_read` int(11) default 0,#看书
   PRIMARY KEY (`type_id`),unique(`typename`),index(`typename`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists s_peroid;
CREATE TABLE s_peroid
(
  `period_id` int(11) AUTO_INCREMENT,
  `pname` Nvarchar(96),
  `description` Nvarchar(96),
  PRIMARY KEY (`period_id`),unique(`pname`),index(`pname`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


drop table if exists s_record;
CREATE TABLE s_record
(
  `uid` int(11),
  `type_id` int(11),
  `period_id` int(11),
  `count` int(11) default 0,#完成数量
  `sum_time` int(11) default 0,#完成时间
  `listen_count` int(11) default 0,#听课
  `read_count` int(11) default 0,#看书
  `update_time` int(11) default 0,
  PRIMARY KEY (`uid`,`type_id`,`period_id`),index (`update_time`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert into users(uname,password) values('test1','password1');
insert into users(uname,password) values('test2','password2');
insert into users(uname,password) values('test3','password3');
insert into users(uname,password) values('ygm','111');
insert into users(uname,password) values('zyh','0217');


#组长-顾宇<jayne.gu@qq.com>
insert into users(uname,password) values('jayne.gu','guyu');
#沪圆兰（任玉兰）(506658128) 
insert into users(uname,password) values('lanzi1966','renyulan');
#可以用手机：http://www.howdypay.com
#用户名：zyh  密码： 0217
#暂时不支持修改密码，登出自己用户可以关闭浏览器，或在浏览器上输入 http://www.howdypay.com/login/logout
