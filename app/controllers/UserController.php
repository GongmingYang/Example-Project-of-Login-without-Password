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

//This is use to download private_file, change user's informations,change key's status.
class UserController extends ControllerBase
{
//FYI
    public function MainAction()
    {
        $this->view->c_table = [];
        $this->view->g_table = [];
        $this->view->n_table = [];

        $uid = $this->session->get('uid');

        $p=new prsa();
        if($uid==Fid_False || false==$this->_check_session())
        {
            $this->response->redirect(Url_login);
            return;
        }

        //get all information
        $sql = "select * from s_record as r left join s_type as t on r.type_id = t.type_id  where r.uid = :uid and type='g' order by r.type_id ";
        $this->view->g_table = $this->db->query($sql,['uid'=>$uid])->fetchAll();

        $sql = "select * from s_record as r left join s_type as t on r.type_id = t.type_id  where r.uid = :uid and type='c'  order by r.type_id";
        $this->view->c_table = $this->db->query($sql,['uid'=>$uid])->fetchAll();

        $sql = "select * from s_record as r left join s_type as t on r.type_id = t.type_id  where r.uid = :uid and type='n'  order by r.type_id";
        $this->view->n_table = $this->db->query($sql,['uid'=>$uid])->fetchAll();

        return;
    }

    public function ClassAction()
    {
        if (!$this->request->isPost())
        {
            $this->response->redirect(Url_login);
            return;
        }

        $p=new prsa();
        if(false==$this->_check_session())
        {
            $this->response->redirect(Url_login);
            return;
        }

        $tid = $this->request->getPost('type_id', 'string');
        $m_uid = $this->request->getPost('uid', 'string');
        $pid = $this->request->getPost('period_id', 'string');
        $lcount =$this->request->hasPost('listen_count')? $this->request->getPost('listen_count', 'int'):-1;
        $rcount =$this->request->hasPost('read_count')? $this->request->getPost('read_count', 'int'):-1;
        $uid = $this->session->get('uid');

        if($uid!=$m_uid)
        {
            $this->response->redirect(Url_login);
            return;
        }
        $dbt=S_record::findFirst(['uid=:uid: and period_id=:pid: and type_id=:tid:',
            'bind'=>['uid'=>$uid,'pid'=>$pid,'tid'=>$tid]]);
        if(-1 != $lcount){
            $dbt->listen_count=$lcount;
        }
        if(-1 != $rcount){
            $dbt->read_count=$rcount;
        }
        $dbt->save();
    }
    public function GuanxiuAction(){
        if (!$this->request->isPost())
        {
            $this->response->redirect(Url_login);
            return;
        }

        $p=new prsa();
        if(false==$this->_check_session())
        {
            $this->response->redirect(Url_login);
            return;
        }

        $tid = $this->request->getPost('type_id', 'string');
        $m_uid = $this->request->getPost('uid', 'string');
        $pid = $this->request->getPost('period_id', 'string');
        $lcount =$this->request->hasPost('count')? $this->request->getPost('count', 'int'):-1;
        $rcount =$this->request->hasPost('sum_time')? $this->request->getPost('sum_time', 'int'):-1;
        $uid = $this->session->get('uid');

        if($uid!=$m_uid)
        {
            $this->response->redirect(Url_login);
            return;
        }
        $dbt=S_record::findFirst(['uid=:uid: and period_id=:pid: and type_id=:tid:',
            'bind'=>['uid'=>$uid,'pid'=>$pid,'tid'=>$tid]]);
        if(-1 != $lcount){
            $dbt->count=$dbt->count + $lcount;
        }
        if(-1 != $rcount){
            $dbt->sum_time=$dbt->sum_time + $rcount;
        }
        $dbt->save();
        $ret = [];
        $ret['count']=$dbt->count;
        $ret['sum_time']=$dbt->sum_time;
        $this->rsend($ret);
        return;
    }
    public function NiansongAction()
    {
        if (!$this->request->isPost())
        {
            $this->response->redirect(Url_login);
            return;
        }

        $p=new prsa();
        if(false==$this->_check_session())
        {
            $this->response->redirect(Url_login);
            return;
        }

        $tid = $this->request->getPost('type_id', 'string');
        $m_uid = $this->request->getPost('uid', 'string');
        $pid = $this->request->getPost('period_id', 'string');
        $lcount =$this->request->hasPost('count')? $this->request->getPost('count', 'int'):'f';
        $uid = $this->session->get('uid');

        if($uid!=$m_uid)
        {
            $this->response->redirect(Url_login);
            return;
        }
        $dbt=S_record::findFirst(['uid=:uid: and period_id=:pid: and type_id=:tid:',
            'bind'=>['uid'=>$uid,'pid'=>$pid,'tid'=>$tid]]);
        if('f' != $lcount){
            $dbt->count=$dbt->count + $lcount;
        }
        $dbt->save();
        $ret = [];
        $ret['count']=$dbt->count;
        $this->rsend($ret);
        return;
    }

}