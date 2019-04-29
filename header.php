<script>
    function hashPassword() {
        const password_input = document.getElementById('inlineFormInputGroupPassword');
        const password_hash = document.getElementById('password_hash');
        const salt_hash = document.getElementById('salt_hash');
        // 完成hash并清空原密码
        password_hash.value = sha256_digest(password_input.value + salt_hash.value);
        password_input.value = "";
        return true;
    }
</script>
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1 class="text-center my-4">
                留言板 <small>(Bootstrap 版)</small>
            </h1>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills align-middle float-left">
            <?php
            switch ($_GET["view"]){
                case "about":
                    $classNoteButtom = "btn-outline-secondary";
                    $classAboutButtom = "btn-warning";
                    $classBadge = "badge-warning";
                    break;
                case "":
                    $classNoteButtom = "btn-primary";
                    $classAboutButtom = "btn-outline-secondary";
                    $classBadge = "badge-light";
                    break;
                default:
                    $classNoteButtom = "btn-outline-secondary";
                    $classAboutButtom = "btn-outline-secondary";
                    $classBadge = "badge-primary";
                    switch ($_GET["view"]){
                        case "register":
                            $displayNewButtom = "用户注册";
                            break;
                        case "note-edit":
                            $displayNewButtom = "留言编写";
                            break;
                    }
            };
            ?>
            <li class="nav-item">
                <a class="nav-link btn <?php echo $classNoteButtom; ?> mr-sm-2" href="index.php">留言内容 <span class="badge <?php echo $classBadge; ?>"><?php $num = countNotes(); if($num < 100){ echo $num; } else { echo "99+"; } ?></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn <?php echo $classAboutButtom; ?> mr-sm-2" href="index.php?view=about">关于本站</a>
            </li>
            <?php
            if ($_GET["view"] != "" and $_GET["view"] != "about"){
            ?>
            <li class="nav-item">
                <a class="nav-link btn btn-primary mr-sm-2" href="index.php?view=<?php echo $_GET["view"]; ?>"><?php echo $displayNewButtom; ?></a>
            </li>
            <?php
            };
            ?>

        </ul>
        <?php
        $statusCode_Login = 0; // 未登入 且 无申请
        if(isset($_POST["login_username"],$_POST["login_password"])){ // 检测并处理登入请求，
            $sql = "SELECT * FROM `users` WHERE `Username` = '".$_POST["login_username"]."'";
            $result = mysqli_query($conn,$sql);
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                if($_POST["login_password"] == $row["Password_hash"]){
                    $statusCode_Login = 1; // 更改为已登入
                    $expire = time() + 60 * 60 * 2; // 单位：秒 这里是2小时
                    setcookie("logined_username", $_POST["login_username"], $expire);
                } else {
                    $statusCode_Login = -1; // 密码错误
                };
            } else if(mysqli_num_rows($result) < 1) {
                $statusCode_Login = -2; // 未找到该用户
            } else {
                $statusCode_Login = -3; // 数据库记录重复错误
            };
            // echo "<script>alert('".$statusCode_Login."')</script>";
            unset($_POST);
        };
        if(isset($_COOKIE["logined_username"])) {
            $statusCode_Login = 2; // 已登入
            ?>
            <form id="user-login" class="form-inline align-middle float-right">
                <label class="sr-only" for="inlineFormInputGroupUsername">用户名</label>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text text-muted">@</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroupUsername" name="login_username" value="<?php echo($_COOKIE["logined_username"]); ?>" readonly>
                    <div class="input-group-append">
                        <a href="index.php?view=note-edit"><button class="btn btn-outline-primary" type="button">写留言</button></a>
                    </div>
                </div>
                <button class="btn btn-outline-danger mb-2 mr-sm-2" type="button" onclick="logOut()">登出</button>
            </form>
            <script>
                function logOut() {
                    document.cookie = "logined_username=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                    window.location.replace(window.location.href);
                }
            </script>
        <?php
        } else if(!isset($_GET["view"]) or $_GET["view"] != "register") { // 未登入 且 未提交登入请求 显示登录栏
        ?>
            <form id="user-login" class="form-inline align-middle float-right" action="index.php" method="post" onsubmit="return hashPassword()">
                <label class="sr-only" for="inlineFormInputGroupUsername">用户名</label>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">@</div>
                    </div>
                    <input type="text" class="form-control" id="inlineFormInputGroupUsername" name="login_username" placeholder="用户名">
                </div>
                <label class="sr-only" for="inlineFormInputGroupPassword">密码</label>
                <div class="input-group mb-2 mr-sm-2">
                    <input type="password" class="form-control" id="inlineFormInputGroupPassword" placeholder="密码">
                    <input type="hidden" id="password_hash" name="login_password">
                    <input type="hidden" id="salt_hash" value="<?php echo $salt; ?>">
                </div>
                <button type="submit" class="btn btn-outline-success mb-2 mr-sm-2">登入</button>
                <a href="index.php?view=register"><button type="button" class="btn btn-outline-secondary mb-2 mr-sm-2">注册</button></a>
            </form>
            <?php
        };
        //if($statusCode_Login == 1) {echo "<script>alert('".$_POST["login_password"]." ".$row["Password_hash"]."')</script>";};
        ?>
    </div>
</div>
<hr>
<?php if($statusCode_Login < 0){ ?>
    <div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="ModalTitle">登入错误</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    switch ($statusCode_Login) {
                        case -1:
                            echo("您输入的密码有误，请重新输入！");
                            break;
                        case -2:
                            echo("您输入的用户名有误，请重新输入！");
                            break;
                        case -3:
                            echo("数据库记录有误，请联系数据库管理员！");
                            break;
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>
    <script> $('#ModalCenter').modal('show'); </script>
<?php } if ($statusCode_Login == 1){ ?>
<script> window.location.replace(window.location.href); </script>
<?php } ?>