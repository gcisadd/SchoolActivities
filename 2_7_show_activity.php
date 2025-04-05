<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 删除报名信息的函数
function deleteApplication($applicationId, $conn): bool {
    // 获取活动ID和当前参与人数
    $query = "SELECT a.activity_id, act.number_of_participant 
               FROM application a 
               JOIN activity act ON a.activity_id = act.activity_id 
               WHERE a.application_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("mysqli_prepare() failed: " . $conn->error);
    }
    mysqli_stmt_bind_param($stmt, "i", $applicationId);
    mysqli_stmt_execute($stmt);
    $stmt->bind_result($activityId, $currentParticipants);
    $stmt->fetch();
    mysqli_stmt_close($stmt);

    // 更新申请结果为拒绝（result = 2）
    $updateResultQuery = "UPDATE application_result SET result = 2 WHERE application_id = ?";
    $updateStmt = mysqli_prepare($conn, $updateResultQuery);
    mysqli_stmt_bind_param($updateStmt, "i", $applicationId);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);

    // 如果当前审核结果为通过（result = 1），减少参与人数
    $checkResultQuery = "SELECT result FROM application_result WHERE application_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkResultQuery);
    mysqli_stmt_bind_param($checkStmt, "i", $applicationId);
    mysqli_stmt_execute($checkStmt);
    $checkStmt->bind_result($currentResult);
    $checkStmt->fetch();
    mysqli_stmt_close($checkStmt);

    if ($currentResult == 2) {
        $newParticipants = max(0, $currentParticipants - 1); // 防止参与人数为负
        $updateParticipantsQuery = "UPDATE activity SET number_of_participant = ? WHERE activity_id = ?";
        $updateStmt = mysqli_prepare($conn, $updateParticipantsQuery);
        mysqli_stmt_bind_param($updateStmt, "ii", $newParticipants, $activityId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    }

    // 删除申请记录
    $deleteApplicationQuery = "DELETE FROM application WHERE application_id = ?";
    $stmt2 = mysqli_prepare($conn, $deleteApplicationQuery);
    mysqli_stmt_bind_param($stmt2, "i", $applicationId);
    mysqli_stmt_execute($stmt2);
    // mysqli_stmt_close($stmt2);

    return mysqli_stmt_affected_rows($stmt2) > 0;
}

// 处理删除活动请求
if (isset($_GET['delete_activity'])) {
    $activityId = $_GET['delete_activity'];
    $deleteActivityQuery = "UPDATE activity SET status = 2 WHERE activity_id = ?";
    $stmt = mysqli_prepare($conn, $deleteActivityQuery);
    mysqli_stmt_bind_param($stmt, "i", $activityId);
    mysqli_stmt_execute($stmt);
    // mysqli_stmt_close($stmt);
    header("Location: 2_7_show_activity.php");
    exit;
}

// 处理删除用户报名请求
if (isset($_GET['delete_user'])) {
    $applicationId = $_GET['delete_user'];
    if (deleteApplication($applicationId, $conn)) {
        header("Location: 2_7_show_activity.php");
        exit;
    } else {
        echo "删除报名失败，请重试。";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活动管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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
        button {
            width: 49%; /* 使按钮宽度填满单元格 */
            height: 100%; /* 使按钮高度填满单元格（如果需要） */
            padding: 10px; /* 移除内边距 */
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-sizing: border-box; /* 确保宽度和高度包括内边距和边框 */
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
    </style>
</head>
<body>
<div class="user-container">
    <h1>活动管理</h1>
    <input type='submit' value='返回管理员界面' onclick='window.location.href="1_1_admin.php"'>
    <h2>查看活动信息</h2>
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
        $query = "SELECT * FROM activity WHERE status = 1";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['activity_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . htmlspecialchars($row['number_of_participant']) . "</td>";
            echo "<td>" . htmlspecialchars($row['asks']) . "</td>";
            echo "<td>
                    <button onclick='toggleDetails(" . $row['activity_id'] . ")'>查看报名用户</button>
                    <button onclick='deleteActivity(" . $row['activity_id'] . ")'>删除活动</button>
                  </td>";
            echo "</tr>";
        }
        mysqli_free_result($result);
        ?>
    </table>

    <!-- 动态生成的用户信息展示区 -->
    <?php
    $query = "SELECT * FROM activity WHERE status = 1";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div id='details-" . $row['activity_id'] . "' class='activity-details'>";
        echo "<h2>活动ID: " . htmlspecialchars($row['activity_id']) . " - 活动标题: " . htmlspecialchars($row['title']) . " 的报名用户:</h2>";
        echo "<ul>";

        // 查询该活动的报名用户
        $userQuery = "SELECT cu.name, cu.common_user_id, ar.application_id 
                      FROM application a 
                      JOIN common_user cu ON a.common_user_id = cu.common_user_id
                      JOIN application_result ar ON a.application_id = ar.application_id
                      WHERE a.activity_id = " . $row['activity_id'];
        $userResult = mysqli_query($conn, $userQuery);
        while ($userRow = mysqli_fetch_assoc($userResult)) {
            // echo "<li>";
            echo "<h3>用户ID: " . htmlspecialchars($userRow['common_user_id']) . " - 用户名: " . htmlspecialchars($userRow['name'])."</h3>";
            echo "<button onclick='deleteApplication(" . $userRow['application_id'] . ")'>删除报名</button>";
            // echo "</li>";
        }
        mysqli_free_result($userResult);
        echo "</ul>";
        echo "</div>";
    }
    mysqli_free_result($result);
    ?>
</div>
<script>
    function toggleDetails(activityId) {
        var details = document.getElementById('details-' + activityId);
        if (details) {
            if (details.style.display === "none") {
                details.style.display = "block";
            } else {
                details.style.display = "none";
            }
        }
    }

    function deleteApplication(applicationId) {
        if (confirm('确定要删除这个用户的报名吗？')) {
            window.location.href = '2_7_show_activity.php?delete_user=' + applicationId;
        }
    }

    function deleteActivity(activityId) {
        if (confirm('确定要删除这个活动吗？')) {
            window.location.href = '2_7_show_activity.php?delete_activity=' + activityId;
        }
    }
</script>
</body>
</html>