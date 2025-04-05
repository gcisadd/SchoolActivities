<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    die("用户未登录，请先登录。");
}

// 查询所有过期活动
$query = "SELECT * FROM timeout_activities";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("查询失败: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看过期活动</title>
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
    </style>
</head>
<body>
<ul>
<!-- <div class="user-container"> -->
    <h1>查看过期活动</h1>
    <input type='submit' value='返回管理员界面' onclick='window.location.href="1_1_admin.php"'>
    <h2>过期活动信息</h2>
    <?php
    $sql = "SELECT * FROM activity WHERE status = 2";
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
            echo "</div>";
            echo "</li>";
        }
        mysqli_free_result($result);
    }
    ?>
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
    <ul>
</body>
</html>

<?php
// 关闭数据库连接
mysqli_close($conn);
?>