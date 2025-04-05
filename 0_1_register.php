<?php
include '0_0_db.php'; // 包含数据库连接

// 设置默认用户类型为普通用户
$type = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $name = $_POST['name'];

    // 插入user表
    $stmt1 = $conn->prepare("INSERT INTO user (user_id, password, name) VALUES (?, ?, ?)");
    $stmt1->bind_param("iis", $user_id, $password, $name);
    if ($stmt1->execute()) {
        // 插入user_type表
        $stmt2 = $conn->prepare("INSERT INTO user_type (user_id, type) VALUES (?, ?)");
        $stmt2->bind_param("ii", $user_id, $type);
        
        if ($stmt2->execute()) {
            // 插入common_user表
            $stmt3 = $conn->prepare("INSERT INTO common_user (common_user_id, name) VALUES (?, ?)");
            $stmt3->bind_param("is", $user_id, $name);
            if($stmt3->execute()){
                header("Location: 1_0_login.php");
            } else {
                echo "<p>Error: " . $stmt3->error . "</p>";
            }
        } else {
            echo "<p>Error: " . $stmt2->error . "</p>";
        }
    } else {
        echo "<p>Error: " . $stmt1->error . "</p>";
    }
    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column; /* 垂直排列 */
            height: 85vh;
            margin: 0;
            background-color: #f4f4f4f4;
            background-image: url('bj5.gif');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }
        .register-container {
            background-color: rgba(0, 0, 0, 0.5); /* 灰色半透明背景 */
            color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 600px; /* 设置表单宽度 */
            height: 440px; /* 设置表单高度 */
        }
        .register-container h1 {
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
            color: #ffffff; /* 白色文字 */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* 文字阴影 */
            background: linear-gradient(to right, #ff7e5f, #feb47b); /* 渐变背景 */
            background-clip: text;
            -webkit-text-fill-color: transparent; /* 使文字填充为透明，显示背景 */
            margin-bottom: 30px; /* 增加底部间距 */
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 83%;
            padding: 15px;
            margin-bottom: 10px;
            border: 2px solid #0bf;
            border-radius: 10px;
        }
        input[type="submit"] {
            width: 88.8%;
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
            width: 88.8%;
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
    <div class="register-container">
        <h1>注册</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="text" name="user_id" placeholder="USER_ID" required>
            <input type="password" name="password" placeholder="PASSWORD" required>
            <input type="text" name="name" placeholder="NAME" required>

            <input type="submit" value="注册">
        </form>
        <button type="button" onclick="window.location.href='1_0_login.php'">返回登录页面</button>
    </div>
</body>
</html>