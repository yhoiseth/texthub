<?php
namespace Group;

use \Codeception\Event\TestEvent;
/**
 * Group class is Codeception Extension which is allowed to handle to all internal events.
 * This class itself can be used to listen events for test execution of one particular group.
 * It may be especially useful to create fixtures data, prepare server, etc.
 *
 * INSTALLATION:
 *
 * To use this group extension, include it to "extensions" option of global Codeception config.
 */

class PrepareDatabase extends \Codeception\Platform\Group
{
    public static $group = 'prepare_database';

    public function _before(TestEvent $e)
    {
        $commands = [
            'doctrine:database:create',
            'doctrine:database:drop --force',
            'doctrine:database:create',
            'doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet',
        ];

        foreach ($commands as $command) {
            exec('bin/console ' . $command . ' --env=dev');
        }
    }

    public function _after(TestEvent $e)
    {
    }
}
