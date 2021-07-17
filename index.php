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
    <body style="max-width: 100%;overflow-x: hidden;">
        <script type="text/javascript">
            var tags = [];
            var tagNumbers = [];
            $(document).ready(function(){
                
                var $form = $('#newTagForm');
                $form.submit(function(){
                    $.post($(this).attr('action'), $(this).serialize(), function(response){
                            // do something here on success
                    },'json');
                    function show_popup(){
                        location.reload();
                    };
                    window.setTimeout( show_popup, 500 );
                    
                    return false;
                    
                });
                $('button').click(function() {
                    var id = $(this).attr('id');
                    if(id != "savePostFormSubmit" || id != "addTagFormSubmit"){
                        var ajaxurl = 'tags.php';
                        data =  {removeTag: id};
                        $.post(ajaxurl, data, function(response){
                            // do something here on success
                        },'json');
                        function show_popup(){
                            location.reload();
                        };
                        window.setTimeout( show_popup, 500 );
                    }else if (id == "addTagToPost") {
                        //var tagDrop = $('#tagDrop').text();
                        //$('#tagsList').val("test");
                        
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
        <h1 class="text-center">Welcome to the reddit post saver!</h1>
        <h4 class="text-center"><a href="view.php">View posts</a></h4>
        <form action="save.php" method="POST" id="postSaveForm">
            <div class="row">
                <div class="col-lg-4 col-1"></div>
                <div class="col-lg-4 col-10">
                    <label for="url">Url of Reddit post</label>
                    <input type="url" placeholder="URL to post" name="url"/>

                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-1"></div>
                <div class="col-lg-4 col-10">
                    <label for="tags">Tags to apply to post : </label>
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
                    <input type="submit" value="Submit" id="savePostFormSubmit" />
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-4 col-1"></div>
            <div class="col-lg-4 col-10">
                <form action="tags.php" method="POST" id="newTagForm">
                    <h3 class="text-center">Manage Tags</h3>
                    <input name="newTag" type="text" placeholder="Tag name"/>
                    
                    <input type="submit" value="Submit" id="addTagFormSubmit">
                </form>
                <h4 class="text-center">Current tags</h4>
                
                <?php 
                    if($tags != null){
                        foreach($tags as $tag){
                            $id = $tag['tag_id'];
                            $name = $tag['tag_name'];
                            echo "<p class=\"text-center\">$id : $name <button id=\"$id\" class='btn btn-outline-danger'> X </button></p>";
                        }
                    }
                ?>
                
            </div>
        </div>
        
        
        <!-- <img src="https://i.redd.it/ydbsn0p2h7671.jpg" class="w-50"/> -->
        <!-- <video width="320" height="240" controls>
            <source src="https://v.redd.it/yl34cq3wih771" type="video/mp4"/>
        </video> -->
    </body>
</html>
<?php
/*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'vendor/autoload.php';

    use App\SQLiteConnection;

    $connection = new SQLiteConnection();
    $pdo = $connection->connect();
    if ($pdo != null){
        echo 'Connected to the SQLite database successfully! <br/>';
        print_r($connection->getTableList());
        //echo $connection->getTableList();
    }else{
        echo 'Whoops, could not connect to the SQLite database! <br/>';
    }*/
?>
