<?php
namespace Iannsp\Scenery;

use DateTime;
use Iannsp\Scenery\RunStrategy\Factory;
use Iannsp\Scenery\RunStrategy\Strategy;
use PDO;

class SceneryTest extends \PHPUnit_Framework_TestCase
{
    use Connection;

    public function assertPreConditions()
    {
        $this->assertTrue(class_exists(Scenery::class));
    }

    /**
     * @test
     */
    public function initDataModel()
    {
        new Scenery($this->pdo);
    }

    /**
     * @test
     */
    public function addAction()
    {
        $scenery = new Scenery($this->pdo);

        $scenery->action('Altera Uma Pessoa', function () {
            $r = $this->pdo->query("select * from person where id=1", PDO::FETCH_ASSOC);
            $pessoaData = $r->fetchAll()[0];
            $pessoa = new PessoaSample($pessoaData);
            $pessoa->data['name'] = "Ivo Nascimento";
            $pessoa->save($this->pdo);
        }, function () {
            $r = $this->pdo->query("select * from person where id=1", PDO::FETCH_ASSOC);
            $pessoaData = $r->fetchAll()[0];

            $pessoa = new PessoaSample($pessoaData);

            assert('$pessoa->data[\'name\']=="Ivo Nascimento"', "ahhhhhh");
        });
    }

    /**
     * @test
     */
    public function runActions()
    {
        assert_options(ASSERT_ACTIVE, 1);

        $scenery = new Scenery($this->pdo);

        $scenery->action('Altera Uma Pessoa', function ($state) {
            $pessoaData = PessoaSample::find($this->pdo, 1);
            $pessoa = new PessoaSample($pessoaData);
            $state->messages[] = "update Person {$pessoa->data['name']} para 'Ivo Nascimento'";
            $pessoa->data['name'] = "Ivo Nascimento";
            $pessoa->save($this->pdo);
        }, function ($state) {
            $pessoaData = PessoaSample::find($this->pdo, 1);
            $pessoa = new PessoaSample($pessoaData);
            $state->messages[] = " Verificação de Alteração name = {$pessoaData['name']}";

            assert('$pessoa->data[\'name\']=="Ivo Nascimento"', "ahhhhhh");
        });
        $runnerStrategy = Factory::get(Strategy::RUN_BY_CYCLE_NUMBER, $scenery);
        $result = $runnerStrategy->run([
            'cycles' => 1,
            'loud' => false
        ]);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
    }

    /**
     * @test
     */
    public function runActionsLotOfCycle()
    {
        $scenery = new Scenery($this->pdo);

        $scenery->action('Altera Uma Pessoa', function ($state) {
            $pessoaData = PessoaSample::find($state->new, 1);
            $pessoa = new PessoaSample($pessoaData);
            $pessoa->data['name'] = $pessoa->data['name'] . "X";
            $pessoa->save($state->new);
        }, function ($state) {
            $newPessoaData = PessoaSample::find($state->new, 1);
            $oldPessoaData = PessoaSample::find($state->old, 1);

            assert('$oldPessoaData[\'name\']."X"==$newPessoaData["name"]', "Nao esta seguindo a regra");
        });

        $runnerStrategy = Factory::get(Strategy::RUN_BY_CYCLE_NUMBER, $scenery);
        $result = $runnerStrategy->run([
            'cycles' => 10,
            'loud' => false
        ]);

        $this->assertInternalType('array', $result);
        $this->assertCount(10, $result);

        $final = PessoaSample::find($this->pdo, 1);

        $this->assertEquals($final['name'], "IvoXXXXXXXXXX");
    }

    /**
     * @test
     */
    public function runCycleByDateTimeLimit()
    {
        $scenery = new Scenery($this->pdo);
        $scenery->action('Altera Uma Pessoa', function ($state) {
            $pessoaData = PessoaSample::find($state->old, 1);
            $pessoa = new PessoaSample($pessoaData);
            $state->messages[] = "[ACTION] Nome Original = {$pessoaData['name']}";
            $pessoa->data['name'] = $pessoa->data['name'] . "X";
            $state->messages[] = "[ACTION] Nome Alterado +1'X' = {$pessoaData['name']} para {$pessoa->data['name']}";
            $pessoa->save($state->new);
        }, function ($state) {
            $newPessoaData = PessoaSample::find($state->new, 1);
            $oldPessoaData = PessoaSample::find($state->old, 1);
            $state->messages[] = "[DOMAIN] Old:{$oldPessoaData['name']}, new: {$newPessoaData['name']}";

            \Assert\that($newPessoaData['name'])->contains('IvoX');
        });

        $runnerStrategy = Factory::get(Strategy::RUN_UNTILDATE, $scenery);

        $result = $runnerStrategy->run([
            'until' => new DateTime('+4 seconds'),
            'by' => 1,
            'loud' => false
        ]);

        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);

        $final = PessoaSample::find($this->pdo, 1);
        $this->assertEquals($final['name'], "IvoXXXX");
    }
}
