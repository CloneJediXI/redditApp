<?php
    if(isset($_POST['url'])) {
        $url = $_POST['url'];
        echo "url is $url";
        $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n". $url);
        fclose($myfile);
        //file_put_contents('logs.txt', $url , FILE_APPEND);
        echo "alert('Success!')";
    }else{
        echo "alert('Oops! Something went wrong!')";
    }
?>
