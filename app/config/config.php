<?php
/**
 * Created by PhpStorm.
 * User: ygm
 * Date: 6/11/2016
 * Time: 6:05 PM
 */
#define related to version
define('Latest_version',100110); #if the version is small than that one, then it isn't the latest.
define('Minimum_version',100101); # if version is small than that one, it has to be upgraded

#default license expiring time
define("Expire_range",2*365*24*3600);
#mysql
define('Mysql_host','localhost');
define('Mysql_name','root');//@TODO You have to change the login name and password. this one is too simple
define('Mysql_pw','root');//@TODO You have to change the login name and password

#define constant value,this is a example for a website
define('Fid_False','-1');
define('Session_idle',300);
define('Guanxiu_times',4);
define('Guanxiu_sum_time',120);
define('Class_listen',1);
define('Class_read',1);

define('Url_login','/');
define('Url_main','/user/main');
define('Url_server','http://www.howdypay.com');
define('Url_server_short','howdypay');

#define network latency
define('Max_allow_latency',1700000);//@TODO 17 seconds. if longer than that , then it should be error.

# for different instances of memcached
define('Memcached_port',11211);
define('Memcache_port_login',11211);

//define('Memcache_email_confirm',"Ec-");//store Email confirm strings
define('Memcache_rsa',"rsa-");//store RSA pub key
define('Memcache_login',"lg-");//store login email+password
define('Memcache_user',"et-");//store login email+password

//pub_key and private_key of this website for login
//@TODO you need apply for your own public key and private key from www.publicKeyCenter.com
define('Server_pub_key',"-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDRWUKY0c/UqG7BG0cd7c2902Ez
4vXh1VsvdWabGcdTCE8DeFbIYSI/pe52OApRU5u1vwEUBeNviLoHz7HLyVdR0QxK
WQhsymLB3g85Ca42NpEbk/hHQsmxUE2s1ca8LmPtCD1sTT2vgh2sYIkRMkRi9AE9
kSt9b/tsyeWR4VDr1QIDAQAB
-----END PUBLIC KEY-----");
define('Server_pri_key',"-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDRWUKY0c/UqG7BG0cd7c2902Ez4vXh1VsvdWabGcdTCE8DeFbI
YSI/pe52OApRU5u1vwEUBeNviLoHz7HLyVdR0QxKWQhsymLB3g85Ca42NpEbk/hH
QsmxUE2s1ca8LmPtCD1sTT2vgh2sYIkRMkRi9AE9kSt9b/tsyeWR4VDr1QIDAQAB
AoGBAJMWYeI5WAqUJOzsm3T9xhZfU+Y+yn9Xhm+7ztGetRsztUA85sx24rdRgE/z
Y9xsH/T8NGe7E2cj64DdfpAt8Hi4FgdYr+op8c5ZeWpyQy2gTdc14ozTCjOTRT1N
ZDvs+aRGwpXFebFnqKvBm9oaw1hMkfGcy9xyaGNQSBYb9VlpAkEA8NG9UhR/NgQN
fByXKVE9izmbabDNO2UJAyGhgJ8twJbkQt4f371bG2cPYGU4qr6zWkxLtSxW8gE5
pok7NsvVRwJBAN6LqIhJJEmSCnYTNl2Ucj1aPRsohD0a2uiMgEsPjbXmpW3E/itu
7E5IpYFZQORQ4rGzcVDixy/cCNvYN3cNNAMCQQCe2Op1YtnKan/ulvlKorDizhvq
alnlzK5WJ2/dZKIMQDvOs6/4qHGZMLDe18W6MtIhRORHXDj5pr89T5YEfg1vAkB0
/G9srR5ROl8bcMAMc2OWUuB6bVMOmBZpVqp+Sr/Q1l0yFfMSu+2mvVObamLNYqO4
jD7OKVhSFVXm04NcpuknAkEA2E0YcIQDIhusWAZ+WPGffmKXyTerCnFoSxREng13
C4/w1vfD4bGfLnbQW9nXJgUuEPiYYnk+HNt+b7WDR3lalQ==
-----END RSA PRIVATE KEY-----");

define('Server_guid','U4720890229');
define('Server_keyid','400');
define('Server_k','U4720890229#400');

// public_key_center's public key and guid#keyid
define('Pkc_guid','pkc007');
define('Pkc_keyid','7');
define('Pkc_k','pkc007#7');

define('Pkc_pub_key','-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhWS6tJScKeLDmp4BI4NdBp3Q
EIk5pKqknUx4PT5D02makxzv8Z6fBmMTCQKoxU1a6IUu463bifnw1O8mK/AjGceE
9KEvhFF7Tbf+LdQj6S4HOQE4lXuRtqWO3qkpUBntgoYe9DAHQlkN+T0hmtDDs10C
L6riOER2GGmr+5ioWQIDAQAB
-----END PUBLIC KEY-----');

define('Pkc_url_get_pub_key','http:/www.publickeycenter.com/w/p');
