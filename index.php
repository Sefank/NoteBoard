<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>留言板|易班考核任务</title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="/sha256.js"></script>
<?php include_once("connector.php"); ?>
<div class="container-fluid w-75">
    <?php
    if(!isset($_GET["view"])){ $_GET["view"] = ""; }; // 首页默认为查看留言板
    include_once("header.php"); // 标题、导航、登入/操作栏
    switch ($_GET["view"]) {
        case "about":
            include_once("about.php"); // 查看 关于 页
            break;
        case "register":
            include_once("register.php"); // 查看 注册 页
            break;
        case "note-edit":
            include_once("note-edit.php"); // 查看 编辑 页
            break;
        default:
            include_once("note-view.php"); // 查看 留言板
    };
    ?>
</div>

</body>
</html>