<?php
/**
 * Stores PHP sessions in MySQL so they survive across serverless containers.
 * Falls back to default file sessions if the database is unreachable.
 */
class DbSessionHandler implements SessionHandlerInterface {
    private PDO $pdo;

    public function __construct() {
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'cse472_skill_exchange';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $this->pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
        );
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(128) NOT NULL PRIMARY KEY,
                data MEDIUMBLOB NOT NULL,
                last_access INT UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }

    public function read(string $id): string|false {
        $stmt = $this->pdo->prepare("SELECT data FROM sessions WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetchColumn();
        return $data === false ? '' : (string) $data;
    }

    public function write(string $id, string $data): bool {
        $stmt = $this->pdo->prepare("REPLACE INTO sessions (id, data, last_access) VALUES (:id, :data, :t)");
        return $stmt->execute([':id' => $id, ':data' => $data, ':t' => time()]);
    }

    public function destroy(string $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function gc(int $max_lifetime): int|false {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE last_access < :t");
        $stmt->execute([':t' => time() - $max_lifetime]);
        return $stmt->rowCount();
    }
}

try {
    session_set_save_handler(new DbSessionHandler(), true);
} catch (Throwable $e) {
    // Database unreachable: keep default file-based sessions
}