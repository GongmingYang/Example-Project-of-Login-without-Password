/**
 * Author: Gongming Yang
 * Date: 6/20/2016
 * Time: 7:05 PM
 */
////////////////////////////////////////////////////////////////////////////////////////////
// Common functions
////////////////////////////////////////////////////////////////////////////////////////////
//We always uss post, and with h_token, so we use this ajax.
// mydata will be like : {}
// fn_ok will be fn_ok(data){};

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['exports'], factory);
    } else if (typeof exports === 'object' && typeof exports.nodeName !== 'string') {
        // Node, CommonJS-like
        factory(module.exports);
    } else {
        factory(root);
    }
})(this, function (exports)
{
    /*
    * @param div_id id of the div contain the Qrcode
    * @param */
    function pkc_gen_QRcode(div_id, data, width, height) {
        var div_qr = document.getElementById(div_id);//top
        div_qr.classList.add('form-signin');
        div_qr.style.width = width + 'px';
        div_qr.style.height = height + 'px';
        div_qr.style.marginTop = '10px';
        div_qr.style.marginBottom = '10px';
        div_qr.style.backgroundColor = '#acad66';

        var qr = document.createElement("div");
        div_qr.appendChild(qr);
        qr.setAttribute("id", div_id + "Qrcode");
        var qrcode = new QRCode(qr,
            {
                width: 300,
                height: 300,
                colorDark: '#010202',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        qrcode.makeCode(data);
        //add advertise
        //Could you please let this advertisement can be displayed, because Our supports need this.
        // var dd = document.createElement("div");
        // dd.setAttribute("id", "CleanDiv");
        // dd.onclick = hideMask;//给元素添加点击事件
        var img = document.createElement("img");
        img.style.float = "right";//js设置样式
        img.src = "/pic/milaxy.jpeg";
        img.style.width = "320px";
        img.style.float = "right";
        qr.appendChild(img);
        // document.body.appendChild(dd);//把元素放进body标签里面
        // document.getElementById(div_id).appendChild(qr);
    }

    /*example:
    * <input type='file'  onchange="pkc_open_file(event,f_onload)"><br>
    *  function f_onload(keys){ alert(keys);}
    * */
    function pkc_open_file (event,f_onload)
    {
        var input = event.target;
        var reader = new FileReader();
        reader.onload=function(){
            var data = this.result;
            var ret = {};
            ret['pri_key']='pri';
            ret['pub_key']='pub';
            //pop-out password input dialog
            var pw = prompt("Password for open the private_key file","");
            if (pw == null){
                alert("Without password, you can't Parse the file");
                return;
            }
            //Parse the private file
            var prsa = new Prsa();
            var c_file = prsa.base64_decode(data);
            var djson = prsa.aes_decrypt(pw,c_file);
            var fdata = JSON.parse(djson);
            // $rpw = $p->pub_encrypt_strkey($this->pub_key, $p->hash($pwd));
            // $prif['prk'] = $p->base64url_encode($p->aes_encrypt($rpw, $pri_key));
            // var pri_key1 = fdata['prk'];
            // var output = document.getElementById('output');
            f_onload(ret)
        }
        reader.readAsBinaryString(input.files[0]);
    }

    function pkc_binary() {
        jQuery.ajax({
            'url': "/my/image/name.png",
            'type': "GET",
            // "data":mydata,
            'dataType': "binary",
            'processData': false,
            'success': function (result) {
                // do something with binary data
            }
        });

        $('#myfile').ajaxSubmit(function (data) {
            $("#image").val(data);
        });
    }
    /*
    * pkc send post to url
    * */
    function pkc_send(iurl, mydata, fn_ok, fn_fail) {
        fn_ok = fn_ok || null;
        fn_fail = fn_fail || null;
        jQuery.ajax({
            'url': iurl,
            'type': 'POST',
            "data": mydata,
            "success": fn_ok,
            'error': fn_fail
        });
    }
    function pkcajax(iurl, mydata, fn_ok, fail_message) {
        // mydata[jQuery('.h_token').val()] ='';
        jQuery.ajax({
            'url': iurl,
            'type': 'POST',
            "data": mydata,
            "success": fn_ok,
            'error': function fn_fail(data) {
                console.log(data);
            }
        });
    }

    /*all exports functions and objects*/
    exports.pkc_gen_QRcode=pkc_gen_QRcode;
    exports.pkc_open_file=pkc_open_file;
    exports.pkc_binary=pkc_binary;
    exports.pkc_send=pkc_send;
    exports.pkcajax=pkcajax;

});

