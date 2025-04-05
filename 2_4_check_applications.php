<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 检查用户是否已登录
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    die("用户未登录或用户类型不是管理员，请先登录。");
}

// 查询所有未审核的申请及活动详情
$query = "SELECT a.application_id, a.common_user_id, a.activity_id, r.result, act.title, act.time, act.location, cu.name
            FROM application a
            LEFT JOIN common_user cu ON a.common_user_id = cu.common_user_id
            LEFT JOIN application_result r ON a.application_id = r.application_id
            LEFT JOIN activity act ON a.activity_id = act.activity_id
            WHERE r.result IS NULL OR r.result = 0";
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
    <title>审核申请</title>
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
            border: 2px solid #007bff; /* 蓝色边框 */
            border-radius: 10px; /* 圆角边框 */
        }
        input[type="submit"], input[type="button"] {
            padding: 10px 20px;
            background-color: #007bff; /* 按钮背景蓝色 */
            color: white;
            border: none;
            border-radius: 10px; /* 按钮圆角 */
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover, input[type="button"]:hover {
            background-color: #566; /* 悬停时的深蓝色 */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            width: 50%; /* 使按钮宽度填满单元格 */
            height: 100%; /* 使按钮高度填满单元格（如果需要） */
            padding: 8px; /* 移除内边距 */
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-sizing: border-box; /* 确保宽度和高度包括内边距和边框 */
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>审核申请</h1>
    <input type='submit' value='返回管理员界面' onclick='window.location.href="1_1_admin.php"'>
    <h2>审核用户申请</h2>
    <table>
        <tr>
            <th>申请ID</th>
            <th>用户名称</th>
            <th>用户ID</th>
            <th>活动ID</th>
            <th>活动标题</th>
            <th>活动时间</th>
            <th>活动地点</th>
            <!-- <th>审核结果</th> -->
            <th>操作</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc(result: $result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['application_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['common_user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['activity_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            // echo "<td>" . ($row['result'] === null ? '未审核' : $row['result']) . "</td>";
            echo "<td>";
            echo "<form method='post' action='2_5_update_result.php'>";
            echo "<input type='hidden' name='application_id' value='" . $row['application_id'] . "'>";
            echo "<button type='submit' name='result' value='1'>通过</button>";
            echo "<button type='submit' name='result' value='2'>拒绝</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// 关闭数据库连接
mysqli_close($conn);
?>