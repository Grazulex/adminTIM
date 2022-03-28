<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class LocationAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper    
            ->add('firstname')
            ->add('lastname')
            ->add('vat')                 
            ->add('adress1') 
            ->add('adress2') 
            ->add('adress3') 
            ->add('zip') 
            ->add('city')
            ->add('country')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('firstname')
            ->add('lastname')
            ->add('vat') 
            ->add('adress1') 
            ->add('adress2') 
            ->add('adress3') 
            ->add('zip') 
            ->add('city')
            ->add('country')               
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        
        $listMapper
            ->addIdentifier('id')
            ->add('firstname')
            ->add('lastname') 
            ->add('vat')     
            ->add('adress1') 
            ->add('adress2') 
            ->add('adress3') 
            ->add('zip') 
            ->add('city')
            ->add('country')
                
        ;
    }
 
}