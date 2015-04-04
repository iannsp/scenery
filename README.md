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
 * transienteData: Dados temporários disponíveis durante a execução de uma ação e suas expectativas:
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

### O que é uma Expectativa(*Expectation*)? ###

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
$data = [
    'person'=>[
        ['id'=>1, 'name'=>'Ivo', 'email'=>'iannsp@gmail.com']
    ]
];
```
####Exemplo de Cenário####

Este cenário tem as seguintes caracteristicas
* tem somente uma ação chamada *Criar Pessoa*.
* sua estratégia de execução é por Número de Ciclos e sera executado 1 vez.
 
```php

// require o bootstrap da aplicação.
require 'bootstrap.php';

    // cria o cenário utilizando um conjunto de dados vazios.
    $scenery = new Scenery(new Data());

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
          $newPersons = $state['new']->get(['person'=>[]]);
          $oldPersons = $state['old']->get(['person'=>[1]]);
          $state->assertCount(0, $oldPersons);
          $state->assertCount(1, $newPersons);
        }
    );
    
    // executando o cenário com uma estratégia de execucaçnao por número de ciclos.
    $results = $scenery->run(Scenery::RUN_BY_CYCLE_NUMBER,1);
```

    