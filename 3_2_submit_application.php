<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 检查是否提交了表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activityId = $_POST['activity_id'];
    $commonUserId = $_SESSION['user_id']; // 从会话中获取用户ID

    // 检查用户是否已经报名了这个活动
    $checkQuery = "SELECT * FROM application WHERE common_user_id = ? AND activity_id = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "ii", $commonUserId, $activityId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<h1>报名失败</h1><span style='font-size: 25px; color: red;'>您已经报名了这个活动，不能重复报名。</span><br><br>";
        echo "<input type='button' value='返回普通用户界面' onclick='window.location.href=\"1_2_user.php\"' />";
    } else {
        // 插入报名信息
        $insertQuery = "INSERT INTO application (common_user_id, activity_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "ii", $commonUserId, $activityId);
        mysqli_stmt_execute($stmt);

        // 插入报名结果到 application_result 表，result 默认为 0（待审核）
        $insertResultQuery = "INSERT INTO application_result (application_id, result) VALUES ((SELECT MAX(application_id) FROM application), 0)";
        $stmt = mysqli_prepare($conn, $insertResultQuery);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            header("Location: 4_2_success.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($stmt);
}

// 关闭数据库连接
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>报名失败</title>
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
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="date"], input[type="number"], textarea {
            width: 20%;
            padding: 10px;
            margin-bottom: 1px;
            border: 2px solid #0bf;
            border-radius: 10px;
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
        ul {
            list-style-type: none; /* 去除列表项前的黑点 */
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        .activity-button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            width: 20%; /* 使按钮占满整个列表项 */
        }
        .activity-button:hover {
            background-color: #566;
        }
    </style>
</head>