<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CreditOpenAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'credit-open';   
    protected $baseRouteName = 'credit_open';
    
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', 
        '_sort_by' => 'created' 
    );     
    
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere(
            $query->expr()->eq($query->getRootAliases()[0] . '.status', ':status')
        );
        $query->setParameter('status', '1');
        return $query;
    }
    
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('customer')
            ->add('created','sonata_type_date_picker')
            ->add('lines', 'sonata_type_collection', array(
                'required' => false,
                'btn_add' => false,
                'type_options' => array('delete' => false)
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position'
            ))         
        ;
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
            ->add('_action', 'actions', array(
            'actions' => array(
                'edit' => array(),
                'Paid' => array(
                    'template' => 'AppBundle:Credit:list__action_paid.html.twig'
                ),
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
        $collection->add('paid', $this->getRouterIdParameter().'/paid');
        $collection->add('send', $this->getRouterIdParameter().'/send');
        $collection->add('print', $this->getRouterIdParameter().'/print');
        $collection->add('erase', $this->getRouterIdParameter().'/erase');
    }     
}