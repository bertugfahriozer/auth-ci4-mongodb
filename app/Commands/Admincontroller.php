<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Commands\Ci4msTrait;

/**
 * Generates a skeleton controller file.
 */
class Admincontroller extends BaseCommand
{
    use Ci4msTrait;

    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Ci4MS';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'make:acontroller';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Generates a new controller file.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'make:controller <name> [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'name' => 'The controller class name.',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--bare'      => 'Extends from CodeIgniter\Controller instead of BaseController.',
        '--restful'   => 'Extends from a RESTful resource, Options: [controller, presenter]. Default: "controller".',
        '--namespace' => 'Set root namespace. Default: "APP_NAMESPACE".',
        '--suffix'    => 'Append the component title to the class name (e.g. User => UserController).',
        '--force'     => 'Force overwrite existing file.',
    ];

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        $this->component = 'Controller';
        $this->directory = '..\modules\Backend\Controller';
        $this->template  = 'controller.tpl.php';

        $this->classNameLang = 'CLI.generator.className.controller';
        $this->execute($params);
    }

    /**
     * Prepare options and do the necessary replacements.
     */
    protected function prepare(string $class): string
    {
        $bare = $this->getOption('bare');
        $rest = $this->getOption('restful');

        $useStatement = trim(APP_NAMESPACE, '\\') . '\Controllers\BaseController';
        $extends      = 'BaseController';

        // Gets the appropriate parent class to extend.
        if ($bare || $rest) {
            if ($bare) {
                $useStatement = 'CodeIgniter\Controller';
                $extends      = 'Controller';
            } elseif ($rest) {
                $rest = is_string($rest) ? $rest : 'controller';

                if (! in_array($rest, ['controller', 'presenter'], true)) {
                    // @codeCoverageIgnoreStart
                    $rest = CLI::prompt(lang('CLI.generator.parentClass'), ['controller', 'presenter'], 'required');
                    CLI::newLine();
                    // @codeCoverageIgnoreEnd
                }

                if ($rest === 'controller') {
                    $useStatement = 'CodeIgniter\RESTful\ResourceController';
                    $extends      = 'ResourceController';
                } elseif ($rest === 'presenter') {
                    $useStatement = 'CodeIgniter\RESTful\ResourcePresenter';
                    $extends      = 'ResourcePresenter';
                }
            }
        }

        return $this->parseTemplate(
            $class,
            ['{useStatement}', '{extends}'],
            [$useStatement, $extends],
            ['type' => $rest]
        );
    }
}