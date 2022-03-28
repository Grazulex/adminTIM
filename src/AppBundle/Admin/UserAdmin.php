<?php

namespace AppBundle\Admin;

use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;
use Sonata\AdminBundle\Admin\Admin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends SonataUserAdmin 
{
    
    protected $translationDomain = 'AppBundle';
    /**
        * {@inheritdoc}
        */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->tab('User')
                ->with('General')
                    ->add('company')                
                ->end()
                ->with('Location')
                    ->add('locations', 'sonata_type_collection', array(
                        'required' => false
                    ), array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable'  => 'position',
                    ))               
                ->end()                
            ->end()                
            ->remove('website')
            ->remove('biography')
            ->remove('dateOfBirth')   
            ->remove('facebookUid')
            ->remove('facebookName')
            ->remove('twitterUid')
            ->remove('twitterName')
            ->remove('gplusUid')
            ->remove('gplusName') 
            ->remove('gplusName')  
            ->remove('plainPassword')
        ;
    }
    
    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        parent::configureDatagridFilters($datagridMapper);
        
        $datagridMapper
            ->remove('email')      
            ->add('firstname')
            ->add('lastname')
            ->add('company.name')     
        ;
    }    
    
    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->add('company.name')     
            ->add('firstname')
            ->add('lastname') 
        ;
        parent::configureListFields($listMapper);
        $listMapper->remove('email');   
        /*$listMapper->add('_action', 'actions', array(
            'actions' => array(
                'Invoiced' => array(
                    'template' => 'AppBundle:User:list__action_invoiced.html.twig'
                ),
                'Credited' => array(
                    'template' => 'AppBundle:user:list__action_credited.html.twig'
                )                
            )
            ));     
         * 
         */    
    }   
    
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        
        $collection->add('invoiced', $this->getRouterIdParameter().'/invoiced');
        $collection->add('credited', $this->getRouterIdParameter().'/credited');
    }   
           
}