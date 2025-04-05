<?php
include '0_0_db.php'; // 确保 db.php 文件中包含了数据库连接的代码

// 检查是否提交了表单
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activity_id'])) {
    $activityId = $_POST['activity_id'];
    $activityId = mysqli_real_escape_string($conn, $activityId);

    $updateActivityQuery = "UPDATE activity SET status = 2 WHERE activity_id = ?";
    $stmt = mysqli_prepare($conn, $updateActivityQuery);
    mysqli_stmt_bind_param($stmt, "i", $activityId);
    mysqli_stmt_execute($stmt);
    header("Location: 4_1_success.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>删除活动</title>
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
        h1 {
            color: #333;
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
        input[type="text"], input[type="date"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
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
    
        .activity-info {
            display: none;
            margin-top: 10px;
            padding: 10px;
            border: 2px solid #0bf;
            border-radius: 10px;
        }
        .confirm-delete {
            margin-top: 10px;
        }
    </style>
    <script>
        function toggleActivityInfo(id) {
            var infoBlock = document.getElementById('info-' + id);
            if (infoBlock.style.display === "none" || infoBlock.style.display === "") {
                infoBlock.style.display = "block";
            } else {
                infoBlock.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <h1>删除活动</h1>
    <a href="1_1_admin.php"><input type='button' value='返回管理员界面' /></a>
    <h2>选择活动进行删除</h2>
    <ul>
        <?php
        // 修改查询以仅显示 status 为 1 的活动
        $sql = "SELECT * FROM activity WHERE status = 1";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($activity = mysqli_fetch_assoc($result)) {
                echo "<li>";
                echo "<button type='button' class='activity-button' onclick='toggleActivityInfo(" . $activity['activity_id'] . ")'>" . htmlspecialchars($activity['title']) . "</button>";
                echo "<div id='info-" . $activity['activity_id'] . "' class='activity-info'>";
                echo "<p>时间: " . htmlspecialchars($activity['time']) . "</p>";
                echo "<p>地点: " . htmlspecialchars($activity['location']) . "</p>";
                echo "<p>参与人数: " . htmlspecialchars($activity['number_of_participant']) . "</p>";
                echo "<p>要求: " . htmlspecialchars($activity['asks']) . "</p>";
                echo "<div class='confirm-delete'>";
                echo "<form action='' method='post' onsubmit='return confirm(\"确定要删除这个活动吗？\")'>";
                echo "<input type='hidden' name='activity_id' value='" . $activity['activity_id'] . "'>";
                echo "<input type='submit' value='确认删除'>";
                echo "</form>";
                echo "</div>";
                echo "</li>";
            }
            mysqli_free_result($result);
        } else {
            //echo "<li>没有可显示的活动或查询失败。</li>";
        }
        ?>
    </ul>
    <?php
    if (isset($_GET['updated']) && $_GET['updated'] == 1) {
        echo "<p>活动状态已更新为已删除！</p>";
    }
    ?>
</body>
</html>


