# Iannsp\Scenery#

*Scenery é uma __ferramenta de criação de cenários__ para __testes de integração__ e validação de expectativas nas camadas da __aplicação/domínio__ e __infra estrutura__ que permite __execuções ciclicas__(repetições do conjunto de ações) para simulações de __comportamento real__ de uso.*


####Inspiração####

"A gente devia ter duas vidas, uma pra ensaiar e outra para viver." ([Vittorio Gassman](http://pt.wikipedia.org/wiki/Vittorio_Gassman))



###Scenery###

Um cenário é uma *configuração de ambiente para execução* e teste de *expectativas* de um conjunto de *Ações*. 

As caracteristicas de um cenário são:

* Conjunto inicial de dados.
* Conjunto de ações.
* Estratégia de execução. 

####Criando um cenário####

```php
   $initialDataState = new Data();
   $scenery = new Scenery($initialDataState); 
```


###Action###

Action é um **comportamento** existente em um **Domínio** cujo **efeito** pode ser testado utilizando **expectativas**.

características de uma Action:

* uma Action tem um estado composto por
     * estado dos Dados
         * New: Os dados no estado atual do cenário.
         * Old: Os dados no estado anterior a execução da Action.

    * transienteData: Dados temporários no escopo da ação e suas expectativas:
        * Cycle: O número identificador do ciclo de execução em que esta o cenário.
        * udd: user defined data. Dados transientes definidos pela action necessários para sua execução e/ou teste de expectativas. 
 
####Criando uma ação####

```php
    $action = function($state){
            // comportamento do domínio ou aplicação. 
        }, function($state){
            // expectativa do domínio.
        }, function ($state){
            // expectativa na infra estrutura.
        }
    );
    // adicionando a action em um cenário.
    $scenery->action([Nome da Action], $action);
```

### Expectation ###

Expectativa é uma verificação dos efeitos relacionados a execução de uma ação. 

Caracteristicas de uma expectativa:
* São dois tipos de expectativa.
 * layer de Domínio (expectedDomain): utiliza código de domínio para validar a Action.
 * layer de infra estrutura (expectedInfraStructure): utiliza recursos de infra estrutura para validar a Action.
* cada tipo de expectativa tem seu conjunto de assertions. 
 

# Estado dos Dados #

O estado do dados é representado por uma estrutura de array representando o modelo dos dados utilizados.

Se um sistema utiliza uma tabela modelada como 

```sql
create table Person
(
id serial primary key,
name varchar(100),
email varchar(100)
)
``` 

O estado dos dados após inserir uma person de nome 'Ivo' e email 'iannsp@gmail.com' é.

```php
        $dsn ='sqlite:/tmp/sceneryTest.sq3';
        $pdo = new \PDO( $dsn);
        $pdo->exec('
        drop table person; 
        create table person(id integer primary key, name CHARACTER(100), email CHARACTER(100))');
        $pdo->exec("insert into person (name, email) values ('Ivo','iannsp@gmail.com')");
        // usei o hack pq clone esta dando segmentFault(precisa verificar o que estou fazendo de errado ou se é bug).
        $pdo->newFromDsn = new \PDO( $dsn);
```
####Exemplo de Cenário####

Caracteristicas:
* tem somente uma ação chamada *Criar Pessoa*.
* sua estratégia de execução é por Número de Ciclos e sera executado 1 vez.
 
```php

// require o bootstrap da aplicação.
require 'bootstrap.php';

    // cria o cenário utilizando um conjunto de dados inicial.
    $scenery = new Scenery($pdo);

    // adiciona a Action Criar Pessoa
    $scenery->action('Criar Pessoa', function($state){
        // código da Action
        $instanciaDeServicoDePessoa = new \Domain\Service\Person();
        $state->addTransienteData('person', ['nome'=>'Ivo','email'=>'iannsp@gmail.com']);
        $instanciaDeServicoDePessoa->create('Ivo', 'iannsp@gmail.com');
    }, function($state){
        // teste de Domain layer.
        $person = $state->getTransienteData('person');
        $instanciaDeServicoDePessoa = new \Domain\Service\Person();
        $persons = $instanciaDeServicoDePessoa->find(['nome'=>'Ivo']);
        $state->assertCount(1,$persons);
        $state->assertEquals($persons[0]->get('nome'), $person['nome']);
        }, function($state){
          // teste de infra estrutura
          $newPersons = $state->new->get(['person'=>[]]);
          $oldPersons = $state->old->get(['person'=>[1]]);
          $state->assertCount(0, $oldPersons);
          $state->assertCount(1, $newPersons);
        }
    );
    
    // executando o cenário com uma estratégia de execucação por número de ciclos.
    $runnerStrategy = factory::get(Strategy::RUN_BY_CYCLE_NUMBER,$scenery);
    $results = $runnerStrategy->run(1);
    
    //executando o cenário durante meia hora com intervalo(by) de 1 segundo entre cada iteracao.
    $executarDuranteMeiaHora = new \Datetime();
    $executarDuranteMeiaHora->add(new \DateInterval("P0YT30M0S"));
    $runnerStrategy = factory::get(Strategy::RUN_UNTILDATE,$scenery);
    $results = $runnerStrategy->run(['until'=>$executarDuranteMeiaHora,'by'=>1]);
```

    