<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    die("用户未登录，请先登录。");
}

// 获取用户输入的日期
$inputDate = isset($_GET['date']) ? $_GET['date'] : '';

// 构建查询
if ($inputDate) {
    $query = "SELECT * FROM activity WHERE time = ? AND status = 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $inputDate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // 如果没有提供日期，查询所有活动
    $query = "SELECT * FROM activity WHERE status = 1";
    $result = mysqli_query($conn, $query);
}

if (!$result) {
    die("查询失败: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看活动</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 auto; /* 居中对齐按钮 */
            display: block; /* 使按钮占满整行 */
            width: 100%; /* 使按钮宽度与单元格一致 */
        }
        button:hover {
            background-color: #556;
        }
        .activity-details {
            margin-top: 5px;
            padding: 10px;
            border: 2px solid #0bf;
            border-radius: 10px;
            display: none; /* 默认不显示详细信息 */
        }
        input[type="application"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
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
        input[type="submit"]:hover, input[type="button"]:hover, input[type="application"]:hover {
            background-color: #566;
        }
        input[type="date"] {
        padding: 10px; /* 增加内边距 */
        font-size: 16px; /* 增加字体大小 */
        margin: 10px 0; /* 调整外边距 */
        border: 1px solid #ddd; /* 边框样式 */
        border-radius: 10px; /* 圆角 */
        width: 150px; /* 增加宽度 */
        }
    </style>
</head>
<body>
<div class="user-container">
    <h1>查看活动</h1>
    <input type='submit' value='返回普通用户界面' onclick='window.location.href="1_2_user.php"'>
    <h2>查看活动信息</h2>
    <form action="3_1_view_activities.php" method="get">
        <!-- <label for="date">选择日期:</label> -->
        <h3>选择日期
        <input type="date" id="date" name="date">
        <input type="submit" value="按日期筛选">
    </form>
    <table>
        <tr>
            <th>活动ID</th>
            <th>标题</th>
            <th>时间</th>
            <th>地点</th>
            <th>参与人数</th>
            <th>活动要求</th>
            <th>操作</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['activity_id']) . "</td>";
            //echo "<td>";
            // echo "<button onclick='toggleDetails(" . $row['activity_id'] . ")'>" . htmlspecialchars($row['title']) . "</button>";
            // echo "<div id='details" . $row['activity_id'] . "' class='activity-details' style='display:none; margin-top: 5px;'>";
            //echo "<p><strong>标题:</strong> " . htmlspecialchars($row['title']) . "</p>";
            // echo "<p><strong>时间:</strong> " . htmlspecialchars($row['time']) . "</p>";
            // echo "<p><strong>地点:</strong> " . htmlspecialchars($row['location']) . "</p>";
            // echo "<p><strong>参与人数:</strong> " . htmlspecialchars($row['number_of_participant']) . "</p>";
            // echo "<p><strong>活动要求:</strong> " . htmlspecialchars($row['asks']) . "</p>";
            // echo "</div></td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . htmlspecialchars($row['number_of_participant']) . "</td>";
            echo "<td>" . htmlspecialchars($row['asks']) . "</td>";
            echo "<td>" ;
            
            echo "<form action='3_2_submit_application.php' method='post'>";
            echo "<input type='hidden' name='activity_id' value='" . $row['activity_id'] . "'>";
            echo "<input type='hidden' name='common_user_id' value='" . $_SESSION['user_id'] . "'>";
            // echo "<input type='applicaton' value='报名活动'>";
            echo "<button type='submit'>报名</button>";
            echo "</form></td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>
<script>
    function toggleDetails(activityId) {
        var details = document.getElementById('details' + activityId);
        if (details.style.display === "none") {
            details.style.display = "block";
        } else {
            details.style.display = "none";
        }
    }
</script>
</body>
</html>

<?php
mysqli_close($conn);
?>