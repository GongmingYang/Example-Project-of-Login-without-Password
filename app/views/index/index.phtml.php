<!DOCTYPE html>
<meta charset="UTF-8">
<html>
<head>
<!--    <meta name="baidu_union_verify" content="c05f99d9fd7ed9094be4f18a7cdfbebe">-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <style type="text/css">
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            max-width: 360px;
            padding: 19px 29px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }
        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }
        .form-signin input[type="text"],
        .form-signin input[type="password"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 15px;
            padding: 7px 9px;
        }

        img {
            border: 0;
            max-width: 100% !important;
            vertical-align: middle;
            padding: 7px;
            border-radius: 6px;
        }

    </style>

    <link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script type="text/javascript" src="/js/pkc_api.js"></script>
    <script type="text/javascript" src="/js/pkc_html.js"></script>
    <script type="text/javascript" src="/js/qrcodejs/qrcode.js"></script>

</head>
<H1 align="center" style="color: maroon ">HowdyPay 支付系统</H1>
    <body style="background:url('/pic/Rock.jpg');">
    <div class="container">
        <div class="row">

            <div class="form-signin " style="width:330px;height: 190px; background: #5895ad;">
                <form action="/login/login"  method="POST" class="form-inline" role="form">
                    <div class="form-group">
                    <input class="form-control"  style="width:230px;" id="user_name" name="user_name" type="text"  placeholder="用户名">
                    <input class="form-control"  style="width:230px;" placeholder="密码" name="password" type="password" value="">
                    <button type="submit"  class="btn btn-default" id="user_login" style="width:230px;">登录</button>
                    </div>
                </form>
            </div>
                <div id="tqrcode"></div>
            <div class="form-signin " >  You can scan it to login using <a href="http://www.publickeycenter.com">public-key-APP</a></div>


        </div>

    </div>

<script type="text/javascript">

    function pkc_gen_QRcode(div_id,data,width,height)
    {
        var div_qr = document.getElementById(div_id);//top
        div_qr.classList.add('form-signin');
        div_qr.style.width = width+'px';
        div_qr.style.height = height+'px';
        div_qr.style.marginTop = '10px';
        div_qr.style.marginBottom = '10px';
        div_qr.style.backgroundColor = '#acad66';

        var qr = document.createElement("div");
        div_qr.appendChild(qr);
        qr.setAttribute("id", "Qrcode");
        var qrcode = new QRCode(qr,
            {
                width: 300,
                height: 300,
                colorDark : '#010202',
                colorLight : '#ffffff',
                correctLevel : QRCode.CorrectLevel.H
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
    pkc_gen_QRcode("tqrcode","<?= $data ?>",330,500);
</script>
</body>

</html>