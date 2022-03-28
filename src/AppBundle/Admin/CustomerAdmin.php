<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\Admin as Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CustomerAdmin extends AbstractAdmin
{
    
    protected $translationDomain = 'AppBundle';

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'id'
    );

    /**
        * {@inheritdoc}
        */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->with('General',array(
                'class'       => 'col-md-6'))
                ->add('company')
                ->add('email')
                ->add('firstname')
                ->add('lastname')
                ->add('locale')
            ->end()
            ->with('Datas',array(
                'class'       => 'col-md-6'))
                ->add('vat')
                ->add('firstname_location',null,array(
                    'label' => 'Firstname Invoice')
                )
                ->add('lastname_location',null,array(
                        'label' => 'Lastname Invoice')
                )
                ->add('adress1')
                ->add('adress2')
                ->add('adress3')
                ->add('zip')
                ->add('city')
                ->add('country')
            ->end()
        ;
    }
    
    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email')
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
            ->addIdentifier('id','text')
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('company.name')
            ->add('vat')
            ->add('adress1')
            ->add('zip')
            ->add('city')
        ;

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

        $collection->add('invoiced', $this->getRouterIdParameter().'/invoiced');
        $collection->add('credited', $this->getRouterIdParameter().'/credited');
    }
           
}