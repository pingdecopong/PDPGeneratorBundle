<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fhirashima
 * Date: 13/07/18
 * Time: 10:11
 * To change this template use File | Settings | File Templates.
 */

namespace pingdecopong\PDPGeneratorBundle\Generator;


use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class DoctrineCrudPDPGenerator extends DoctrineCrudGenerator
{
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);
    }

    protected function generateControllerClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile('crud/controller.php.twig', $target, array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'format'            => $this->format,
            'fields'            => $this->metadata->fieldMappings,
            'associations'      => $this->getAssociationMetadata($this->metadata),
        ));
    }

    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);

        $dir = sprintf('%s/Resources/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));

        //delete
        if (in_array('delete', $this->actions)) {
            $this->generateDeleteView($dir);
        }

        //layout
        $this->generateLayoutView($dir);

/*
        //test
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        $cme = new ClassMetadataExporter();
        $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
        $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $entity).'.orm.'.$format;

        if (file_exists($mappingPath)) {
            throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
        }

//        $mappingCode = $exporter->exportClassMetadata($metadata);
        $entityGenerator->setGenerateAnnotations(false);
//        $metadata->setTableName(str_replace('\\', '/', $entity).'SearchModel');
        $bundleNameSpace = $bundle->getNamespace();
        $metadata->name = $bundleNameSpace.'\\Form\\'.$entity.'SearchModel';
        $metadata->namespace = $bundleNameSpace.'\\Form\\'.$entity.'SearchModel';

//        $metadata->rootEntityName .= 'SearchModel';
        $entityCode = $entityGenerator->generateEntityClass($metadata);

        $entityPath = $bundle->getPath().'/Form/'.str_replace('\\', '/', $entity).'SearchModel.php';
        if (!file_exists($entityPath)) {
//            throw new \RuntimeException(sprintf('Entity "%s" already exists.'));
            $this->filesystem->mkdir(dirname($entityPath));
            file_put_contents($entityPath, $entityCode);
        }
*/


/*
        $entityGenerator->generateEntityClass($metadata);
        $entityPath = $bundle->getPath().'/Form/'.str_replace('\\', '/', $entity).'SearchModel.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity aaaa already exists.'));
        }
        $entityCode = $entityGenerator->generateEntityClass($metadata);
        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);
*/
    }

    protected function generateIndexView($dir)
    {
        $this->renderFile('crud/views/index.html.twig.twig', $dir.'/index.html.twig', array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'record_actions'    => $this->getRecordActions(),
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'associations'      => $this->getAssociationMetadata($this->metadata),
        ));
    }

    protected function generateShowView($dir)
    {
        $this->renderFile('crud/views/show.html.twig.twig', $dir.'/show.html.twig', array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'associations'      => $this->getAssociationMetadata($this->metadata),
        ));
    }

    protected function generateDeleteView($dir)
    {
        $this->renderFile('crud/views/delete.html.twig.twig', $dir.'/delete.html.twig', array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'associations'      => $this->getAssociationMetadata($this->metadata),
        ));
    }

    protected function generateLayoutView($dir)
    {
        $this->renderFile('crud/views/layout.html.twig.twig', $dir.'/layout.html.twig', array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
        ));
    }

    protected function generateNewView($dir)
    {
        $this->renderFile('crud/views/new.html.twig.twig', $dir.'/new.html.twig', array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'actions'           => $this->actions,
            'fields'            => $this->metadata->fieldMappings,
            'associations'      => $this->getAssociationMetadata($this->metadata),
        ));
    }

    protected function generateEditView($dir)
    {
        $this->renderFile('crud/views/edit.html.twig.twig', $dir.'/edit.html.twig', array(
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'bundle'            => $this->bundle->getName(),
            'actions'           => $this->actions,
            'fields'            => $this->metadata->fieldMappings,
            'associations'      => $this->getAssociationMetadata($this->metadata),
        ));
    }

    private function getAssociationMetadata(ClassMetadataInfo $metadata)
    {
        $fields = array();

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== ClassMetadataInfo::ONE_TO_MANY) {
                $fields[$fieldName] = $fieldName;
            }
        }

        return $fields;
    }
}