<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CreditAllAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'credit-all';   
    protected $baseRouteName = 'credit_all';
    
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', 
        '_sort_by' => 'created' 
    ); 

    public function getBatchActions()
    {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        if (
          $this->hasRoute('edit') && $this->isGranted('EDIT')
        ) {
            $actions['pdf'] = array(
                'label' => 'PDF',
                'translation_domain' => 'SonataAdminBundle',
                'ask_confirmation' => true
            );

        }

        return $actions;
    }     

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'))     
            ->add('customer.company.name')
            ->add('customer.firstname')
            ->add('credit_number','doctrine_orm_number_range')     
            ->add('total','doctrine_orm_number')  
            ->add('status', 'doctrine_orm_choice', array(),
             'choice', array('choices' => array(
                '0' => 'Draft',
                '1' => 'Open',
                '2' => 'Close'
            ),'multiple' => true))                 
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        
        $listMapper
            ->add('id','text')
            ->add('created','date',array(
            'format' => 'd-m-Y'
            ))      
            ->add('customer.company.name')
            ->add('customer.name')
            ->add('credit_number','text')     
            ->add('total','currency',array('currency'=>'EUR','locale' => 'fr')) 
            ->add('status','choice',array('choices' => array(
                '0' => 'Draft',
                '1' => 'Open',
                '2' => 'Close'
            ))) 
            ->add('_action', 'actions', array(
            'actions' => array(
                'Print' => array(
                    'template' => 'AppBundle:Credit:list__action_print.html.twig'
                ),   
                'Send' => array(
                    'template' => 'AppBundle:Credit:list__action_send.html.twig'
                ),     
                'Erase' => array(
                    'template' => 'AppBundle:Credit:list__action_erase.html.twig',
                    'ask_confirmation' => true
                )                 
            )
            ))                 
        ;
    }
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete'); 
        $collection->add('print', $this->getRouterIdParameter().'/print');
        $collection->add('send', $this->getRouterIdParameter().'/send');
        $collection->add('erase', $this->getRouterIdParameter().'/erase');        
    }     
}