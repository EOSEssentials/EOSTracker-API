<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('eostracker:stats')
            ->setDescription('Generate Stats');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine.orm.entity_manager");
        $cache = $this->getContainer()->get('cache.app');

        // blocks
        $stmt = $em->getConnection()->prepare('SELECT count(id) FROM blocks');
        $stmt->execute();
        $blocks = $stmt->fetchColumn();

        // tx
        $stmt = $em->getConnection()->prepare('SELECT count(id) FROM transactions');
        $stmt->execute();
        $tx = $stmt->fetchColumn();

        // accounts
        $stmt = $em->getConnection()->prepare('SELECT count(name) FROM accounts');
        $stmt->execute();
        $acc = $stmt->fetchColumn();

        // actions
        $stmt = $em->getConnection()->prepare('SELECT count(id) FROM actions');
        $stmt->execute();
        $actions = $stmt->fetchColumn();

        $result = $cache->getItem('stats.action');
        $result->set([$blocks, $tx, $acc, $actions]);
        $cache->save($result);
    }
}
