<?php
include '0_0_db.php'; // 确保 db.php 文件中包含了数据库连接的代码

// 检查是否提交了表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        
    
    $activityId = $_POST['activity_id'];
    $title = $_POST['title'] ?? '';
    $time = $_POST['time'] ?? '';
    $location = $_POST['location'] ?? '';
    // $participants = $_POST['participants'] ?? '';
    $asks = $_POST['asks'] ?? '';
    $status = $_POST['status'] ?? '';
    // 检查活动时间是否早于当前日期
    $current_date = date('Y-m-d');
    // if ($time < $current_date) {
    //     // echo "Error: 活动日期不能早于当前日期。";
    //     header("Location: 2_6_date_error.php");
    // } 
    // 防止 SQL 注入
    $activityId = mysqli_real_escape_string($conn, $activityId);
    $title = mysqli_real_escape_string($conn, $title);
    $time = mysqli_real_escape_string($conn, $time);
    $location = mysqli_real_escape_string($conn, $location);
    // $participants = mysqli_real_escape_string($conn, $participants);
    $asks = mysqli_real_escape_string($conn, $asks);
    $status = mysqli_real_escape_string($conn, $status);

    // 构建更新语句，只更新非空字段
    $updateFields = [];
    $updateFields[] = "title = '$title'";
    $updateFields[] = "time = '$time'";
    $updateFields[] = "location = '$location'";
    // $updateFields[] = "number_of_participant = '$participants'";
    $updateFields[] = "asks = '$asks'";
    $updateFields[] = "status = '$status'";

    if (!empty($updateFields)) {
        $updateQuery = "UPDATE activity SET " . implode(", ", $updateFields) . " WHERE activity_id = '$activityId'";
        if (mysqli_query($conn, $updateQuery)) {
            //echo "活动信息更新成功";
            header("Location: 4_1_success.php");
        } else {
            echo "Error: " . $updateQuery . "<br>" . mysqli_error($conn);
        }
    }
} else {
    // 如果不是 POST 请求，显示活动列表和修改表单
    $sql = "SELECT * FROM activity";
    $result = mysqli_query($conn, $sql);
}
// 关闭数据库连接
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改活动</title>
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
            /* background-color: #fff; */
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
            font-size: 16px; /* 确保字体大小一致 */
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
        input[type="submit"]:hover, input[type="button"]:hover, select:hover {
            background-color: #566;
        }
        select:hover {
            background-color: #abc;
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
<body>
    <h1>修改活动</h1>
    <ul>
    <?php
        if (isset($result) && $result) {
            echo "<input type='button' value='返回管理员界面' onclick='window.location.href=\"1_1_admin.php\"' />";
            echo "</form>";
            echo "<h2>选择活动进行修改</h2>";
            while ($activity = mysqli_fetch_assoc($result)) {
                
                echo "<li>";
                echo "<button type='button' class='activity-button' onclick='window.location.href=\"2_2_edit_activity.php?activity_id=" . $activity['activity_id'] . "\"'>" . htmlspecialchars($activity['title']) . "</button>";
                echo "</li>";
            }
            mysqli_free_result($result);
        } else {
            //echo "<li>没有可显示的活动或查询失败。</li>";
        }
    ?>
    </ul>
    
    <?php
    if (isset($_GET['activity_id'])) {
        $activityId = $_GET['activity_id'];
        include '0_0_db.php'; // 重新包含数据库连接
        $sql = "SELECT * FROM activity WHERE activity_id = '$activityId'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $activity = mysqli_fetch_assoc($result);
            echo "<h2>修改活动: " . htmlspecialchars($activity['title']) . "</h2>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='activity_id' value='" . $activity['activity_id'] . "'>";

            echo "<label for='title'>活动标题:</label>";
            echo "<input type='text' id='title' name='title' value='" . htmlspecialchars($activity['title']) . "' required>";

            echo "<label for='time'>活动时间:</label>";
            echo "<input type='date' id='time' name='time' value='" . htmlspecialchars($activity['time']) . "' required>";

            echo "<label for='location'>活动地点:</label>";
            echo "<input type='text' id='location' name='location' value='" . htmlspecialchars($activity['location']) . "' required>";

            // echo "<label for='participants'>参与人数:</label>";
            // echo "<input type='number' id='participants' name='participants' value='" . htmlspecialchars($activity['number_of_participant']) . "' required>";

            echo "<label for='asks'>活动要求:</label>";
            echo "<textarea id='asks' name='asks' required>" . htmlspecialchars($activity['asks']) . "</textarea>";

            echo "<label for='status'>活动状态:</label>";
            echo "<select id='status' name='status' required>";
            echo "<option value='1'" . ($activity['status'] == 1 ? " selected" : "") . ">活动开启</option>";
            echo "<option value='2'" . ($activity['status'] == 2 ? " selected" : "") . ">活动关闭</option>";
            echo "</select>";

            echo "</p><input type='submit' value='提交修改'>";
            echo "</form>";
            // 添加返回管理员页面的按钮
            // echo "<input type='button' value='返回管理员界面' onclick='window.location.href=\"1_1_admin.php\"' />";
            
            mysqli_free_result($result);
        } else {
            echo "<p>未找到活动或活动不存在。</p>";
        }
        mysqli_close($conn); // 关闭重新打开的连接
    }
    ?>
</body>
</html>