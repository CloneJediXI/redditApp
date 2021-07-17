<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'vendor/autoload.php';
    use App\SQLiteConnection;
?>
<html>
    <head>
        <title>Reddit Post Saver</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    </head>
    <body style="max-width: 100%;overflow-x: hidden;" class="pb-5 pt-5">
    <script type="text/javascript">
            var tags = [];
            var tagNumbers = [];
            $(document).ready(function(){
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
        <h4 class="text-center"><a href="index.php">Home</a></h4>
        <label for="tags">Show posts with tags : </label>
        <!--<input id="tagsList" name="tags" type="text" disabled /> -->
        <span role="textbox" id="tagsList" name="tags" style="border: 1px solid #ccc;padding: 1px 6px; margin-left: 5px;"></span>
        
        <input id="tagsIdList" name="tags" type="hidden"/>
        <br/>
        <select name="tagList" id="tagDrop">
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
        <?php 
            $num = 0;
            $page = 1;
            if(isset($_GET['num'])) {
                $num = $_GET['num'];
            } else {
                $num = 0;
            }
            if(isset($_GET['page'])) {
                $page = $_GET['page'];
            } else {
                $page = 1;
            }
            $connection = new SQLiteConnection();
            $pdo = $connection->connect();
            if ($pdo != null){
                $posts = $connection->getAllPostData();
                
                foreach($posts as $post){
                    echo '<div class="row bg-secondary">';
                    echo '<div class="col-lg-4 col-1"></div>';
                    echo '<div class="col-lg-4 col-10 bg-light rounded border-bottom border-dark">';
                    echo '<h3><a href="'.$post['post_link'].'" target="_blank">'.$post['post_title'].'</a></h3>';
                    $tags = $connection->getTagsForPost($post['post_id']);
                    foreach($tags as $tag){
                        echo '<span class="badge badge-pill badge-info">'.$tag['tag_name'].'</span>';
                    }
                    echo '<br />';
                    echo '<p>'.$post['post_subreddit'].'</p>';
                    echo '<img src="'.$post['post_media'].'" class="w-100"/>';
                    echo '</div>';
                    echo '</div>';
                }
            }
        ?>
        
    
    </body>
</html>