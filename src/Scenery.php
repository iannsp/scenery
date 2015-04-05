<?php
namespace Iannsp\Scenery;

use PDO;
use stdClass;

class Scenery
{
    private $data;
    private $actions = [];

    /**
     * @param PDO $initData
     */
    public function __construct(PDO $initData)
    {
        $this->data = $initData;
    }

    /**
     * @param string $name
     * @param callable $action
     * @param callable $expectedDomain
     * @param callable $expectedInfraStructure
     */
    public function action(
        $name,
        callable $action,
        callable $expectedDomain,
        callable $expectedInfraStructure = null
    ) {
        $this->actions[$name] = [
            'action' => $action,
            'expectedDomain' => $expectedDomain,
            'expectedInfraStructure' => $expectedInfraStructure
        ];
    }

    /**
     * @param int $cycleId
     * @param string $loud
     *
     * @return array
     */
    public function run($cycleId, $loud = false)
    {
        $state  = new stdClass();

        $result = ['messages' => []];

        $state->cycle = $cycleId;
        $state->new = $this->data;
        $state->old = $this->data->newFromDsn;
        $state->loud = $loud;

        foreach ($this->actions as $actionItem) {
            $state->messages = [];
            $state->new->exec('Begin;'); // beginTransaction();

            $actionItem['action']($state);
            $actionItem['expectedDomain']($state);

            if (!is_null($actionItem['expectedInfraStructure'])) {
                $actionItem['expectedInfraStructure']($state);
            }

            $state->new->exec('Commit');

            $result['messages'] = array_merge($result['messages'], $state->messages);
        }

        return $result;
    }
}
