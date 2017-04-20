<?php

namespace Room204\AddBrands\Console\Command;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Room204\AddBrands\Helper\Data;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;


/**
 * Created by PhpStorm.
 * User: markcyrulik
 * Date: 11/1/16
 * Time: 6:58 PM
 */
class AddBrands extends Command
{
    protected $_attributeRepository;

    protected $_registry;

    protected $_state;

    protected $_optionCollection;

    protected $_helper;



    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        Registry $registry,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection,
        Data $helper
    ) {
        $this->_attributeRepository = $attributeRepository;
        $this->_registry = $registry;
        $this->_state = $state;
        $this->_optionCollection = $optionCollection;
        $this->_helper = $helper;

        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('room204:addbrands')
            ->setDescription('Add new Brands')
            ->addArgument('file', InputArgument::REQUIRED, 'File to Import From');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_registry->register('isSecureArea', true);
        $this->_state->setAreaCode('adminhtml');

        $output->writeln('Adding Brands');

        if (!file_exists($input->getArgument('file'))) {
            throw new LocalizedException(__('Input file does not exist.'));
        }

        $brandFileContents = file_get_contents($input->getArgument('file'));

        $brandFileContentsAray = explode("\n", $brandFileContents);


        // @todo: remove after options are saved...
        //$brandFileContentsAray = ["Holiday Editions"];

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute\Interceptor $manufacturer */
        $manufacturer = $this->_attributeRepository->get(\Magento\Catalog\Model\Product::ENTITY, 'manufacturer');

        $manufacturerAttributeID = $manufacturer->getId();

        $count = 1;
        foreach ($brandFileContentsAray as $newBrand) {
            $id = $this->_helper->createOrGetId('manufacturer', trim($newBrand));
            $output->writeln(str_pad($count, "0", 5, STR_PAD_LEFT).": Adding Brand: ".trim($newBrand)." => ID: ".$id);
            $count ++;
        }

        $output->writeln('DONE');
    }


}