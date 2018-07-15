<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MissedBlocksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('eostracker:missed-blocks')
            ->setDescription('Check Missed blocks')
            ->addOption('scheduling', '', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scheduling = $input->getOption('scheduling') ?? 1;
        $em = $this->getContainer()->get("doctrine.orm.entity_manager");

        $output->writeln('scheduling: '.$scheduling);


        $sql = "SELECT block_number, producer FROM blocks WHERE version = $scheduling ORDER BY block_number ASC LIMIT 1";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $block = $stmt->fetchAll();
        $firstBlock = $block[0]['block_number'];

        $sql = "SELECT block_number FROM blocks WHERE version = $scheduling ORDER BY block_number DESC LIMIT 1";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $lastBlock = $stmt->fetchColumn();

        $sql = "SELECT new_producers FROM blocks WHERE block_number < $firstBlock AND version = ($scheduling-1) AND new_producers IS NOT NULL ORDER BY block_number DESC LIMIT 1";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $listProducers = json_decode($stmt->fetchColumn(), true);
        $orderedProducers = [];

        foreach ($listProducers as $producer) {
            $orderedProducers[] = $producer['producer_name'];
        }

        $output->writeln('Blocks: '.($lastBlock - $firstBlock));
        $output->writeln('First Block: '.$firstBlock);
        $output->writeln('Last Block: '.$lastBlock);
        $output->writeln('List Producers: '.json_encode($orderedProducers));

        $blocksPerQuery = 10000;
        $blocksPerRound = 12;
        $currentBlockInRoundForBP = 1;
        $currentBP = $block[0]['producer'];
        $currentBlock = $firstBlock - 2;

        $missedBlocks = [];

        while ($currentBlock < $lastBlock) {
            $sql = "SELECT block_number, producer, version FROM blocks WHERE block_number BETWEEN $currentBlock AND ($currentBlock+$blocksPerQuery)  ORDER BY block_number ASC";
            $stmt = $em->getConnection()->prepare($sql);
            //$output->writeln($sql);
            $stmt->execute();
            $blocks = $stmt->fetchAll();

            foreach ($blocks as $block) {
                if ($block['version'] != $scheduling) {
                    continue;
                }

                if ($this->isCurrentRound($currentBlockInRoundForBP, $blocksPerRound)) {
                    if ($block['producer'] !== $currentBP) {
                        $blocksMissed = $blocksPerRound - $currentBlockInRoundForBP;
                        $output->writeln('Missed Blocks ('.$blocksMissed .') at: '.$block['block_number'] . ' for: '. $currentBP);
                        $missedBlocks[$currentBP] = isset($missedBlocks[$currentBP]) ? $missedBlocks[$currentBP] + $blocksMissed : $blocksMissed;

                        list($currentBP, $currentBlockInRoundForBP, $missedBlocks) = $this->startNewRound(
                            $output, $orderedProducers, $currentBP, $block, $missedBlocks, $blocksPerRound
                        );
                    } else {
                        $currentBlockInRoundForBP++;
                    }
                } else {
                    list($currentBP, $currentBlockInRoundForBP, $missedBlocks) = $this->startNewRound(
                        $output, $orderedProducers, $currentBP, $block, $missedBlocks, $blocksPerRound
                    );
                }
            }

            $currentBlock = $currentBlock + $blocksPerQuery + 1;
        }

        $output->writeln('Missed Blocks: '.json_encode($missedBlocks));
    }


    private function nextBP(array $orderedProducers, string $currentProducer): string
    {
        foreach ($orderedProducers as $key => $producer) {
            if ($currentProducer == $producer) {
                if (isset($orderedProducers[$key + 1])) {
                    return $orderedProducers[$key + 1];
                } else {
                    return $orderedProducers[0];
                }
            }
        }
    }

    private function isCurrentRound(int $currentBlockInRoundForBP, int $blocksPerRound): bool
    {
        return $currentBlockInRoundForBP < $blocksPerRound;
    }

    private function startNewRound($output, $orderedProducers, $currentBP, $block, $missedBlocks, $blocksPerRound): array
    {

        // $output->writeln('new Round: '. $block['producer']. ' ' .$block['block_number']);

        $nextBP = $this->nextBP($orderedProducers, $currentBP);
        if ($nextBP !== $block['producer']) {
            $output->writeln('Missed Round (12) at: '.$block['block_number'] . ' for: '. $nextBP);
            $missedBlocks[$nextBP] = isset($missedBlocks[$nextBP]) ? $missedBlocks[$nextBP] + $blocksPerRound : $blocksPerRound;
        }
        $currentBP = $block['producer'];
        $currentBlockInRoundForBP = 1;

        return [
            $currentBP,
            $currentBlockInRoundForBP,
            $missedBlocks,
        ];
    }
}
