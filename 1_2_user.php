<?php
include '0_0_db.php';
session_start(); // 初始化会话

if (!isset($_SESSION['user_id'])) {
    header("Location: 1_0_login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_unset(); // 清除会话变量
    session_destroy(); // 销毁会话
    header("Location: 1_0_login.php"); // 重定向到登录页面
    exit;
}

$applySql = "SELECT name FROM common_user WHERE common_user_id = ?";
$stmt = mysqli_prepare($conn, $applySql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userName = $row['name'];
} else {
    echo "用户信息获取失败。";
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>普通用户界面</title>
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
        .user-container {
            background-color: rgba(0, 0, 0, 0.5);
            color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 480px;
            height: 380px;
            text-align: center;
        }
        .user-container h1 {
            font-weight: bold;
            font-size: 36px;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }
        .user-container h2 {
            font-weight: bold;
            font-size: 30px;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
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
    <div class="user-container">
        <h1>普通用户界面</h1>
        <h2>用户 <?php echo $userName?> 欢迎回来</h2>
        <a href="3_1_view_activities.php">
            <button type="button">查看活动</button>
        </a>
        <a href="3_3_view_results.php">
            <button type="button">查看结果</button>
        </a>
        <a href="1_2_user.php?logout=true">
            <button type="button">退出登录</button>
        </a>
    </div>
</body>
</html>