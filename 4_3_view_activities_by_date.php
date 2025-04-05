<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 获取用户输入的日期
$inputDate = isset($_GET['date']) ? $_GET['date'] : '';

// 构建查询，使用时间索引来优化查询
$query = "SELECT * FROM activity WHERE time = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $inputDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("查询失败: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>按日期查看活动</title>
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
        input[type="date"], input[type="submit"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>按日期查看活动</h1>
    <form action="4_3_view_activities_by_date.php" method="get">
        <label for="date">选择日期:</label>
        <input type="date" id="date" name="date" required>
        <input type="submit" value="查看活动">
    </form>
    <table>
        <tr>
            <th>活动ID</th>
            <th>标题</th>
            <th>时间</th>
            <th>地点</th>
            <th>参与人数</th>
            <th>活动要求</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['activity_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . htmlspecialchars($row['number_of_participant']) . "</td>";
            echo "<td>" . htmlspecialchars($row['asks']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>