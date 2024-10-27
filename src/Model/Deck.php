<?php

declare(strict_types=1);

namespace App\Model;

class Deck extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'deck';

    public function creer(array $datas): ?int{
        return $this->create($datas);
    }

    public function find(
        int $id
    ): ?array {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE id_deck = :id";
        $sth = $this->query($sql, [':id' => $id]);
        $rows = $sth->fetch();
        if ($rows && count($rows)) {
            return $rows;
        }
        return null;
    }
}
