<?php
    class Database {
        private $host = DB_HOST;
        private $user = DB_USER;
        private $password = DB_PASSWORD;
        private $dbname = DB_NAME;

        private $dbh;   //database handler
        private $stmt;  // statement
        private $error; // for exception handling
        private $lastSql = '';
        private $queryTimeoutSeconds = 10;
        private $isMariaDb = false;

        public function __construct() {
            // Set DSN (include port and charset if defined)
            $port = defined('DB_PORT') ? DB_PORT : 3306;
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
            $dsn = 'mysql:host=' . $this->host . ';port=' . $port . ';dbname=' . $this->dbname . ';charset=' . $charset;

            // Set options
            $options = array(
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 10,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION innodb_lock_wait_timeout=10, SESSION max_statement_time=10"
            );

            // Create a new PDO instance
            try {
                $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
                $this->detectServerType();
                $this->configureSessionTimeouts();
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                echo 'Database connection error: ' . $this->error; 
            }
        }

        private function detectServerType() {
            try {
                $version = $this->dbh->getAttribute(PDO::ATTR_SERVER_VERSION);
                $this->isMariaDb = stripos((string)$version, 'mariadb') !== false;
            } catch (PDOException $e) {
                $this->isMariaDb = false;
            }
        }

        private function configureSessionTimeouts() {
            try {
                // Applies to metadata lock waits (e.g., table DDL contention).
                $this->dbh->exec('SET SESSION lock_wait_timeout=' . (int)$this->queryTimeoutSeconds);
            } catch (PDOException $e) {
                // Keep going if the variable is unavailable.
            }

            try {
                $this->dbh->exec('SET SESSION innodb_lock_wait_timeout=' . (int)$this->queryTimeoutSeconds);
            } catch (PDOException $e) {
                // Keep going if the variable is unavailable.
            }

            try {
                if ($this->isMariaDb) {
                    $this->dbh->exec('SET SESSION max_statement_time=' . (float)$this->queryTimeoutSeconds);
                } else {
                    $this->dbh->exec('SET SESSION MAX_EXECUTION_TIME=' . ((int)$this->queryTimeoutSeconds * 1000));
                }
            } catch (PDOException $e) {
                // Keep going if the variable is unavailable.
            }
        }

        // Other methods for database operations can be added here...
        public function query($sql){
            $this->lastSql = $sql;
            if (!$this->dbh) {
                throw new RuntimeException('Database connection is not available.');
            }

            $sqlToPrepare = $sql;
            if ($this->isMariaDb && preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|REPLACE)\b/i', $sql)) {
                $timeout = (float)$this->queryTimeoutSeconds;
                $sqlToPrepare = 'SET STATEMENT max_statement_time=' . $timeout . ' FOR ' . $sql;
            } elseif (!$this->isMariaDb && preg_match('/^\s*SELECT\b/i', $sql)) {
                // MySQL supports MAX_EXECUTION_TIME optimizer hint on SELECT statements.
                $timeoutMs = (int)$this->queryTimeoutSeconds * 1000;
                $sqlToPrepare = preg_replace('/^\s*SELECT\s+/i', 'SELECT /*+ MAX_EXECUTION_TIME(' . $timeoutMs . ') */ ', $sql, 1);
            }

            $this->stmt = $this->dbh->prepare($sqlToPrepare);
        }

        // Bind values to the prepared statement
        public function bind($param, $value, $type = null) {
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
        }

        // Execute the prepared statement
        public function execute(){
            if (!$this->stmt) {
                throw new RuntimeException('No prepared statement found. Call query() first.');
            }

            $startedAt = microtime(true);
            try {
                $result = $this->stmt->execute();
                $elapsed = microtime(true) - $startedAt;
                if ($elapsed > 2.0) {
                    $sqlPreview = preg_replace('/\s+/', ' ', (string)$this->lastSql);
                    $sqlPreview = substr($sqlPreview, 0, 300);
                    error_log('Slow SQL (' . round($elapsed, 3) . 's): ' . $sqlPreview);
                }
                return $result;
            } catch (PDOException $e) {
                $sqlPreview = preg_replace('/\s+/', ' ', (string)$this->lastSql);
                $sqlPreview = substr($sqlPreview, 0, 300);
                error_log('SQL execute failed: ' . $e->getMessage() . ' | SQL: ' . $sqlPreview);
                throw $e;
            }
        }

        /**
         * Get multiple records as the result
         * @return array
         */
        public function resultSet(){
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        }

        /**
         * Get a single record as the result
         * @return object|false
         */
        public function single(){
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        }

        // Check record existence
        public function rowCount(){
            return $this->stmt->rowCount();
        }

        // Transaction helpers
        public function beginTransaction() {
            return $this->dbh->beginTransaction();
        }

        public function commit() {
            return $this->dbh->commit();
        }

        public function rollBack() {
            return $this->dbh->rollBack();
        }
                // Return last inserted ID from the PDO instance
        public function lastInsertId(){
            if($this->dbh){
                return $this->dbh->lastInsertId();
            }
            return null;
        }
    }
?>