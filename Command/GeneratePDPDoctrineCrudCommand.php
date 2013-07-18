<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fhirashima
 * Date: 13/07/18
 * Time: 9:56
 * To change this template use File | Settings | File Templates.
 */

namespace pingdecopong\PDPGeneratorBundle\Command;


use pingdecopong\PDPGeneratorBundle\Generator\DoctrineCrudPDPGenerator;
use pingdecopong\PDPGeneratorBundle\Generator\DoctrineSearchFormGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePDPDoctrineCrudCommand extends GenerateDoctrineCrudCommand
{
    private $skeletonName;
    private $searchFormGenerator;
    protected function configure()
    {
        parent::configure();
        $options = $this->getDefinition()->addOption(new InputOption('skeleton', '', InputOption::VALUE_OPTIONAL, 'skeleton'));
//        $options[] = new InputOption('skeleton', '', InputOption::VALUE_REQUIRED, 'skeleton');
        $this
            ->setDescription('Generates a CRUD based on a Doctrine entity by PDP')
            ->setName('pingdecopong:generate:crud')
            ->setAliases(array())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->skeletonName = $input->getOption('skeleton');
        if(!empty($this->skeletonName))
        {
            $output->writeln(sprintf('skeleton "<info>%s</info>"', $this->skeletonName));
        }
        parent::execute($input, $output);

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);
        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);
        //SearchForm
        $this->generateSearchForm($bundle, $entity, $metadata);
        $output->writeln('Generating the SearchForm code: <info>OK</info>');

    }

    protected function generateSearchForm($bundle, $entity, $metadata)
    {
        try {
            $this->getSearchFormGenerator($bundle)->generate($bundle, $entity, $metadata[0]);
        } catch (\RuntimeException $e ) {
            // form already exists
        }
    }

    protected function getSearchFormGenerator($bundle = null)
    {
        if (null === $this->searchFormGenerator) {
            $this->searchFormGenerator = new DoctrineSearchFormGenerator($this->getContainer()->get('filesystem'));
            $this->searchFormGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->searchFormGenerator;
    }

    protected function createGenerator($bundle = null)
    {
        return new DoctrineCrudPDPGenerator($this->getContainer()->get('filesystem'));
    }



    protected function getSkeletonDirs($bundle = null)
    {
        $skeletonDirs = array();

        if(!empty($this->skeletonName))
        {

            if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/PDPGeneratorBundle/skeleton/'.$this->skeletonName)) {
                $skeletonDirs[] = $dir;
            }

            if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/PDPGeneratorBundle/skeleton/'.$this->skeletonName)) {
                $skeletonDirs[] = $dir;
            }

            $skeletonDirs[] = __DIR__.'/../Resources/skeleton/'.$this->skeletonName;
            $skeletonDirs[] = __DIR__.'/../Resources';

        }else{

            if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/PDPGeneratorBundle/skeleton/default')) {
                $skeletonDirs[] = $dir;
            }

            if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/PDPGeneratorBundle/skeleton/default')) {
                $skeletonDirs[] = $dir;
            }

            $skeletonDirs[] = __DIR__.'/../Resources/skeleton/default';
            $skeletonDirs[] = __DIR__.'/../Resources';
        }

        return $skeletonDirs;
    }
}