<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加用户</title>
    <style>
        div {
            font-size: 20px;
            color: red;
        }
    </style>
</head>
<body>
<div>您的账号：{{$userModel->username}}</div>
<div>您的密码：{{$password}}</div>
<div>您的邮件：{{$userModel->email}}</div>
{{--<div>您的手机号码：{{$userModel->phone}}</div>--}}
</body>
</html>
