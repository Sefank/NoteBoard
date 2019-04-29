<?php
// 数据库地址 本地数据库请保持默认值 localhost
$servername   = "localhost";
// 数据库用户名
$username     = "username";
// 数据库密码
$password     = "password";
// 数据库名称
$databasename = "databasename";
// Hash Salt 用于密码存储
$salt         = "o6pp91mkdrsjta3q";

// 创建连接
$conn = mysqli_connect($servername, $username, $password, $databasename);

// 检测连接
if (!$conn) {
    die("数据库连接失败：" . mysqli_connect_error());
}

// 以下为数据库函数封装

function getSQLErr() {
    global $conn;
    return mysqli_error($conn);
}
// 用户部分

function checkPassword($username, $password_hash){
    global $conn;
    $sql = "SELECT * FROM `users` WHERE `Username` = '".$username."'";
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        if($password_hash == $row["Password_hash"]){
            return 0; // 验证成功
        } else {
            return -1; // 密码错误
        }
    } else if(mysqli_num_rows($result) < 1) {
        return -2; // 未找到该用户
    } else {
        return -3; // 数据库记录重复
    }
}

function addUser($username,$password_hash){
    global $conn;
    $sql = "SELECT * FROM `users` WHERE `Username` = '".$username."'";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return -2; // 数据库错误
    } else if(mysqli_num_rows($result) > 0) {
        return -1; // 已存在同名账号
    }
    $sql = "INSERT INTO `users` (`Username`, `Password_hash`) VALUES ('".$username."', '".$password_hash."')";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return -2; // 数据库错误
    } else {
        return 0; // 添加成功
    }
}

function getUID($username){
    global $conn;
    $sql = "SELECT * FROM `users` WHERE `Username` = '".$username."'";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return -2; // 数据库错误
    } else if(mysqli_num_rows($result) != 1) {
        return -1; // 匹配用户不唯一/无此用户
    } else {
        $row = mysqli_fetch_assoc($result);
        return $row["UID"];
    }
}

function getUsername($UID){
    global $conn;
    $sql = "SELECT * FROM `users` WHERE `UID` = '".$UID."'";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return -2; // 数据库错误
    } else if(mysqli_num_rows($result) != 1) {
        return -1; // 匹配用户不唯一/无此用户
    } else {
        $row = mysqli_fetch_assoc($result);
        return $row["Username"];
    }
}

// 留言部分

/**
 * @param $username
 * @param $noteTopic
 * @param $noteContent
 * @return int 0: 成功 -1:数据库错误 -2:匹配用户不唯一/无此用户
 */
function addNote($username, $noteTopic, $noteContent){
    // INSERT INTO `notes` (`OwnUID`, `Topic`, `Content`) VALUES ('17', '测试标题1', '测试内容1')
    // TODO:留言重复检查
    global $conn;
    $UID = getUID($username);
    switch ($UID) {
        case -1:
            return -1; // 数据库错误
            break;
        case -2:
            return -2; // 匹配用户不唯一/无此用户
        break;
        default:
            $sql = "INSERT INTO `notes` (`OwnUID`, `Topic`, `Content`) VALUES ('".$UID."', '".base64_encode($noteTopic)."', '".base64_encode($noteContent)."')";
            if(mysqli_query($conn,$sql) == true){
                return 0;
            } else {
                return -1;
            }
    }
}

function readNote($nid){
    global $conn;
    $sql = "SELECT * FROM `notes` WHERE `NID` = '".$nid."'";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return false; // 数据库错误
    }
    return mysqli_fetch_assoc($result);
}

function readAllNotes(){
    global $conn;
    $sql = "SELECT * FROM `notes`";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return false; // 数据库错误
    }
    $result_note = array();
    while($row = mysqli_fetch_assoc($result)){
        array_push($result_note,$row);
    };
    return $result_note;
}

function countNotes(){
    global $conn;
    $sql = "SELECT COUNT(*) AS 'notesNum' FROM `notes`";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return false; // 数据库错误
    }
    $row = mysqli_fetch_assoc($result);
    return $row["notesNum"];
}

function editNote($nid,$username,$noteTopic,$noteContent){
    global $conn;
    $UID = getUID($username);
    if($UID == -1){ // 用户数据库错误
        return -4;
    } else if ($UID == -2){ // 匹配用户不唯一/无此用户
        return -3;
    }
    $sql = "SELECT * FROM `notes` WHERE `NID` = '".$nid."'";
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return -2; // 留言数据库错误
    } else if(mysqli_num_rows($result) != 1) {
        return -1; // 匹配留言不唯一/无此留言
    }
    $sql = "UPDATE `notes` SET `OwnUID`='$UID', `Topic`='".base64_encode($noteTopic)."', `Content`='".base64_encode($noteContent)."'  WHERE `NID`=$nid";
    if(mysqli_query($conn,$sql) == false){
        return -5; // 更新失败
    };
    return 0;
}

function deleteNote($nid){
    global $conn;
    $sql = "DELETE FROM `notes` WHERE `NID`='$nid'";
    return mysqli_query($conn,$sql);
}

// 评论部分


function readAllComments($nid){
    global $conn;
    $sql = "SELECT * FROM `comments` WHERE `RelatedNID` = ".$nid;
    $result = mysqli_query($conn,$sql);
    if($result == false){
        return false; // 数据库错误
    }
    $result_comment = array();
    while($row = mysqli_fetch_assoc($result)){
        array_push($result_comment,$row);
    }
    return $result_comment;
}


function addComment($username,$nid,$commentContent){
    global $conn;
    $UID = getUID($username);
    switch ($UID) {
        case -1:
            return -1; // 数据库错误
            break;
        case -2:
            return -2; // 匹配用户不唯一/无此用户
            break;
    }
    if(readNote($nid) == false){
        return -3; // 没有匹配的留言
    }
    $sql = "INSERT INTO `comments` (`OwnUID`, `RelatedNID`, `Content`) VALUES ('".$UID."', '".$nid."', '".base64_encode($commentContent)."')";
    if(mysqli_query($conn,$sql) == true){
        return 0;
    } else {
        return -1;
    }
}


/*
function editComment($nid,$username,$noteTopic,$noteContent){
    // UPDATE `comments` SET `Topic`='测试回复标题1', `Content`='测试回复内容AA' WHERE (`CID`='1')
}
*/