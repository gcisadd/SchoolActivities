<?php
include '0_0_db.php'; // 包含数据库连接
session_start(); // 初始化会话

// 检查用户是否已登录
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    die("用户未登录或用户类型不是管理员，请先登录。");
}

// 检查是否提交了表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $applicationId = $_POST['application_id'];
    $result = $_POST['result'];

    // 防止 SQL 注入
    $applicationId = mysqli_real_escape_string($conn, $applicationId);
    $result = mysqli_real_escape_string($conn, $result);

    // 更新申请结果
    $updateResultQuery = "UPDATE application_result SET result = ? WHERE application_id = ?";
    $stmt = mysqli_prepare($conn, $updateResultQuery);
    mysqli_stmt_bind_param($stmt, "ii", $result, $applicationId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt); // 关闭预处理语句

    // 获取活动ID和当前参与人数
    $query = "SELECT a.activity_id, act.number_of_participant 
              FROM application a 
              JOIN activity act ON a.activity_id = act.activity_id 
              WHERE a.application_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $applicationId);
    mysqli_stmt_execute($stmt);
    $stmt->bind_result($activityId, $currentParticipants);
    $stmt->fetch();
    mysqli_stmt_close($stmt); // 关闭预处理语句

    if ($result == 1) { // 如果通过申请，增加参与人数
        $newParticipants = $currentParticipants + 1;
        $updateParticipantsQuery = "UPDATE activity SET number_of_participant = ? WHERE activity_id = ?";
        $updateStmt = mysqli_prepare($conn, $updateParticipantsQuery);
        mysqli_stmt_bind_param($updateStmt, "ii", $newParticipants, $activityId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt); // 关闭预处理语句
    }

    if ($result == 2) { // 如果拒绝申请，减少参与人数（如果之前是通过状态）
        $checkPreviousResultQuery = "SELECT result FROM application_result WHERE application_id = ?";
        $checkStmt = mysqli_prepare($conn, $checkPreviousResultQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $applicationId);
        mysqli_stmt_execute($checkStmt);
        $checkStmt->bind_result($previousResult);
        $checkStmt->fetch();
        mysqli_stmt_close($checkStmt); // 关闭预处理语句

        if ($previousResult == 1) {
            $newParticipants = $currentParticipants - 1;
            $updateParticipantsQuery = "UPDATE activity SET number_of_participant = ? WHERE activity_id = ?";
            $updateStmt = mysqli_prepare($conn, $updateParticipantsQuery);
            mysqli_stmt_bind_param($updateStmt, "ii", $newParticipants, $activityId);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt); // 关闭预处理语句
        }
    }

    header("Location: 4_1_success.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}

// 关闭数据库连接
mysqli_close($conn);
?>