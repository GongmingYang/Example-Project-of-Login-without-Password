<?php 
//set_include_path('C:\wamp\www\lib\phpseclib');
//include('../config/config.php');
include('prsa.php');

//insert into rsapool
function insert_rsapool($klen = 1024,$num=30)
{
    $conn = new mysqli(Mysql_host,Mysql_name,Mysql_pw,'pkcPool');
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT count(*) as ct FROM rsapool where guid is null;";
    $result = $conn->query($sql);

    $sql = " insert ignore into rsapool(key_hash,klen,pri_key,pub_key) values(?,?,?,?);";
    $stmt = $conn->stmt_init();
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siss',$key_hash,$klen,$pri_key,$pub_key);

    $t = $result->fetch_assoc();

    if(intval($t['ct'])<$num)
    {
        $p = new prsa();
        for($x=0;$x<$num;$x++)
        {
            list($pri_key, $pub_key) = $p->gen_rsa_key($klen);
            $key_hash = $p->base64url_encode($p->hash($pub_key));
            $klen = $p->get_length($pri_key);
            $stmt->execute();
        }
    }
    $conn->close();
    return;
}
#for Server's pool
function insert_srsapool($klen = 1024)
{
    $conn = new mysqli(Mysql_host,Mysql_name,Mysql_pw,'pkcPool');
    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }
    $Srv_rsapool_array = array('pkc007','pkc10000','pkc10001','pkc001','pkc003','pkc10003','pkc70000');
    $sql = " insert ignore into srsapool(key_hash,guid,keyid,pri_key,pub_key) values(?,?,?,?,?);";
    $stmt = $conn->stmt_init();
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssiss', $key_hash, $guid, $keyid, $pri_key, $pub_key);
    $p = new prsa();

    foreach ($Srv_rsapool_array as $guid) {
        list($pri_key, $pub_key) = $p->gen_rsa_key($klen);
        $key_hash = $p->base64url_encode($p->hash($pub_key));
        $keyid = 7;
        $stmt->execute();
    }
    $conn->close();
    return;
}

//echo 'start:'.time().'<br>';
//insert_rsapool();
//echo time().":insert_rsapool called<br>";
//insert_srsapool();
//echo  time().":insert_srsapool called<br>";
?>