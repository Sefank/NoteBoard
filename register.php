<?php
$statusCode_Register = 1; // 未提交注册请求
if(isset($_COOKIE["logined_username"])){
    $statusCode_Register = -3; // 已登入，禁止注册
} else if(isset($_POST["register_username"],$_POST["register_password"])){
    $statusCode_Register = addUser($_POST["register_username"],$_POST["register_password"]);
    // -2：数据库错误 -1：已存在同名账号 0：添加成功
}
if($statusCode_Register < 1){
    if($statusCode_Register < 0){
        $typeModal = "danger";
    } else {
        $typeModal = "success";
    }
    ?>
    <div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-<?php echo $typeModal; ?>" id="ModalTitle">注册<?php if($statusCode_Register == 0) {echo "成功";} else {echo "失败";} ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    switch ($statusCode_Register) {
                        case 0:
                            echo("您的账户 ". $_POST["register_username"] ." 已注册成功！<br>登入后，您就可以在留言板留言了！");
                            break;
                        case -1:
                            echo("您输入的用户名不可用，请重新输入！");
                            break;
                        case -2:
                            echo("数据库记录有误，请联系数据库管理员！");
                            break;
                        case -3:
                            echo("出于安全原因，登入状态下不能注册！");
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" <?php if($statusCode_Register == 0 || $statusCode_Register == -3) {echo 'onclick="jump()"';} ?>>确定</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    $('#ModalCenter').modal('show');
<?php
}
if($statusCode_Register == 0 || $statusCode_Register == -3) { ?>
    function jump(){ window.location.replace("index.php"); }
<?php }?>
    </script>
<?php if($statusCode_Register != -3) { ?>
<div class="row w-50 mx-auto">
    <div class="col-md-12">
        <form action="index.php?view=register" method="post" onsubmit="return submitForm();">
            <div class="form-group row">
                <label for="inputUsername" class="col-sm-3 col-form-label">用户名</label>
                <div class="input-group col-sm-9">
                    <div class="input-group-prepend">
                        <div class="input-group-text">@</div>
                    </div>
                    <input type="text" class="form-control" id="inputUsername" name="register_username" placeholder="用户名" oninput="checkForm(1)" >
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-3 col-form-label">密码</label>
                <div class="input-group col-sm-9">
                    <input type="password" class="form-control" id="inputPassword" placeholder="密码" oninput="checkForm(2)" >
                    <div class="invalid-tooltip">两次输入的密码不匹配！</div>
                    <input type="hidden" id="password_hash" name="register_password">
                    <input type="hidden" id="salt_hash" value="<?php echo $salt; ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPasswordR" class="col-sm-3 col-form-label">重复密码</label>
                <div class="input-group col-sm-9">
                    <input type="password" class="form-control" id="inputPasswordR" placeholder="密码" oninput="checkForm(3)">
                    <div class="invalid-tooltip">两次输入的密码不匹配！</div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary d-block mx-auto">注册</button>
        </form>
        <!-- TODO：用户注册开发
        <form action="index.php" method="post" onsubmit="return hashPassword()">
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
                <input type="hidden" id="salt_hash" value="123">
            </div>
            <div class="d-block mx-auto">
                <a href="index.php?view=register"><button type="button" class="btn btn-outline-secondary mb-2 mr-sm-2">注册</button></a>
            </div>
        </form>
        -->
    </div>
</div>
<?php } ?>
<script>
    let un_written = false, pw_original_written = false, pw_repeated_written = false;
    function checkForm(name) {
        const un = $("#inputUsername");
        const pw_original = $("#inputPassword");
        const pw_repeated = $("#inputPasswordR");
        let unCheck = false, pwCheck = false;
        switch (name) {
            case 1:
                un_written = true;
                break;
            case 2:
                pw_original_written = true;
                break;
            case 3:
                pw_repeated_written = true;
                break;
        }
        if (un_written){
            if (un.val() === "") {
                un.removeClass("is-valid");
                un.addClass("is-invalid");
            } else {
                un.removeClass("is-invalid");
                un.addClass("is-valid");
                unCheck = true;
            }
        }
        if(pw_original_written && pw_repeated_written){
            if(pw_original.val() !== pw_repeated.val()) {
                pw_original.removeClass("is-valid");
                pw_original.addClass("is-invalid");
                pw_repeated.removeClass("is-valid");
                pw_repeated.addClass("is-invalid");
            } else {
                pw_original.removeClass("is-invalid");
                pw_original.addClass("is-valid");
                pw_repeated.removeClass("is-invalid");
                pw_repeated.addClass("is-valid");
                pwCheck = true;
            }
        }
        return (unCheck && pwCheck);
    }
    function submitForm() {
        let pw_original = $("#inputPassword");
        let pw_repeated = $("#inputPasswordR");
        const pw_salt = $("#salt_hash");
        let pw_hash = $("#password_hash");
        if(checkForm(0) === false) {
            return false;
        } else {
            pw_hash.val(sha256_digest(pw_original.val() + pw_salt.val()));
            pw_original.val("");
            pw_repeated.val("");
            return true;
        }
    }
</script>
