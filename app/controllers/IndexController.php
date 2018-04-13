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
class IndexController extends ControllerBase
{
    //every time people typing over website, this function is visited.
    public function indexAction()
    {
        if ($this->session->has('st') && $this->session->get('st') != Fid_False)
        {
            $this->response->redirect(Url_main);
        } else {

            $this->view->server_pub = Server_pub_key;// tell the client the pub_key of server
            //initial session
            $this->session->set('uname',Fid_False);
            $this->session->set('uid',Fid_False);
            $this->session->set('utm',0);
            $this->session->set('st',Fid_False);
            $this->session->set('gid',Fid_False);


            $sid = $this->session->getId();
            $token_id = $this->_gen_shortid_url($sid);
            $token_id = 'test_token_id123';

            $m = $this->cache_init(Memcached_port);

            //map short_num to session_id
            $this->cache_set($m,$token_id,['sid'=>$sid,'status'=>Fid_False,'guid'=>Fid_False],300000);//test only should be 300
            $rsp=[];
            $rsp['guid']=Server_guid.'#'.Server_keyid;

            //the data will be showed in 2D barcode
            $this->view->data=Url_server_short.'/login/l/'.$token_id;

        }
        return;//call views/index.php
   }
}