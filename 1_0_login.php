<?php
include '0_0_db.php';
session_start(); // 初始化会话

// 确保数据库连接成功
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // 使用预处理语句进行查询
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_id=? AND password=?");
    if ($stmt === false) {
        die("准备语句失败: " . $conn->error);
    }
    $stmt->bind_param("si", $user_id, $password); // "si"表示第一个参数是字符串，第二个参数是字符串
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // 用户存在，查询user_type表获取用户类型
        $stmt_type = $conn->prepare("SELECT type FROM user_type WHERE user_id=?");
        if ($stmt_type === false) {
            die("准备语句失败: " . $conn->error);
        }
        $stmt_type->bind_param("i", $user_id);
        $stmt_type->execute();
        $result_type = $stmt_type->get_result();

        if ($result_type->num_rows > 0) {
            $row_type = $result_type->fetch_assoc();
            $type = $row_type['type'];
            // 存储用户ID到会话
            $_SESSION['user_id'] = $user_id;

            if ($type == 1) {
                // 管理员
                header("Location: 1_1_admin.php");
                exit;
            } else {
                // 普通用户
                header("Location: 1_2_user.php");
                exit;
            }
        } else {
            echo "<p>用户类型未定义</p>";
        }
        $stmt_type->close();
    } else {
        $error = "用户名或密码错误";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            flex-direction: column; /* 垂直排列 */
            align-items: center;
            height: 80vh;
            margin: 0;
            background-color: #f4f4f4f4;
            background-image: url('bj5.gif');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }
        .login-container {
            background-color: rgba(0, 0, 0, 0.5); /* 灰色半透明背景 */
            color: #ffffff;
            padding: 5px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 600px; /* 设置表单宽度 */
            height: 400px; /* 设置表单高度 */
        }
        h1 {
        font-weight: bold;
        font-size: 50px;
        background: linear-gradient(to right, #ff7e5f, #feb47b);
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 25px;
        }
        
        .login-container h1 {
        font-weight: bold;
        font-size: 44px;
        background: linear-gradient(to right, #ff7e5f, #feb47b);
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 25px;
        }
        .title {
            font-weight: bold;
            font-size: 66px;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* 文字阴影 */
            margin-bottom: 30px; /* 增加底部间距 */
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 82%;
            padding: 15px;
            margin-bottom: 10px;
            border: 2px solid #0bf;
            border-radius: 10px;
        }
        input[type="submit"] {
            width: 87.2%;
            padding: 15px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #556;
        }
        button {
            width: 87.2%;
            padding: 15px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #556;
        }
        .error-message {
            color: #ff0000;
            margin-bottom: 10px;
        }
    </style>

</head>
<body>
    <h1 class="title">校园活动征召系统</h1>
    <div class="login-container">
        <h1>登录</h1>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" name="user_id" placeholder="USER_ID" required>
            <input type="password" name="password" placeholder="PASSWORD" required>
            <input type="submit" value="登录">
        </form>
        <button type="button" onclick="window.location.href='0_1_register.php'">注册新账户</button>
    </div>
</body>
</html>