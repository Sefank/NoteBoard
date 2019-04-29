<div class="row">
    <div class="col-md-12">
        <?php
            if (isset($_POST["noteContent"])) { // 识别申请，开始处理
                if ($_POST["nid"] == -1) { // 新增留言
                    $statusCode_noteEdit = addNote($_COOKIE["logined_username"], $_POST["noteTopic"], $_POST["noteContent"]);
                    if ($statusCode_noteEdit == 0) {
                        header("Location: index.php");
                    }
                } else {
                    $statusCode_noteEdit = editNote($_POST["nid"], $_COOKIE["logined_username"], $_POST["noteTopic"], $_POST["noteContent"]);
                    if ($statusCode_noteEdit == 0) {
                        header("Location: index.php");
                    }
                }
                if ($statusCode_noteEdit < 0) {
                    ?>
                    <div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalTitle"
                         aria-hidden="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-danger" id="ModalTitle">添加留言失败</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>出错！</p>
                                    <small>错误代码：<?php echo ($_POST["nid"] == -1) ? ("addNote") : ("editNote"); ?>
                                        (<?php echo $statusCode_noteEdit; ?>)
                                        <small>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">确定</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script> $('#ModalCenter').modal('show'); </script>
                    <?php
                }
                unset($_POST);
            }
        ?>
        <form action="index.php?view=note-edit" method="post">
            <?php
            if(isset($_GET["nid"])){
                $curNote=readNote($_GET["nid"]);
                if($curNote == false){
                    die("数据库错误！");
                }
            ?>
            <div class="row">
                <div class="col form-group">
                    <label for="inputTopic">留言主题</label>
                    <input name="noteTopic" type="text" class="form-control" id="inputTopic" aria-describedby="helpTopic" value="<?php echo base64_decode($curNote["Topic"]); ?>" placeholder="（无主题）">
                    <small id="helpTopic" class="form-text text-muted">留言主题是选填的，您也可以提交没有主题的留言。</small>
                </div>
                <div class="col-md-1 form-group">
                    <label for="inputNID">留言序号</label>
                    <input name="nid" type="text" class="form-control" id="inputNID" value="<?php echo $_GET["nid"]; ?>" readonly>
                </div>
            </div>
            <?php
            } else {
            ?>
            <div class="form-group">
                <label for="inputTopic">留言主题</label>
                <input name="noteTopic" type="text" class="form-control" id="inputTopic" aria-describedby="helpTopic" placeholder="（无主题）">
                <small id="helpTopic" class="form-text text-muted">留言主题是选填的，您也可以提交没有主题的留言。</small>
            </div>
            <input name="nid" type="hidden" value="-1">
            <?php
            }
            ?>
            <div class="form-group">
                <label for="inputContent">留言内容</label>
                <textarea name="noteContent" class="form-control" id="inputContent"><?php
                    if(isset($_GET["nid"])){
                        echo base64_decode($curNote["Content"]);
                    }
                ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary d-block mx-auto">提交</button>
        </form>
    </div>
</div>