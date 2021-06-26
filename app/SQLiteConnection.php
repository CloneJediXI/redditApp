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