<?php
namespace Iannsp\Scenery;

trait Connection
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @before
     */
    public function stablishConnection()
    {
        $dsn ='sqlite:/tmp/sceneryTest.sq3';
        $this->pdo = new \PDO($dsn, null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        $this->pdo->exec(
            'drop table IF EXISTS person;
            create table person(id integer primary key, name CHARACTER(100), email CHARACTER(100))'
        );
        $this->pdo->exec("insert into person (name, email) values ('Ivo','iannsp@gmail.com')");
        $this->pdo->newFromDsn = new \PDO($dsn, null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }
}
