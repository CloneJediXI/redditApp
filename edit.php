<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'vendor/autoload.php';
    use App\SQLiteConnection;
?>
<html>
    <head>
        <title>Edit Post</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    </head>
    <body style="max-width: 100%;overflow-x: hidden;" class="">
        <script type="text/javascript">
            var tags = [];
            var tagNumbers = [];
            $(document).ready(function(){
                var $form = $('#editForm');
                
                $form.submit(function(){
                    $.post($(this).attr('action'), $(this).serialize(), function(response){
                            // do something here on success
                    },'json');
                    function show_popup(){
                        window.location = "/redditApp/view.php";
                    };
                    window.setTimeout( show_popup, 500 );
                    
                    return false;
                    
                });
                $('button').click(function() {
                    var id = $(this).attr('id');
                    if(id == "cancel"){
                        window.location = "/redditApp/view.php";
                    }
                    
                });
                $('#tagDrop').change(function(){
                    var tag = $('#tagDrop option:selected').text();
                    var tagNumber = $('#tagDrop').val();
                    if (tagNumber == "-1"){
                        tags = [];
                        tagNumbers = [];
                    }else if(tagNumbers.includes(tagNumber)){
                        var index = tagNumbers.indexOf(tagNumber);
                        if (index >-1){
                            tagNumbers.splice(index, 1);
                        }
                        index = tags.indexOf(tag);
                        if (index >-1){
                            tags.splice(index, 1);
                        }
                    }else {
                        tags.push(tag);
                        tagNumbers.push(tagNumber);
                    }
                    //$('#tagsList').text(tags);
                    $('#tagsIdList').val(tagNumbers);
                    var tagNames = "";
                    tags.forEach(element => tagNames+= element+", ");
                    $('#tagsList').text(tagNames);
                });
            });
        </script>

    <?php
        if(isset($_POST['postId'])) {
            $postId = $_POST['postId'];

            $connection = new SQLiteConnection();
            $pdo = $connection->connect();
            if ($pdo != null){
                $posts = $connection->getOnePost($postId);
                $post = $posts[0];
                ?> 
                <div class="row bg-secondary">
                    
                    <div class="col-lg-4 col-1"></div>
                    <div class="col-lg-4 col-10 bg-light rounded border-bottom border-dark m-1">
                        <form action="edit.php" method="POST" id="editForm"> 
                            <div class="row">
                                <div class="col-6"></div>
                                <div class="col-1">
                                    <button id="save" type="submit" class="btn btn-success">Save</button>
                                </div>
                                <div class="col-3"></div>
                                <div class="col-1">
                                    <button id="cancel" type="button" class="btn btn-warning">Cancel</button>
                                </div>
                            </div>
                            <h3><a href="<?php echo $post['post_link'] ?>" target="_blank"><?php echo $post['post_title'] ?></a></h3>
                           
                            <div class="p-3 text-danger">
                                <label for="tags">Tags to apply to post : </label>
                                <span role="textbox" id="tagsList" name="tags" style="border: 1px solid #ccc;padding: 1px 6px; margin-left: 5px;"></span>
                                <input name="postId2" type="hidden" value="<?php echo $postId ?>"/>
                                <input id="tagsIdList" name="tags" type="hidden"/>
                                <br/>
                                <select name="tagList" id="tagDrop" class="text-danger">
                                    <?php 
                                        $connection = new SQLiteConnection();
                                        $pdo = $connection->connect();
                                        if ($pdo != null){
                                            $tags = $connection->getAllTags();
                                            echo "<option value=\"-1\">Remove all Tags</option>";
                                            foreach($tags as $tag){
                                                $id = $tag['tag_id'];
                                                $name = $tag['tag_name'];
                                                echo "<option value=\"$id\">$name</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        
                            <br />
                            <p><?php echo $post['post_subreddit'] ?></p>
                            <img src="<?php echo $post['post_media'] ?>" class="w-100"/>
                        </form>
                    </div>
                    
                </div> 
                <?php
                
            }
        } else if(isset($_POST['tags']) && isset($_POST['postId2'])){
            $postId = $_POST['postId2'];
            $tags = $_POST['tags'];
            $connection = new SQLiteConnection();
            $pdo = $connection->connect();
            if ($pdo != null){
                $tagArray = explode(",", $tags);
                $connection -> updateTagsForPost($postId, $tagArray);
                ?>
                <script type="text/javascript">
                    var tags = [];
                    var tagNumbers = [];
                    $(document).ready(function(){
                        function show_popup(){
                            window.location = "/redditApp/view.php";
                        };
                        window.setTimeout( show_popup, 500 );
                        
                    });
                </script>
                <div class="row bg-secondary h-100">
                <div class="col-lg-4 col-1"></div>
                    <div class="col-lg-4 col-10 bg-light rounded border-bottom border-dark m-1">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    </div>
                </div>
              <?php
            }
        }
    ?>
    </body>
</html>
