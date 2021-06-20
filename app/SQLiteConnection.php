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


    public function createTables(){
        $commands = ['CREATE TABLE IF NOT EXISTS posts (
                        post_id   TEXT NOT NULL PRIMARY KEY,
                        post_title TEXT,
                        post_content TEXT,
                        post_link TEXT,
                        post_is_video INTEGER,
                        post_media TEXT,
                    )',
                    'CREATE TABLE IF NOT EXISTS tags (
                        tag_id   INTEGER PRIMARY KEY,
                        tag_name TEXT NOT NULL
                    )',
                    'CREATE TABLE IF NOT EXISTS postTags (
                        tag_id INTEGER NOT NULL PRIMARY KEY,
                        post_id TEXT NOT NULL PRIMARY KEY,
                        FOREIGN KEY (tag_id,post_id)
                            REFERENCES projects(post_id) 
                                ON UPDATE CASCADE
                                ON DELETE CASCADE)
                            REFERENCES tags(tag_id) 
                                ON UPDATE CASCADE
                                ON DELETE CASCADE)'];
        // execute the sql commands to create new tables
        foreach ($commands as $command) {
            $this->pdo->exec($command);
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
     * return in instance of the PDO object that connects to the SQLite database
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