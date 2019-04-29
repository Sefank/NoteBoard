<?php
if(isset($_POST["comment"]) && !empty($_POST["comment"])){
    $status_addcomment = addComment($_COOKIE["logined_username"],$_POST["nid"],$_POST["comment"]);
    if($status_addcomment == 0){
    ?>
<script>
        window.location.replace(window.location.href);
</script>
    <?php
    }
}
?>
<div class="row">
    <!-- INSERT INTO `notes` (`OwnUID`, `Topic`, `Content`) VALUES ('17', '测试标题1', '测试内容1') -->
    <div id="card-notes-container" class="col-md-12">
        <?php
        $notes = readAllNotes();
        if(!empty($notes)){
        foreach($notes as $curNote){
            ?>
        <div class="card my-2">
            <div class="card-header align-bottom">
                <div class="float-left">
                    <a id="card-note-title-<?php echo $curNote["NID"]; ?>" class="card-link float-left" data-toggle="collapse" data-parent="#card-notes-container" href="#card-note-<?php echo $curNote["NID"]; ?>">
                        <?php
                        $title = "留言 #" . $curNote["NID"];
                        if ($curNote["Topic"] != ""){
                            $title = $title . "：" . base64_decode($curNote["Topic"]);
                        }
                        echo $title;
                        ?>
                    </a>
                    <span class="card-text text-muted"> @<?php echo getUsername($curNote["OwnUID"]); ?></span>
                </div>
                <?php
                if (isset($_COOKIE["logined_username"])){
                ?>
                <div class="btn-group btn-group-sm float-right" role="group" aria-label="操作">
                    <button type="button" class="btn btn-warning" onclick="javascrtpt:window.location.href='index.php?view=note-edit&nid=<?php echo $curNote["NID"]; ?>'">修改</button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delModal" data-title="<?php echo $title; ?>" data-nid="<?php echo $curNote["NID"]; ?>">删除</button>
                </div>
                <?php
                }
                ?>
            </div>
            <div id="card-note-<?php echo $curNote["NID"]; ?>" class="collapse"><!--  class="collapse show" 以设置为默认展开 -->
                <div class="card-body">
                    <?php
                    $content = preg_split ("(\r|\n)", htmlspecialchars(base64_decode($curNote["Content"])));
                    foreach ($content as $content_p)
                    {
                    ?>
                    <p><?php echo $content_p; ?></p>
                    <?php
                    }
                    ?>
                    <div class="card">
                    <?php
                    if(($comments = readAllComments($curNote["NID"])) != false)
                    {
                        ?>
                        <ul class="list-group list-group-flush">
                        <?php
                        foreach ($comments as $curComment)
                        {
                        ?>
                                <li class="list-group-item"><span class="card-text text-muted">
                                        <?php echo getUsername($curComment["OwnUID"]).":"; ?></span><?php echo htmlspecialchars(base64_decode($curComment["Content"])); ?>
                                </li>
                        <?php
                        }
                        ?>
                        </ul>
                        <?php
                    }
                    ?>
                        <div class="card-footer">
                            <form class="form-inline input-group" action="index.php" method="post">
                                <input type="hidden" name="nid" value="<?php echo $curNote["NID"]; ?>">
                                <label class="sr-only" for="comment-<?php echo $curNote["NID"]; ?>">留言</label>
                                <input type="text" name="comment" class="form-control mb-2 mr-sm-2"  id="comment-<?php echo $curNote["NID"]; ?>" placeholder="回复此留言">
                                <button type="submit" class="btn btn-primary mb-2">回复</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        } else {
        ?>
        <div class="alert alert-warning text-center" role="alert">
            暂无留言，快来写一个吧！
        </div>
        <?php
        }
        if(isset($_GET["request"],$_GET["nid"]) && $_GET["request"] == "del"){ // 检测到删除请求
            deleteNote($_GET["nid"]);
        ?>
        <script> window.location.replace("index.php"); </script>
        <?php
        }
        ?>
        <!-- Modal -->
        <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="delModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="delModalTitle">删除留言确认</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        您确认要删除[未载入留言标题]吗？
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger" onclick="">删除</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('#delModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var title = button.data('title'); // Extract info from data-* attributes
                var nid = button.data('nid');
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this);
                modal.find('.modal-body').text("您确认要删除 " + title + " 吗？");
                modal.find('.btn-danger').attr("onclick","javascrtpt:window.location.href='index.php?request=del&nid=" + nid + "'");
            })
        </script>
        <!--  TODO：留言回复Loop 以下为样例
        <div class="card card-note">
            <div class="card-header">
                <a class="card-link" data-toggle="collapse" data-parent="#card-notes-container" href="#card-note-1">
                    留言 #1：测试标题
                </a>
            </div>
            <div id="card-note-1" class="collapse show">
                <div class="card-body">
                    <p>
                        测试内容！
                    </p>
                    <div id="card-comments-container">
                        <div class="card">
                            <div class="card-header">
                                <a class="card-link" data-toggle="collapse" data-parent="#card-comments-container" href="#card-comment-1">留言回复 #1</a>
                            </div>
                            <div id="card-comment-1" class="collapse show">
                                <div class="card-body">
                                    回复 #1 内容，回复 #1 内容，回复 #1 内容，回复 #1 内容，回复 #1 内容，回复 #1 内容，回复 #1 内容。
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <a class="card-link" data-toggle="collapse" data-parent="#card-comments-container" href="#card-comment-2">留言回复 #2</a>
                            </div>
                            <div id="card-comment-2" class="collapse show">
                                <div class="card-body">
                                    回复 #2 内容，回复 #2 内容，回复 #2 内容，回复 #2 内容，回复 #2 内容，回复 #2 内容，回复 #2 内容。
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        -->
    </div>
</div>