<?php
include '0_0_db.php'; // 确保 db.php 文件中包含了数据库连接的代码

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // $managedUserId = $_POST['111'];
    $title = $_POST['title'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $participants = 0;
    $asks = $_POST['asks'];
    $status = $_POST['status'];

    // 防止 SQL 注入
    // $managedUserId = mysqli_real_escape_string($conn, $managedUserId);
    $title = mysqli_real_escape_string($conn, $title);
    $time = mysqli_real_escape_string($conn, $time);
    $location = mysqli_real_escape_string($conn, $location);
    $participants = 0;//mysqli_real_escape_string($conn, $participants);
    $asks = mysqli_real_escape_string($conn, $asks);
    $status = mysqli_real_escape_string($conn, $status);

    // 构建插入语句
    $sql = "INSERT INTO activity (title, time, location, number_of_participant, asks, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $title, $time, $location, $participants, $asks, $status);
    
    if ($stmt->execute()) {
    //echo "新活动添加成功";
    header("Location: 4_1_success.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增活动</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4f4;
            background-image: url('bj.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }
        form {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="date"], input[type="number"], textarea, select {
            width: 20%;
            padding: 10px;
            margin-bottom: 1px;
            border: 2px solid #0bf;
            border-radius: 10px;
        }
        select:hover {
            background-color: #abc;
        }
        input[type="submit"], input[type="button"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover, input[type="button"]:hover {
            background-color: #566;
        }
    </style>
</head>
<body>
    <h1>新增活动</h1>
    <input type='submit' value='返回管理员界面' onclick='window.location.href="1_1_admin.php"' />
    <h2>填写活动信息</h2>
    <form action="2_1_new_activity.php" method="post">
        <!-- <label for="managed_user_id">管理员 ID:</label> -->
        <!-- <input type="number" id="managed_user_id" name="managed_user_id" required> -->

        <label for="title">活动标题:</label>
        <input type="text" id="title" name="title" required>

        <label for="time">活动时间:</label>
        <input type="date" id="time" name="time" required>

        <label for="location">活动地点:</label>
        <input type="text" id="location" name="location" required>

        <!-- <label for="participants">参与人数:</label> -->
        <!-- <input type="number" id="participants" name="participants" required> -->

        <label for="asks">活动要求:</label>
        <textarea id="asks" name="asks" required></textarea>

        <label for="status">活动状态:</label>
        <select id='status' name='status' required>
            <option value='1'>活动开启</option>
            <option value='2'>活动关闭</option>
        </select>

        <input type="submit" value="提交">
    </form>
    <!-- <input type="submit" value="返回管理员界面" onclick="window.location.href='1_1_admin.php'" /> -->
</body>
</html>