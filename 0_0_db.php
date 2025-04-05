<?php 
$hostname = "localhost"; //主机名,可以用IP代替
$database = "database_work"; //数据库名
$username = "root"; //数据库用户名
$password = ""; //数据库密码
// 连接数据库
$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
//echo "连接成功\n";
?>