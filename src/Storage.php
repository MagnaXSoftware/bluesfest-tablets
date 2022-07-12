<?php

namespace App;

use App\Models\Area;
use App\Models\Status;
use PDO;

class Storage
{

    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;

        $this->db->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS areas (
    id INTEGER PRIMARY KEY AUTOINCREMENT ,
    name TEXT NOT NULL ,
    expected INTEGER
);
SQL
        );

        $this->db->exec(
            <<<'SQL'
CREATE TABLE IF NOT EXISTS status (
    id INTEGER PRIMARY KEY,
    area_id INTEGER NOT NULL,
    status TEXT NOT NULL,
    deployed INTEGER NOT NULL,
    stored INTEGER NOT NULL,
    recovered INTEGER NOT NULL,
    notes TEXT,
    timestamp INTEGER NOT NULL,
    actor TEXT NOT NULL,
    FOREIGN KEY (area_id) REFERENCES areas(id)
)
SQL

        );
    }

    /**
     * @return Area[]
     */
    public function getAreas(): array
    {
        $res = $this->db->query('SELECT id, name, expected from areas');

        if (false === $res) {
            return [];
        }

        return $res->fetchAll(PDO::FETCH_CLASS, Area::class);
    }

    public function getArea(int $id): ?Area
    {
        $stmt = $this->db->prepare('SELECT id, name, expected FROM areas WHERE id = :id');
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $q = $stmt->execute();
        if (false === $q) {
            return null;
        }
        $obj = $stmt->fetchObject(Area::class);
        $stmt->closeCursor();
        return $obj;
    }

    public function getAreaWithStatuses(int $id): ?Area
    {
        $area = $this->getArea($id);
        if (is_null($area))
            return $area;

        $area->setStatuses($this->getStatusesForArea($area));
        return $area;
    }

    public function addArea(Area $area): bool
    {
        $stmt = $this->db->prepare('INSERT INTO areas (name) VALUES (:name)');
        $stmt->bindValue('name', $area->getName());
        return $stmt->execute();
    }

    public function updateArea(Area $area): bool
    {
        $stmt = $this->db->prepare('UPDATE areas SET name = :name, expected = :expected WHERE id = :id');
        $stmt->bindValue('name', $area->getName());
        $stmt->bindValue('expected', $area->getExpected(), PDO::PARAM_INT);
        $stmt->bindValue('id', $area->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @return Status[]
     */
    public function getLatestStatuses(): array
    {
        $stmt = $this->db->prepare(<<<'SQL'
SELECT
    a.id as area_area_id,
    a.name as area_area_name,
    ( CASE WHEN s.status IS NULL THEN 'na' ELSE s.status END ) as status,
    ( CASE WHEN s.deployed IS NULL THEN 0 ELSE s.deployed END ) as deployed,
    ( CASE WHEN s.stored IS NULL THEN 0 ELSE s.stored END ) as stored,
    ( CASE WHEN s.recovered IS NULL THEN 0 ELSE s.recovered END ) as recovered,
    ( CASE WHEN s.timestamp IS NULL THEN 0 ELSE s.timestamp END ) as timestamp,
    ( CASE WHEN s.notes IS NULL THEN '' ELSE s.notes END ) as notes,
    ( CASE WHEN s.actor IS NULL THEN '' ELSE s.actor END ) as actor
FROM
    areas a
        LEFT OUTER JOIN status s ON s.area_id = a.id
        LEFT OUTER JOIN status s2
                        ON (s.area_id = s2.area_id AND s.timestamp < s2.timestamp)
WHERE
    s2.area_id IS NULL;
        
SQL
        );
        $res = $stmt->execute();
        if (false === $res)
            return [];

        $objs = $stmt->fetchAll(PDO::FETCH_CLASS, Status::class);
        $stmt->closeCursor();
        return $objs;
    }

    /**
     * @param Area|int $area
     * @return Status[]
     */
    public function getStatusesForArea($area): array
    {
        $area_id = $area;
        if ($area instanceof Area)
            $area_id = $area->getId();

        $stmt = $this->db->prepare(<<<'SQL'
SELECT
    status,
    deployed,
    stored,
    recovered,
    timestamp,
    notes,
    actor
FROM
    status
WHERE
    area_id = :area_id
ORDER BY timestamp DESC;
SQL
);
        $stmt->bindValue('area_id', $area_id, PDO::PARAM_INT);
        $res = $stmt->execute();
        if (false === $res)
            return [];

        $objs = $stmt->fetchAll(PDO::FETCH_CLASS, Status::class);
        $stmt->closeCursor();
        return $objs;
    }

    public function addStatus(Status $status): bool
    {
        $stmt = $this->db->prepare(<<<'SQL'
INSERT INTO 
    status (area_id, status, deployed, stored, recovered, timestamp, notes, actor)
VALUES
    (:area_id, :status, :deployed, :stored, :recovered, :timestamp, :notes, :actor)
;    
SQL
        );
        $stmt->bindValue('area_id', $status->getAreaId(), PDO::PARAM_INT);
        $stmt->bindValue('status', $status->getStatus()->getValue());
        $stmt->bindValue('deployed', $status->getDeployed(), PDO::PARAM_INT);
        $stmt->bindValue('stored', $status->getStored(), PDO::PARAM_INT);
        $stmt->bindValue('recovered', $status->getRecovered(), PDO::PARAM_INT);
        $stmt->bindValue('timestamp', $status->getTimestamp(), PDO::PARAM_INT);
        $stmt->bindValue('notes', $status->getNotes());
        $stmt->bindValue('actor', $status->getActor());

        return $stmt->execute();
    }

}