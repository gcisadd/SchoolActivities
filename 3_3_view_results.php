<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    die("用户未登录，请先登录。");
}

// 查询用户的申请及审核结果
$query = "SELECT * FROM application_result ar 
            JOIN application a ON ar.application_id = a.application_id 
            JOIN activity act ON a.activity_id = act.activity_id 
            WHERE a.common_user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $applicationId = $_POST['application_id'];

    // 获取活动ID和当前参与人数
    $query = "SELECT a.activity_id, act.number_of_participant, ar.result 
              FROM application a 
              JOIN activity act ON a.activity_id = act.activity_id 
              JOIN application_result ar ON a.application_id = ar.application_id 
              WHERE a.application_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $applicationId);
    mysqli_stmt_execute($stmt);
    $stmt->bind_result($activityId, $currentParticipants, $currentResult);
    $stmt->fetch();
    $stmt->close();

    // 如果当前审核结果为通过，减少参与人数
    if ($currentResult == 1) {
        $newParticipants = $currentParticipants - 1;
        $updateParticipantsQuery = "UPDATE activity SET number_of_participant = ? WHERE activity_id = ?";
        $updateStmt = mysqli_prepare($conn, $updateParticipantsQuery);
        mysqli_stmt_bind_param($updateStmt, "ii", $newParticipants, $activityId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    }

    // 删除申请记录
    $deleteResultQuery = "DELETE FROM application_result WHERE application_id = ?";
    $stmt1 = mysqli_prepare($conn, $deleteResultQuery);
    mysqli_stmt_bind_param($stmt1, "i", $applicationId);
    mysqli_stmt_execute($stmt1);

    $deleteApplicationQuery = "DELETE FROM application WHERE application_id = ?";
    $stmt2 = mysqli_prepare($conn, $deleteApplicationQuery);
    mysqli_stmt_bind_param($stmt2, "i", $applicationId);
    mysqli_stmt_execute($stmt2);

    if (mysqli_stmt_affected_rows($stmt1) > 0 && mysqli_stmt_affected_rows($stmt2) > 0) {
        header("Location: 4_2_success.php");
        exit;
    } else {
        echo "删除申请失败，请重试。";
    }
    mysqli_stmt_close($stmt1);
    mysqli_stmt_close($stmt2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看结果</title>
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
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        border-radius: 10px; /* 单元格圆角 */
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }
    button {
        padding: 10px 20px;
        background-color: #007bff; /* 按钮背景蓝色 */
        color: white;
        border: none;
        border-radius: 10px; /* 按钮圆角 */
        cursor: pointer;
        font-size: 16px;
        margin-right: 10px;
        width: 100%; /* 使按钮占满整个列表项 */
    }
    button:hover {
        background-color: #556; /* 悬停时的深蓝色 */
    }
    .activity-details {
        margin-top: 10px;
        padding: 10px;
        background-color: #fff;
        border: 2px solid #0bf; /* 活动详情边框 */
        border-radius: 10px; /* 活动详情圆角 */
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
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        if (button.textContent === '删除申请') { // 只为删除申请按钮添加确认
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const confirmation = confirm('您确定要删除这个申请吗？');
                if (confirmation) {
                    this.form.submit();
                }
            });
        }
    });
});
</script>
</head>
<body>
    <h1>查看结果</h1>
    <input type='submit' value='返回普通用户界面' onclick='window.location.href="1_2_user.php"'>
    <h2>查看审核结果</h2>
    <table>
        <tr>
            <th>活动ID</th>
            <th>活动标题</th>
            <th>活动时间</th>
            <th>活动地点</th>
            <th>活动要求</th>
            <th>审核结果</th>
            <th>操作</th>
        </tr>
        <?php


        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            
            echo "<td>" . htmlspecialchars($row['activity_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . htmlspecialchars($row['asks']) . "</td>";
            echo "<td>";
                if ($row['result'] == 0) {
                    echo "待审核";
                } elseif ($row['result'] == 1) {
                    echo "通过";
                } elseif ($row['result'] == 2) {
                    echo "拒绝";
                }

            echo "<td>";
            switch ($row['result']) {
                case 0: // 待审核
                    echo "<form method='post' action='3_3_view_results.php'>";
                    echo "<input type='hidden' name='application_id' value='" . $row['application_id'] . "'>";
                    echo "<input type='hidden' name='action' value='delete'>";
                    echo "<button type='submit'>删除申请</button>";
                    echo "</form>";
                    break;
                case 1: // 通过
                    echo "<form method='post' action='3_3_view_results.php'>";
                    echo "<input type='hidden' name='application_id' value='" . $row['application_id'] . "'>";
                    echo "<input type='hidden' name='action' value='delete'>";
                    echo "<button type='submit'>取消报名</button>";
                    echo "</form>";
                    break;
                case 2: // 拒绝
                    echo "<form method='post' action='3_2_submit_application.php'>";
                    echo "<input type='hidden' name='activity_id' value='" . $row['activity_id'] . "'>";
                    echo "<input type='hidden' name='common_user_id' value='" . $_SESSION['user_id'] . "'>";
                    echo "<button type='submit'>重新申请</button>";
                    echo "</form>";
                    break;
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
        
    </table>
</body>
</html>

<?php
// 关闭数据库连接
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>