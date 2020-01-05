<?php

namespace Jycamier\ConsoleCommandProcessBundle\Task;

use CleverAge\ProcessBundle\Model\AbstractConfigurableTask;
use CleverAge\ProcessBundle\Model\BlockingTaskInterface;
use CleverAge\ProcessBundle\Model\ProcessState;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsoleApplicationTask extends AbstractConfigurableTask implements BlockingTaskInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @inheritDoc
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     * @throws \Exception
     */
    public function execute(ProcessState $state)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $application->setCatchExceptions($this->getOption($state, 'command_catch_exception'));

        $output = new BufferedOutput();
        $exitCode = $application->run(new ArrayInput($state->getInput()), $output);

        if ($this->getOption($state, 'debug_mode')) {
            dump(
                array_merge(
                    $state->getInput(),
                    [
                        'exit_code' => $exitCode,
                    ]
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function proceed(ProcessState $state)
    {
        $state->setOutput([]); //fixme : find something to do after
    }

    /**
     * @inheritDoc
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('debug_mode', false);
        $resolver->setAllowedTypes('debug_mode', 'boolean');
        $resolver->setDefault('command_catch_exception', false);
        $resolver->setAllowedTypes('command_catch_exception', 'boolean');
    }
}
