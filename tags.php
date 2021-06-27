<?php
    require 'vendor/autoload.php';

    use App\Secrets;
    use App\SQLiteConnection;

    if(isset($_POST['newTag'])) {
        $connection = new SQLiteConnection();
        $pdo = $connection->connect();
        if ($pdo != null){
            $connection->addTag($_POST['newTag']);
        }
    }else if(isset($_POST['removeTag'])) {
        $connection = new SQLiteConnection();
        $pdo = $connection->connect();
        $id = intval($_POST['removeTag']);
        if ($pdo != null){
            $connection->removeTag($id);
        }
    }
?>