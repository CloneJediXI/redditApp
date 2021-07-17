<?php
namespace App;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * SQLite connnection
 */
class SQLiteConnection {
    /**
     * PDO instance
     * @var type 
     */
    private $pdo;

    /**
     * return in instance of the PDO object that connects to the SQLite database
     */
    public function createTables(){
        $commands = [
                    'CREATE TABLE IF NOT EXISTS posts (
                        post_id TEXT PRIMARY KEY,
                        post_title TEXT,
                        post_subreddit TEXT,
                        post_content TEXT,
                        post_html TEXT,
                        post_link TEXT,
                        post_media TEXT,
                        post_is_video TEXT,
                        post_video_height INTEGER,
                        post_video_width INTEGER
                    )',
                    'CREATE TABLE IF NOT EXISTS tags (
                        tag_id INTEGER PRIMARY KEY,
                        tag_name TEXT NOT NULL
                    )',
                    'CREATE TABLE IF NOT EXISTS postTags (
                        tag_id INTEGER NOT NULL,
                        post_id TEXT NOT NULL,
  						PRIMARY KEY (tag_id,post_id)
                        FOREIGN KEY (post_id)
                            REFERENCES posts(post_id) 
                                ON UPDATE CASCADE
                                ON DELETE CASCADE,
  						FOREIGN KEY (post_id)
                            REFERENCES posts(post_id) 
                                ON UPDATE CASCADE
                                ON DELETE CASCADE)'];
        // execute the sql commands to create new tables
        foreach ($commands as $command) {
            try{
                $this->pdo->exec($command);
            }catch (\PDOException $e) {
                echo $e;
            }
        }
    }

    public function getTableList() {

        $stmt = $this->pdo->query("SELECT name
                                   FROM sqlite_master
                                   WHERE type = 'table'
                                   ORDER BY name");
        $tables = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tables[] = $row['name'];
        }

        return $tables;
    }

    /**
     * Takes an array of post data. They keys are the column names and the values are the values to insert
     */
    public function insertPostData($postData){
        $sql = 'INSERT INTO posts(post_id,post_title,post_subreddit,post_content,post_html,post_link,post_media,post_is_video,post_video_height,post_video_width) '
                        . 'VALUES(:post_id,:post_title,:post_subreddit,:post_content,:post_html,:post_link,:post_media,:post_is_video,:post_video_height,:post_video_width)';
        //echo $sql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':post_id' => $postData['post_id'],
            ':post_title' => $postData['post_title'],
            ':post_subreddit' => $postData['post_subreddit'],
            ':post_content' => $postData['post_content'],
            ':post_html' => $postData['post_html'],
            ':post_link' => $postData['post_link'],
            ':post_media' => $postData['post_media'],
            ':post_is_video' => $postData['post_is_video'],
            ':post_video_height' => $postData['post_video_height'],
            ':post_video_width' => $postData['post_video_width'],
        ]);
    }
    public function addTag($tagName){
        $sql = 'INSERT INTO tags(tag_name) '
                        . 'VALUES(:tag_name)';
        //echo $sql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':tag_name' => $tagName,
        ]);
        //$stmt->execute();
    }
    public function removeTag($tagId){
        $sql = 'DELETE FROM tags WHERE tag_id=:tag_id';
        //echo $sql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':tag_id' => $tagId,
        ]);
        //$stmt->execute();
    }

    public function getAllPostData(){
        $stmt = $this->pdo->query('SELECT post_id, post_title, post_subreddit, post_content,' 
                                    . 'post_html, post_link, post_media, post_is_video, post_video_height, post_video_width '
                                . 'FROM posts');
        $posts = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $posts[] = [
                'post_id' => $row['post_id'],
                'post_title' => $row['post_title'],
                'post_subreddit' => $row['post_subreddit'],
                'post_content' => $row['post_content'],
                'post_html' => $row['post_html'],
                'post_link' => $row['post_link'],
                'post_media' => $row['post_media'],
                'post_is_video' => $row['post_is_video'],
                'post_video_height' => $row['post_video_height'],
                'post_video_width' => $row['post_video_width']
            ];
        }
        //print_r($posts);
        return $posts;
    }
    public function getAllTags(){
        $stmt = $this->pdo->query('SELECT tag_id, tag_name '
                                . 'FROM tags');
        $tags = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tags[] = [
                'tag_id' => $row['tag_id'],
                'tag_name' => $row['tag_name']
            ];
        }
        //print_r($tags);
        return $tags;
    }
    public function getTagsForPost($postId){
        $stmt = $this->pdo->prepare('SELECT tag_name
                                    FROM tags JOIN postTags ON tags.tag_id=postTags.tag_id
                                    WHERE post_id=:post_id');
        $stmt->execute([
            ':post_id' => $postId
        ]);
        $tags = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tags[] = [
                'tag_name' => $row['tag_name']
            ];
        }
        //print_r($tags);
        return $tags;
    }
    public function setTagsForPost($postId, $tags){
        $sql = 'INSERT INTO postTags(post_id,tag_id) '
                        . 'VALUES(:post_id,:tag_id)';
        //echo $sql;
        foreach ($tags as $tag){
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':post_id' => $postId,
                ':tag_id' => $tag,
            ]);
        }
    }

    /**
     * return an instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect() {
        if ($this->pdo == null) {
            try {
                $this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
             } catch (\PDOException $e) {
                echo $e;
             }
        }
        return $this->pdo;
    }

}
?>