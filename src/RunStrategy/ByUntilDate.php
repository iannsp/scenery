<?php
namespace Iannsp\Scenery\RunStrategy;

use DateTime;
use Iannsp\Scenery\Scenery;

class ByUntilDate implements Strategy
{
    /**
     * @var Scenery
     */
    private $scenery;

    private $cycleCollection = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(Scenery $scenery)
    {
        $this->scenery = $scenery;
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $rule)
    {
        if (!$rule['until'] instanceof DateTime || !is_numeric($rule['by'])) {
            throw new InsufficientParametersException(
                "rule for run BYUntilDate Strategy is [\\DateTime until, numeric by]"
            );
        }

        $result = [];
        $untilDate = $rule['until'];
        $cycleId = 0;

        $now = new DateTime();

        $diff = (int) $untilDate->format('U') - (int) $now->format("U");

        while ($diff > 0) {
            $result[$cycleId] = $this->scenery->run($cycleId, $rule['loud']);
            ++$cycleId;

            usleep(1000000 * $rule['by']);

            if ($rule['loud']) {
                ob_start();
                echo "cycle of {$diff} seconds\n";
                ob_end_flush();
            }

            $now = new DateTime();
            $diff = (int) $untilDate->format('U') - (int) $now->format("U");
        }

        return $result;
    }
}
