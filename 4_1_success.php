<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    die("用户未登录，请先登录。");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>操作成功</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 1px;
            padding: 15px;
            background-color: #f4f4f4f4;
            background-image: url('bj.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }
        h2 {
            color: #333;
        }
        .success-message {
            color: #008000; /* 绿色 */
            font-size: 24px; /* 字体加大 */
            font-weight: bold;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #556;
        }
    </style>
</head>
<body>
<div class="user-container">
    <h1>操作结果</h1>
    <?php
    echo "<span style='font-size: 25px; color: green;'>操作成功。</span><br><br>"
    ?>

    <button type="button" onclick="window.location.href='1_1_admin.php'">返回管理员界面</button>
</div>
</body>
</html>

<?php
// 关闭数据库连接
mysqli_close($conn);
?>