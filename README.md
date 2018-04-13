# Recoder_API
This is the Example of how to use the API provided by the www.publickeycenter.com
It includes:
     1. Tranditional login with username+password
     2. Passwordless login with www.publicKeyCenter.com
     3. Related API for implement the passwordless login

This project is writen by PHP and Javascript, the framework of PHP is Phalcon.
API include:
   1. Prsa API which includes: AES,Sha256,RSA,Random, You can check pkc_api.js for it
   2. Pkc's passwordless login, You can check pkc_html.js for it.
   3. Website to allow access of passwordless login is in LoginControler.php, functions:
       LAction()
       _get_pubkey_from_pkc()
       QAction()
      
   The key code is (you can check LoginControler.php for details):
       $p = new prsa();
       /*get {'tid':token_id,'tm':time(),'rk':randomKey}<app_pri> using Server_pri_key*/
        $n_dt1 = $p->pri_decrypt_strkey(Server_pri_key, base64_decode($n_dt));
       /*Try to decode {'tid':token_id,'tm':time(),'rk':randomKey} using $app_pub_key
        * If $n_dt is normal json code, we can say it is valid user who is the holder of this app_pub_key
        * */
        $n_dt = json_decode($p->pub_decrypt_strkey($app_pub_key, $n_dt1),true);
    If $n_dt is normal array, then , the passwordless should be good.
    
    
    Gongming
