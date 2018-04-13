<?php
/**
 * PHP implementation of PRSA.
 *
 * PHP versions 4 and 5; phpseclib;
 *
 * NOTE:
 * Here's a short example of how to use this library:
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  Crypt
 * @package   AES,RSA,Sha256,Base64,
 * @author    Gongming Yang <gongming.yang.yu@gmail.com>
 * @copyright 2018 Gongming Yang
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://www.publickeycenter.com
 */
set_include_path('../lib/phpseclib');
include('Math/BigInteger.php');
include('Crypt/RSA.php');
include('Crypt/Random.php');
include('Crypt/AES.php');

define("pkcs1",2);
define("no_padding",3);
define("oaep",1);
define("aes_256_cfb",3);
define("aes_cfb",3);
define('aes_cbc', 2);
define("sha128",'sha128');
define("sha256",'sha256');
define("sha512",'sha512');

class prsa
{
    public $rsa_key = null;
    function hash($passkey, $htype = sha512)
    {
        $hash = new Crypt_Hash($htype);
        return $hash->hash($passkey);
    }

    function gen_str_hash($key)
    {
      return $this->base64url_encode($this->hash($key,sha512));
    }

    function gen_rand($length = 512)
    {
        $rstr = crypt_random_string($length);
        return $rstr;
    }

    function rand($min=0,$max=9){
        return mt_rand($min,$max);
    }

    //$ran_map='pkc00###7', then fill ### with random characters
    //all are from base64. base64 with %& replace /-;
    public function gen_guid($ran_map='pkc00###900',$rlen=16)
    {
        $p=new prsa();
//        $ranGuid = $p->base64url_encode($p->gen_rand($rlen));
        for($c=0;$c<strlen($ran_map);$c++){
            if($ran_map[$c]=='#'){
                $ran_map[$c]=$p->rand(0,9);
            }
        }
        return $ran_map;
    }
    public function gen_keyid($ran_map='#00',$rlen=2)
    {
        $p=new prsa();
        for($c=0;$c<strlen($ran_map);$c++){
            if($ran_map[$c]=='#'){
                $ran_map[$c]=$p->rand(1,9);
            }
        }
        return $ran_map;
    }

    //32bits,ABDEFGHJabdefghijkmnpqrtuy356789,total 32. to avoid misleading characters.
    public function gen_password($plen=11,$rpassword=null,$mymap='ABDEFGHJabdefghijkmnpqrtuy356789')
    {
        $p=new prsa();
        if($rpassword==null)$rpassword = $p->gen_rand($plen);
//        echo $rpassword;
        for($c=0;$c<strlen($rpassword);$c++){
            $rpassword[$c]=$mymap[ord($rpassword[$c])&0x1f];
        }
        return $rpassword;
    }
    function gen_rand_strong($pubkey, $length = 512)
    {
        $rstr = $this->pub_encrypt($pubkey, crypt_random_string($length));
        echo $rstr;
        return $rstr;;
    }

    //generate RSA
    function gen_rsa_key($length = 1024)
    {
        $rsa = new Crypt_RSA();
        extract($rsa->createKey($length)); // $publickey,$privatekey
        return array($privatekey, $publickey);
    }

    function get_length($prikey)
    {
        return 1024;
    }

    //
    function pub_encrypt_strkey($pubkey, $text, $padding = pkcs1)
    {
        $this->rsa_key = new Crypt_RSA();
        $this->rsa_key->setEncryptionMode($padding);
        $this->rsa_key->loadKey($pubkey);
        return $this->rsa_key->encrypt($text);
    }

    function pub_decrypt_strkey($pubkey, $text, $padding = pkcs1)
    {
        $this->rsa_key = new Crypt_RSA();
        $this->rsa_key->setEncryptionMode($padding);
        $this->rsa_key->loadKey($pubkey);
        return $this->rsa_key->decrypt($text);
    }

    function pri_encrypt_strkey($prikey, $text, $padding = pkcs1)
    {
        $this->rsa_key = new Crypt_RSA();
        $this->rsa_key->setEncryptionMode($padding);
        $this->rsa_key->loadKey($prikey);
        return $this->rsa_key->encrypt($text);
    }

    function pri_decrypt_strkey($prikey, $text, $padding = pkcs1)
    {
        $this->rsa_key = new Crypt_RSA();
        $this->rsa_key->setEncryptionMode($padding);
        $this->rsa_key->loadKey($prikey);
        return $this->rsa_key->decrypt($text);
    }

    function pub_encrypt($pubkey, $text, $padding = pkcs1)
    {
        $pubkey->setEncryptionMode($padding);
        return $pubkey->encrypt($text);
    }

    function pub_decrypt($pubkey, $text, $padding = pkcs1)
    {
        $pubkey->setEncryptionMode($padding);
        return $pubkey->decrypt($text);
    }

    function pri_encrypt($prikey, $text, $padding = pkcs1)
    {
        $prikey->setEncryptionMode($padding);
        return $prikey->encrypt($text);
    }

    function pri_decrypt($prikey, $text, $padding = pkcs1)
    {
        $prikey->setEncryptionMode($padding);
        return $prikey->decrypt($text);
    }

    //load from string
    function load_pub($str_pub)
    {
        $rsa = new Crypt_RSA();
        $rsa->loadKey($str_pub);
        return $rsa;
    }

    function load_pri($str_pub)
    {
        $rsa = new Crypt_RSA();
        $rsa->loadKey($str_pub);
        return $rsa;
    }

    #get IV and password by $passkey.
    function get_iv($passkey, $len = 32)
    {
        $tkey = base64_encode($this->hash($passkey));
        $tkey = substr($tkey, 0, -1) . base64_encode($this->hash($tkey));
        return array(substr($tkey, 0, $len), substr($tkey, -1 - $len, -1));
    }

    #get IV and password by $passkey.
    function get_iv_simple($passkey, $len = 16)
    {
      $tkey = $this->hash($passkey,sha256);
      return array(substr($tkey, 0, $len), substr($tkey, 0, $len));
    }

    #we only support one way of public key.
    function aes_encrypt($passkey, $text, $etype = aes_cbc)
    {
        if ($etype == aes_256_cfb) {
            $aes = new Crypt_AES(aes_cfb);
            $aes->setKeyLength(256);
            $ret = $this->get_iv($passkey, 32);//32 is the byte length of 256
            // the $passkey can't be used as the key didrectly
            $aes->setIV($ret[1]);
            $aes->setKey($ret[0]);
            return $aes->encrypt($text);
        }else if($etype == aes_cbc)
        {
          #old one which using hash
          $aes = new Crypt_AES(aes_cbc);
          $aes->setKeyLength(128);
          $ret = $this->get_iv_simple($passkey, 16);//32 is the byte length of 256
          // the $passkey can't be used as the key didrectly
          $aes->setKey($ret[0]);
          $aes->setIV($ret[1]);
          return $aes->encrypt($text);
          #new one:
        }
    }

    function aes_decrypt($passkey, $ctext, $etype = aes_256_cfb)
    {
        if ($etype == aes_256_cfb) {
            $aes = new Crypt_AES(aes_cfb);
            $aes->setKeyLength(256);
            $ret = $this->get_iv($passkey, 32);//32 is the byte length of 256
            // the $passkey can't be used as the key didrectly
            $aes->setIV($ret[1]);
            $aes->setKey($ret[0]);
            return $aes->decrypt($ctext);
        }else if($etype == aes_cbc){
          #old one
          $aes = new Crypt_AES(aes_cbc);
          $aes->setKeyLength(128);
          $ret = $this->get_iv_simple($passkey, 16);//32 is the byte length of 256
          // the $passkey can't be used as the key didrectly
          $aes->setKey($ret[0]);
          $aes->setIV($ret[1]);
          return $aes->decrypt($ctext);

        }
    }

  function aes_encrypt_simple($passkey, $text, $etype = aes_256_cfb)
  {
    if ($etype == aes_256_cfb) {
      $aes = new Crypt_AES(aes_cfb);
      $aes->setKeyLength(256);
      $ret = $this->get_iv($passkey, 32);//32 is the byte length of 256
      // the $passkey can't be used as the key didrectly
      $aes->setIV($ret[1]);
      $aes->setKey($ret[0]);
      return $aes->encrypt($text);
    }else if($etype == aes_cbc)
    {
      #new one:
      $aes = new Crypt_AES(aes_cbc);
      $aes->setKeyLength(128);
      $iv = aes_key;
      $tkey = '';
      if(strlen($passkey)<16){
        for($c=0;$c<16;$c++){
          if($c<strlen($passkey)){
            $tkey=$tkey.$passkey[$c];
          }else{
            $tkey=$tkey.$iv[$c];
          }
        }
      }else{
        $tkey = substr($passkey,0,16);
      }
      // the $passkey can't be used as the key didrectly
      $aes->setKey($tkey);
      $aes->setIV($iv);
      return $aes->encrypt($text);
    }
  }

  function aes_decrypt_simple($passkey, $ctext, $etype = aes_256_cfb)
  {
    if ($etype == aes_256_cfb) {
      $aes = new Crypt_AES(aes_cfb);
      $aes->setKeyLength(256);
      $ret = $this->get_iv($passkey, 32);//32 is the byte length of 256
      // the $passkey can't be used as the key didrectly
      $aes->setIV($ret[1]);
      $aes->setKey($ret[0]);
      return $aes->decrypt($ctext);
    }else if($etype == aes_cbc){
      #new one:
      $aes = new Crypt_AES(aes_cbc);
      $aes->setKeyLength(128);
      $iv = aes_key;
      $tkey = '';
      if(strlen($passkey)<16){
        for($c=0;$c<16;$c++){
          if($c<strlen($passkey)){
            $tkey=$tkey.$passkey[$c];
          }else{
            $tkey=$tkey.$iv[$c];
          }
        }
      }else{
        $tkey = substr($passkey,0,16);
      }
      // the $passkey can't be used as the key didrectly
      $aes->setKey($tkey);
      $aes->setIV($iv);
      return $aes->decrypt($ctext);
    }
  }

    function base_16($st)
    {
        return bin2hex($st);
    }
    function base64_encode($st)
    {
        return base64_encode($st);
    }
    function base64_decode($st)
    {
        return base64_decode($st);
    }
    function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '&%'), '=');
    }
    function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '&%', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    //function test_hash_aes()
//{
//    // pass communicate with python
//    $passkey = "diminiinnnnn";
//    $rr = $this->hash("diminiinnnnn");
//    echo base64_encode($rr);
//    $tt = $this->aes_encrypt("pkc", "hello world");
//    echo "<br>" . $tt . "<br>";
//    echo "<br>" . base64_encode($tt) . "<br>";
//    echo $this->aes_decrypt("pkc", $tt);
//}
//function test_rsa()
//{
//    $key1 = $this->gen_rsa_key(512);
//    echo ":------------------------------------------------------------:";
//    echo $key1[0];
//    $pub = $key1[1];$pri=$key1[0];
//    echo ":------------------------------------------------------------:";
//    echo $key1[1];
//    echo ":------------------------------------------------------------:";
//
//    echo "------------------------------------------------------------";
//    $str_pub = "-----BEGIN PUBLIC KEY-----\nMFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAMTs7AhZEhAKXX5zWJVa89+bu/SrdoGS\nTHmeG3GzRz3ckt1gcjZKq55eu8iGacBwTDtqK7a8SnlwDdK9tVg2rKUCAwEAAQ==\n-----END PUBLIC KEY-----\n";
//    $str_pri = "-----BEGIN RSA PRIVATE KEY-----\nMIIBOgIBAAJBAMTs7AhZEhAKXX5zWJVa89+bu/SrdoGSTHmeG3GzRz3ckt1gcjZK\nq55eu8iGacBwTDtqK7a8SnlwDdK9tVg2rKUCAwEAAQJAPJ8u3jcFT3jRZU7+8yOH\ntcuMZfquxZ6S+lGI40ysXsdQ7/C6U77Ye1858qtXkRZU+INBnvDO1iO9HqiIdMLT\nAQIhAOsYwcfVug9Fhaxedd3hB5vMSpfuKx/qtIbA91yewf5lAiEA1m9Q6rRMFl/H\nnxsFd0XmKDHOrvKzod9jlvnzBeMY8UECIF9queOIbC6ckedmo0H9fiAOp0vIn3oh\nwUlb8kmGKcg9AiBdmDasSsfPGD0sIAIxviuoLZ011S88nyF721sMncPcQQIhAKW6\n+0MiX7H8J4i02S2vAhNWkh/z1IkrurCeZ9xuTD7w\n-----END RSA PRIVATE KEY-----\n";
//    $pubkey = $this->load_pub($str_pub);
//    $prikey = $this->load_pri($str_pub);
//
//    $cc = $this->pub_encrypt($str_pub, "hello dd");
//    echo $this->pri_decrypt($str_pri, $cc);
//
//    $cc = $this->pri_encrypt($str_pri, "hello dd");
//    echo $this->pub_decrypt($str_pub, $cc);
//
//    echo "------------------------------------------------------------";
//    echo $this->gen_rand_strong($this->load_pub($str_pub), 512);
//}
}

//$p=new prsa();
//$p->test_hash_aes();
//$p->test_rsa();

return;
?>