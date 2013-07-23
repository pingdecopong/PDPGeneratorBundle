<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fhirashima
 * Date: 13/07/18
 * Time: 9:56
 * To change this template use File | Settings | File Templates.
 */

namespace pingdecopong\PDPGeneratorBundle\Command;


use Doctrine\ORM\Tools\EntityGenerator;
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

        //SearchModel
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

//        $cme = new ClassMetadataExporter();
//        $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
//        $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $entity).'.orm.'.$format;

//        if (file_exists($mappingPath)) {
//            throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
//        }

        //SearchModel
        $entityGenerator->setGenerateAnnotations(false);
        $bundleNameSpace = $bundle->getNamespace();
        $metadata[0]->name = $bundleNameSpace.'\\Form\\'.$entity.'SearchModel';
        $metadata[0]->namespace = $bundleNameSpace.'\\Form\\'.$entity.'SearchModel';

        $entityCode = $entityGenerator->generateEntityClass($metadata[0]);

        $entityPath = $bundle->getPath().'/Form/'.str_replace('\\', '/', $entity).'SearchModel.php';
        if (!file_exists($entityPath)) {
//            throw new \RuntimeException(sprintf('Entity "%s" already exists.'));
            $this->getContainer()->get('filesystem')->mkdir(dirname($entityPath));
            file_put_contents($entityPath, $entityCode);
            $output->writeln('Generating the SearchModel code: <info>OK</info>');
        }else{
            $output->writeln('SearchModel already exists.');
        }

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