<?php

namespace MD\Bundle\ServiceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FontAsseticCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('assetic:dump:font')
                ->setDescription('Assetic Dump Font')
//                ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
//                ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $assets = $this->getContainer()->getParameter('assets');

        foreach ($assets as $asset) {
            $src = $asset['inputs'];
            $dest = $asset['output'];
            shell_exec("cp -r $src $dest");

            $scranDirSrc = scandir(str_replace("*", "", $src));
            for ($i = 2; $i < count($scranDirSrc) - 2; $i++) {
                $output->writeln("<info>[file+]</info> " . $dest . $scranDirSrc[$i]);
            }
        }
    }

}
