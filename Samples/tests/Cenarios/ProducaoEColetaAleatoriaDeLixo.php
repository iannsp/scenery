<?php
require __DIR__.'/../../vendor/autoload.php';


// Um dia qualquer numa cidade qualquer

use SampleDomain\Lixo\Lixo;
use SampleDomain\Lixo\Lixeiro;
use SampleDomain\Lixo\Morador;
use SampleDomain\Lixo\LocalDeRetirada;
use SampleDomain\Lixo\Service\ServicoDeLixo;
use SampleDomain\Lixo\Repository\LixoRepository;

use Iannsp\Scenery\Scenery;
use Iannsp\Scenery\Data;
use Iannsp\Scenery\RunStrategy\Strategy;
use Iannsp\Scenery\RunStrategy\Factory;

use Assert\Assertion;

$dsn ='sqlite:/tmp/lixeiro.sq3';
$pdo = new \PDO( $dsn);
$pdo->exec('
create table sistemaDeLixo(id integer primary key, localDeRetirada integer, Lixao integer)');
$pdo->exec("delete from sistemaDeLixo;vacuum; insert into sistemaDeLixo (localDeRetirada, Lixao) values (0,0)");
$pdo->newFromDsn = new \PDO( $dsn);

$config = 
    $scenery = New Scenery($pdo);
    $scenery->action('Produzir Lixo', function($state){
        
        $umDiaQualquer = new ServicoDeLixo($state->new);    
        $umDiaQualquer->produzirLixo();
        $state->messages[]= "[ACTION] Produzindo Lixo com ServicoDeLixo->produzirLixo";
        
    }, function($state){
        $repoNew = new LixoRepository($state->new);
        $repoOld = new LixoRepository($state->old);
        $newStatelocalDeRetirada = $repoNew->find(1);
        $oldStatelocalDeRetirada = $repoOld->find(1);                   $state->messages[]= "[DOMAIN] antes Tinha {$oldStatelocalDeRetirada['localDeRetirada']} na rua, agora tem {$newStatelocalDeRetirada['localDeRetirada']}";

Assertion::true(($newStatelocalDeRetirada >= $oldStatelocalDeRetirada),"Coleta Esta mais rapida que geração" );
    });

    $scenery->action('Coletar Lixo', function($state){
        $umDiaQualquer = new ServicoDeLixo($state->new);    
        $umDiaQualquer->coletarLixo();
        $state->messages[]= "[ACTION] Coletando Lixo com ServicoDeLixo->coletarLixo";
    }, function($state){
        $repositoryNew = new LixoRepository($state->new);
        $repositoryOld = new LixoRepository($state->old);
        $newStateLixao = $repositoryNew->find(1);
        $oldStateLixao = $repositoryOld->find(1);
        $state->messages[]= "[DOMAIN] Antes Tinha {$oldStateLixao['Lixao']} no Lixão e agora tem {$newStateLixao['Lixao']}";
        Assertion::true(($oldStateLixao['localDeRetirada'] >= $newStateLixao['localDeRetirada']),"Recolhendo Pouco");
    });

    $rodarAte = new \Datetime();
    $rodarAte->add(new \DateInterval("P0YT0M6S"));
    $runnerStrategy = Factory::get(Strategy::RUN_UNTILDATE,$scenery);
    $result = $runnerStrategy->run(['until'=>$rodarAte,'by'=>1,'loud'=>true]);
    $info = ["localDeRetirada"=>0,"Lixao"=>0];
    foreach ($result as $cycle => $r){
        echo "Ciclo: {$cycle}\n";
        foreach ($r['messages'] as $message){
            echo "\t{$message}\n";
        }
    }
    ?>