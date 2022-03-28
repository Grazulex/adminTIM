<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class InvoiceTolateAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'invoice-tolate';
    protected $baseRouteName = 'invoice_tolate';
    
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'asc', 
        '_sort_by' => 'term' 
    );     
        
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere(
            $query->expr()->eq($query->getRootAliases()[0] . '.status', ':status')
        )
        ->andWhere(
            $query->expr()->lt($query->getRootAliases()[0] . '.term', ':now')
        )                ;
        $query->setParameters(array('status'=>'1','now'=> New \DateTime()));
        return $query;
    }
    
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General',array(
                        'class'       => 'col-md-6'))    
                ->add('customer')
                ->add('reference')
                ->add('term','sonata_type_date_picker') 
            ->end()
            ->with('Discount',array(
                        'class'       => 'col-md-6'))     
                ->add('discount') 
                ->add('discount_type',
                'sonata_type_choice_field_mask', array('choices' => array(
                '%' => '%',
                '€' => '€'
                )))                      
                ->add('discount_comment')  
            ->end()  
            ->with('Lines')    
                ->add('lines', 'sonata_type_collection', array(
                    'required' => false
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable'  => 'position',
                )) 
            ->end()    
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'))     
            ->add('customer.company.name')
            ->add('customer.firstname')
            ->add('customer.lastname')
            ->add('invoice_number','doctrine_orm_number_range')     
            ->add('total_with_discount','doctrine_orm_number')                 
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        
        $listMapper
            ->add('id','text')
            ->add('term','date',array(
            'format' => 'd-m-Y'
            ))      
            ->add('customer.company.name')
            ->add('customer.name')
            ->add('invoice_number','text')              
            ->add('total_with_discount','currency',array('currency'=>'EUR','locale' => 'fr')) 
            ->add('reminders')        
            ->add('_action', 'actions', array(
            'actions' => array(
                'edit' => array(),
                'Reminder' => array(
                    'template' => 'AppBundle:Invoice:list__action_reminder.html.twig'
                ),                
                'Paid' => array(
                    'template' => 'AppBundle:Invoice:list__action_paid.html.twig'
                ),
                'Print' => array(
                    'template' => 'AppBundle:Invoice:list__action_print.html.twig'
                ),
                'Send' => array(
                    'template' => 'AppBundle:Invoice:list__action_send.html.twig'
                ),
                'Erase' => array(
                    'template' => 'AppBundle:Invoice:list__action_erase.html.twig',
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
        $collection->add('reminder', $this->getRouterIdParameter().'/reminder');
        $collection->add('print', $this->getRouterIdParameter().'/print');
        $collection->add('send', $this->getRouterIdParameter().'/send');
        $collection->add('erase', $this->getRouterIdParameter().'/erase');
    }   
    
    public function prePersist($object)
    {
        $total = 0;
        foreach ($object->getLines() as $line) {
            $line->setInvoice($object);
            $total=$total+$line->getTotalPrice();
        }
        $total_with_discount = $total;
        if ($object->getDiscount() > 0) {
            if ($object->getDiscountType() === '€') 
            {
                $total_with_discount = $total_with_discount - $object->getDiscount();
            } else {
                $total_with_discount = $total_with_discount- ($total_with_discount * ($object->getDiscount()/100));
            }
        }
        $object->setTotal($total);
        $object->setTotalWithDiscount($total_with_discount);
        
    }

    public function preUpdate($object)
    {
        $total = 0;
        foreach ($object->getLines() as $line) {
            $line->setInvoice($object);
            $total=$total+$line->getTotalPrice();
        }
        $total_with_discount = $total;
        if ($object->getDiscount() > 0) {
            if ($object->getDiscountType() === '€') 
            {
                $total_with_discount = $total_with_discount - $object->getDiscount();
            } else {
                $total_with_discount = $total_with_discount- ($total_with_discount * ($object->getDiscount()/100));
            }
        }
        $object->setTotal($total);
        $object->setTotalWithDiscount($total_with_discount);
    }      
}