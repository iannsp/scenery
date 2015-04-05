<?php
namespace Iannsp\Scenery;

use PDO;

class PessoaSample
{
    /**
     * @var array
     */
    public $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param PDO $pdo
     */
    public function save(PDO $pdo)
    {
        $count = $pdo->prepare('SELECT COUNT(id) FROM person WHERE id=?');
        $count->execute([$this->data['id']]);

        if ($count->fetchColumn() == 1) {
            $update = $pdo->prepare('UPDATE person SET name=:name, email=:email WHERE id=:id');
            $update->execute($this->data);
            return;
        }

        $insert = $pdo->prepare('INSERT INTO person (name, email) VALUES (?, ?)');
        $insert->execute([$this->data['name'], $this->data['email']]);
    }

    /**
     * @param PDO $pdo
     * @param int $id
     *
     * @return array
     */
    public static function find(PDO $pdo, $id)
    {
        $find = $pdo->prepare('SELECT * FROM person WHERE id=?');
        $find->execute([$id]);

        return $find->fetch(PDO::FETCH_ASSOC);
    }
}
