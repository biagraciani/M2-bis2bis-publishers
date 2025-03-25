<?php
/**
 * @category   Bis2bis
 * @package    Bis2bis_Publishers
 * @author     Beatriz Graciani Sborz
 * @copyright  Copyright (c) 2025
 */

namespace Bis2bis\Publishers\Console\Command;

use Bis2bis\Publishers\Model\PublisherFactory;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\DirectoryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportPublishers
 *
 * CLI command to import publishers from a CSV file.
 */
class ImportPublishers extends Command
{
    /**
     * @var PublisherFactory
     */
    private PublisherFactory $publisherFactory;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var State
     */
    private State $state;

    /**
     * @var FileDriver
     */
    private FileDriver $fileDriver;

    /**
     * Constructor.
     *
     * @param PublisherFactory $publisherFactory
     * @param Filesystem $filesystem
     * @param State $state
     * @param FileDriver $fileDriver
     * @param string|null $name
     */
    public function __construct(
        PublisherFactory $publisherFactory,
        Filesystem $filesystem,
        State $state,
        FileDriver $fileDriver,
        string $name = null
    ) {
        $this->publisherFactory = $publisherFactory;
        $this->filesystem = $filesystem;
        $this->state = $state;
        $this->fileDriver = $fileDriver;
        parent::__construct($name);
    }

    /**
     * Configure the CLI command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('publisher:import')
            ->setDescription('Import publishers from a CSV file')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to the CSV file'
            );

        parent::configure();
    }

    /**
     * Execute the import process.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            $this->logger->info('Area code already set: ' . $e->getMessage());
        }

        $csvFile = $input->getArgument('file');

        if (!$this->fileDriver->isExists($csvFile)) {
            $output->writeln("<error>File not found: {$csvFile}</error>");
            return Cli::RETURN_FAILURE;
        }

        $handle = $this->fileDriver->fileOpen($csvFile, 'r');
        if (!$handle) {
            $output->writeln("<error>Unable to open file: {$csvFile}</error>");
            return Cli::RETURN_FAILURE;
        }

        $header = $this->fileDriver->fileGetCsv($handle);
        if (!$header) {
            $output->writeln('<error>CSV file is empty or invalid.</error>');
            $this->fileDriver->fileClose($handle);
            return Cli::RETURN_FAILURE;
        }

        $importCount = 0;

        while ($row = $this->fileDriver->fileGetCsv($handle)) {
            $data = array_combine($header, $row);
            if (!$data) {
                continue;
            }

            $publisher = $this->publisherFactory->create();
            $publisher->setData([
                'name'    => $data['name'] ?? null,
                'status'  => $data['status'] ?? 1,
                'address' => $data['address'] ?? null,
                'logo'    => $data['logo'] ?? null,
                'cnpj'    => $data['cnpj'] ?? null,
            ]);

            try {
                $publisher->save();
                $importCount++;
            } catch (\Exception $e) {
                $output->writeln('<error>Error importing publisher: ' . $e->getMessage() . '</error>');
            }
        }

        $this->fileDriver->fileClose($handle);

        $output->writeln("<info>Imported {$importCount} publishers successfully.</info>");
        return Cli::RETURN_SUCCESS;
    }
}
