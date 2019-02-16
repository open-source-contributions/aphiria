<?php

/*
 * Opulence
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/console/blob/master/LICENSE.md
 */

namespace Aphiria\Console\Commands;

use Aphiria\Console\Requests\Argument;
use Aphiria\Console\Requests\ArgumentTypes;
use Aphiria\Console\Requests\Option;
use Aphiria\Console\Responses\Formatters\CommandFormatter;
use Aphiria\Console\Responses\Formatters\PaddingFormatter;
use Aphiria\Console\Responses\IResponse;

/**
 * Defines the help command
 */
class HelpCommand extends Command
{
    /** @var string The template for the output */
    private static $template = <<<EOF
-----------------------------
Command: <info>{{name}}</info>
-----------------------------
<b>{{command}}</b>

<comment>Description:</comment>
  {{description}}
<comment>Arguments:</comment>
{{arguments}}
<comment>Options:</comment>
{{options}}{{helpText}}
EOF;
    /** @var ICommand The command to help with */
    private $command;
    /** @var CommandFormatter The formatter that converts a command object to text */
    private $commandFormatter;
    /** @var PaddingFormatter The space padding formatter to use */
    private $paddingFormatter;

    /**
     * @param CommandFormatter $commandFormatter The formatter that converts a command object to text
     * @param PaddingFormatter $paddingFormatter The space padding formatter to use
     */
    public function __construct(CommandFormatter $commandFormatter, PaddingFormatter $paddingFormatter)
    {
        parent::__construct();

        $this->commandFormatter = $commandFormatter;
        $this->paddingFormatter = $paddingFormatter;
    }

    /**
     * Sets the command to help with
     *
     * @param ICommand $command The command to help with
     */
    public function setCommand(ICommand $command): void
    {
        $this->command = $command;
    }

    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        $this->setName('help')
            ->setDescription('Displays information about a command')
            ->addArgument(new Argument(
                'command',
                ArgumentTypes::OPTIONAL,
                'The command to get help with'
            ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response): ?int
    {
        if ($this->command === null) {
            $response->writeln("<comment>Pass in the name of the command you'd like help with</comment>");
        } else {
            $descriptionText = 'No description';
            $helpText = '';

            if ($this->command->getDescription() !== '') {
                $descriptionText = $this->command->getDescription();
            }

            if ($this->command->getHelpText() !== '') {
                $helpText = PHP_EOL . '<comment>Help:</comment>' . PHP_EOL . '  ' . $this->command->getHelpText();
            }

            // Compile the template
            $compiledTemplate = str_replace(
                ['{{command}}', '{{description}}', '{{name}}', '{{arguments}}', '{{options}}', '{{helpText}}'],
                [
                    $this->commandFormatter->format($this->command),
                    $descriptionText,
                    $this->command->getName(),
                    $this->getArgumentText(),
                    $this->getOptionText(),
                    $helpText
                ],
                self::$template
            );
            $response->writeln($compiledTemplate);
        }

        return null;
    }

    /**
     * Converts the command arguments to text
     *
     * @return string The arguments as text
     */
    private function getArgumentText(): string
    {
        if (count($this->command->getArguments()) === 0) {
            return '  No arguments';
        }

        $argumentTexts = [];

        foreach ($this->command->getArguments() as $argument) {
            $argumentTexts[] = [$argument->getName(), $argument->getDescription()];
        }

        return $this->paddingFormatter->format($argumentTexts, function ($row) {
            return "  <info>{$row[0]}</info> - {$row[1]}";
        });
    }

    /**
     * Gets the option names as a formatted string
     *
     * @param Option $option The option to convert to text
     * @return string The option names as text
     */
    private function getOptionNames(Option $option): string
    {
        $optionNames = "--{$option->getName()}";

        if ($option->getShortName() !== null) {
            $optionNames .= "|-{$option->getShortName()}";
        }

        return $optionNames;
    }

    /**
     * Gets the options as text
     *
     * @return string The options as text
     */
    private function getOptionText(): string
    {
        if (count($this->command->getOptions()) === 0) {
            return '  No options';
        }

        $optionTexts = [];

        foreach ($this->command->getOptions() as $option) {
            $optionTexts[] = [$this->getOptionNames($option), $option->getDescription()];
        }

        return $this->paddingFormatter->format($optionTexts, function ($row) {
            return "  <info>{$row[0]}</info> - {$row[1]}";
        });
    }
}
