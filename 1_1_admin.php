<?php
include '0_0_db.php';
session_start(); // 初始化会话

// 退出登录功能
if (isset($_GET['logout'])) {
    session_unset(); // 清除会话变量
    session_destroy(); // 销毁会话
    header("Location: 1_0_login.php"); // 重定向到登录页面
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员界面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4f4;
            background-image: url('bj4.gif');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .admin-container {
            background-color: rgba(0, 0, 0, 0.5);
            color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 480px;
            height: 560px;
            text-align: center;
        }
        .admin-container h1 {
            font-weight: bold;
            font-size: 36px;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .admin-container h2 {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 30px;
            margin-top: 10px;
        }
        button {
            display: block;
            width: 100%;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #566;
        }
        a {
        text-decoration: none; /* 去除链接下划线 */
        color: inherit; /* 继承父元素的颜色 */
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>管理员界面</h1>
        <h2>管理员 Root 欢迎回来</h2>
        
        <a href="2_7_show_activity.php?action=check">
            <button type="button">查看活动</button>
        </a>

        <a href="2_1_new_activity.php?action=new">
            <button type="button">新增活动</button>
        </a>
        
        <a href="2_2_edit_activity.php?action=edit">
            <button type="button">修改活动</button>
        </a>

        <a href="2_6_timeout.php?action=view_expired">
            <button type="button">过期活动</button>
        </a>

        <a href="2_4_check_applications.php?action=check">
            <button type="button">审核申请</button>
        </a>
        
        <a href="1_1_admin.php?logout=true">
            <button type="button">退出登录</button>
        </a>
    </div>
</body>
</html>