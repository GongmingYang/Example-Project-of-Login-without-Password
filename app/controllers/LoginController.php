<?php
/**
 * Created by PhpStorm.
 * Author: Gongming Yang
 * Date: 6/11/2016
 * Time: 5:24 PM
 */

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Query;

use Phalcon\Cache\Frontend\Data as DataFrontend;
use Phalcon\Cache\Backend\Apc as ApcCache;

//using email and password as the management operations
//This is use to download private_file, change user's informations,change key's status.
class LoginController extends ControllerBase
{

    public function IndexAction()
    {
        return;//call views/index.php
    }

    /**************************************************************
     * API functions
     * ************************************************************/
    /*prepare for 2D-barcode
     *
     * */
    public function QAction()
    {
        //put 's':guid#keyid into the QrBarcode.

        $sid = $this->session->getId();
        $token_id = $this->_gen_shortid_url($sid);
        $token_id = 'test_token_id123';//for test only

        $m = $this->cache_init(Memcached_port);
        $this->session->set('status',Fid_False);//Does not login

        //map short_num to session_id
        $this->cache_set($m,$token_id,['sid'=>$sid,'status'=>Fid_False,'guid'=>Fid_False],300);
        $rsp=[];
        $rsp['guid']=Server_guid.'#'.Server_keyid;

        //This define the url
        $this->view->data=Url_server.'/login/l/'.$token_id;

        //show 2D-Barcode in the view.
    }

    /*Get this website's public_key; just response with the 's':guid#key_id
     * in case the app don't get the information of the 's'
     */
    public function KAction()
    {
        $public_key_id = Server_guid.'#'.Server_keyid;
        $robj=['k'=>$public_key_id,'pub'=>Server_pub_key];
        $this->response->setJsonContent($robj);
        $this->response->send();
    }

    /*@TODO.
    *app login process.
    *login message:{'d':app_guid#key,'dt':{'tid':token_id,'tm':time(),'rk':randomKey}<app_pri><srv_pub><base_64>}
    *success:{'op':'s','dt':{'tm':time(),'rk':randomKey}<server_pri><app_pub>}
    *fail: {'op':'f'} if valid, or nothing
    */
    public function LAction()
    {
        //refuse get
        if (!$this->request->isPost())
        {
            $this->response->redirect(Url_login);
        }
        //check login
        try {
            $n_d = $this->request->getPost('d', 'string');
            $n_dt = $this->request->getPost('dt', 'string');

            //check session: which you can define by your own.
            if (false == $this->_check_session()) {
                return;//do nothing
            }

            $pri_key = Server_pri_key;
            $pub_key = Server_pub_key;
            /*get the login user's public_key from our cached pool(memcache)
            * or from www.publickeycenter.com
            */
            $app_pub_key = $this->_get_pubkey($n_d);//get the public key of the user

            $p = new prsa();
            /*get {'tid':token_id,'tm':time(),'rk':randomKey}<app_pri> using Server_pri_key*/
            $n_dt1 = $p->pri_decrypt_strkey(Server_pri_key, base64_decode($n_dt));
            /*Try to decode {'tid':token_id,'tm':time(),'rk':randomKey} using $app_pub_key
             * If $n_dt is normal json code, we can say it is valid user who is the holder of this app_pub_key
             * */
            $n_dt = json_decode($p->pub_decrypt_strkey($app_pub_key, $n_dt1),true);

            /*In addition, we can check the 'tm' to see whether it is an old messages*/

            /*token_id should the same as the token_id which showed in the 2D-barcode*/
            $token_id = $n_dt['tid'];
            $m = $this->cache_init();
            $t = $this->cache_get($m, $token_id);
            $sid = $t['sid'];//this is the session_id

            /*
             * Here, the user already success for loginning. Next, the web can define it's own
             * ways to handle the first sign up user, or the returned user.
             * e.g.
             * check it is the first user or not?
             * Update User's information. If it is the first time for this user,
             * sign up the user,using the information of this user in the public key center.
             * and build up the information for this user.
            */
            $uid = $this->_update_users(explode("#",$n_d)[0]);
            $this->_update_session_status($sid, explode('#', $n_d)[0],$uid);


            /*response with the success messages*/
            $rsp = [];
            $rsp['op'] = 's';
            //by default, the server need to ready for authenticated by the app
            $rdt = [];
            $rdt['tm'] = time();
            $rdt['rk'] = $n_dt['rk'];//we return back the random key to the user.
            $rsp['dt'] = $p->base64_encode($p->pub_encrypt_strkey($app_pub_key, $p->pri_encrypt_strkey($pri_key, json_encode($rdt))));
            $this->rsend($rsp);

        }catch (Exception $e){
            /*If any error or exception occurs
            It means that the login shouldn't the private_key holder,
            so just refuse silently to avoid attacking.
            */
            return;
        }

    }


    /************************************************************
     *utilize functions
     * **********************************************************/
    /*Get public_key of app.
    */
    public function _get_pubkey($k)
    {
        //check whether the login user's public_key exists in memcache or not
        $m=$this->cache_init();
        $pub_key = $this->cache_get($m,$k);
        if(false==$pub_key) {//get it from pkc
            $pub_key = $this->_get_pubkey_from_pkc($k);
            //put pub_key into memcache for future usage.
            $this->cache_set($m, $k, $pub_key);
        }
        return $pub_key;
    }
    /*Get public_key of app. it will get public_key from www.publicKeyCenter.com */
    /* Please see the API doc for details.
     * message:{"op":"gp","s":"svid#srvkid",
     *"dt":{'d':guid#keyid,"rk".randomKey,'c':'pk'/'all'><srv_pub><base_64>}
     * response:
     */
    public function _get_pubkey_from_pkc($k)
    {
        $p = new prsa();
        $pub=Pkc_pub_key;
        $data=[];$dt=[];
        $data['op']='gp';
        $data['s']=Pkc_k;

        $dt['d']=$k;
        /*@TODO change 'randomKey' to really random Key and record it for checking*/
        $dt['rk']='randomKey';//random key; 'randomKey' is string for test,
        /* $dt['c']='all';*/ /* all means get all information*/
        $dt['c']='pk';

        $xx=$p->pub_encrypt_strkey($pub,json_encode($dt));
        $data['dt']=$p->base64_encode($xx);

        $result = $this->_send_post(Pkc_url_get_pub_key,$data);
        try {
            //get public_key from $result "pub"<srv_pri>.
            $result = json_decode($result);
            $pub_key = $result->pub->pub_key;
            $key_status = $result->pub->kstatus;
            /*@TODO the pub_key is pure text. We will make it encrypted by public_key_center's private key.*/
            if($key_status=='n'){
                /*TODO Here, the $result may also contain the detail information of this GUID*/
                return $pub_key;
            }else{
                return null;
            }
        }catch(Exception $e){
            return null; //do nothing to avoid attack.
        }
    }

    /**************************************************************
     * traditional method to login; It can be defined by your own
     * ************************************************************/
    //traditional method to login
    public function LoginAction()
    {
        if (!$this->request->isPost())
        {
            $this->response->redirect(Url_login);
        }
        $username = $this->request->getPost('user_name', 'string');
        $password = $this->request->getPost('password', 'string');

        $p=new prsa();
        if(true==$this->_check_session())
        {
            $user=Users::findFirst(['uname=:uname:','bind'=>['uname'=>$username]]);
            //the simplest way to check password.
            //You can rewrite this judgement,yet,it is hard to be good enough.
            if(false!=$user && $this->_check_password($password, $user->password))
            {
                $this->session->set('uname',$username);
                $this->session->set('st','s');//means success
                $this->session->set('uid',$user->uid);
                $this->session->set('utm',time());
                $this->response->redirect(Url_main);
                return false;
            }
        }
        $this->session->set('uname',Fid_False);
        $this->session->set('uid',Fid_False);
        $this->session->set('st',Fid_False);

        $this->response->redirect(Url_login);//fail to login
    }

    // in case you need logout
    public function logoutAction()
    {
        $this->session->set('uname', Fid_False);
        $this->session->set('uid', Fid_False);
        $this->session->set('st',Fid_False);
        $this->response->redirect(Url_login);
    }

    public function _update_users($guid){
        $user = Users::findFirst(['guid=:guid:','bind'=>['guid'=>$guid]]);
        if($user==false){
            $user = new Users();
            $user->guid = $guid;
            $user->save();
            //add this user:
            $u_tmp = Users::findFirst(['guid=:guid:','bind'=>['guid'=>$guid]]);
            if($u_tmp!=false)
            {
                $this->_load_user_record($u_tmp->uid);
                return $u_tmp->uid;
            }else {
                return $user->uid;
            }
        }
        return $user->uid;
    }
    public function _update_session_status($sid,$guid,$uid){
        $csid = $this->session->getId();
        session_commit();
        //find specified session
        session_id($sid);
        session_start();

        $this->session->set('gid',$guid);
        $this->session->set('uid',$uid);
        $this->session->set('st','s');//set status of this session as success.
        $this->session->set('utm',time());
        //modify session status;
//            session_destroy();
        session_commit();
        #back to the new one;
        session_id($csid);
        session_start();
        session_commit();
    }

    //This is used to build up users' records
    //This is only related to this example
    public function TXAction($pw,$xx)
    {
        if($pw!='nmamtf')return;
        $this->_load_class();
        $tuser = Users::find();
        foreach($tuser as $u) {
            $this->_load_user_record($u->uid);
        }
        echo 'Finished';
    }
}