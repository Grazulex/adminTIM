<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use A2lix\TranslationFormBundle\Form\Type\GedmoTranslationsType;

class ItemCategoryAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {    
        $formMapper
            ->add('parent') 
            ->add('name')
            ->add('translations', GedmoTranslationsType::class, array('translatable_class' => "AppBundle\Entity\ItemCategory"))   
            ->add('reference')
            ->add('taxrate')     
            ->add('useForCash') 
            ->add('useForLogistic')                   
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('reference')
            ->add('taxrate') 
            ->add('useForCash') 
            ->add('useForLogistic')     
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        
        $listMapper
            ->addIdentifier('id')
            ->add('name')  
            ->add('parent.name')      
            ->add('reference','text')
            ->add('taxrate','percent')     
            ->add('useForCash',null,array('editable' => true)) 
            ->add('useForLogistic',null,array('editable' => true))                   
        ;
    }
}