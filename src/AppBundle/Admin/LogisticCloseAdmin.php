<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use AppBundle\Entity\InvoiceLine;

class LogisticCloseAdmin extends AbstractAdmin
{
    
    protected $baseRoutePattern = 'logistic-close';
    protected $baseRouteName = 'logistic_close';
 
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', 
        '_sort_by' => 'created' 
    );     
    
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere($query->getRootAliases()[0] . '.checked is not NULL');
        return $query;
    }    
    
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $customerQuery = $this->modelManager
        ->getEntityManager('AppBundle\Entity\Customer')
        ->createQuery(
            'SELECT c
             FROM AppBundle:Customer c
             ORDER BY c.lastname ASC'
        );
     
        $categoryQuery = $this->modelManager
        ->getEntityManager('AppBundle\Entity\ItemCategory')
        ->createQuery(
            'SELECT c
             FROM AppBundle:ItemCategory c
             WHERE c.useForLogistic = true
             ORDER BY c.name ASC'
        );        
        
        $formMapper
            ->with('General',array(
                        'class'       => 'col-md-6'))    
                ->add('customer', 'sonata_type_model',
                array(
                    'class' => 'AppBundle\Entity\Customer',
                    'property' => 'fullname',
                    'query' => $customerQuery
                ))  
                ->add('category', 'sonata_type_model',
                array(
                    'class' => 'AppBundle\Entity\ItemCategory',
                    'property' => 'name',
                    'query' => $categoryQuery,
                    'btn_add' => false
                )) 
                ->add('invoiced')
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
            ->add('category.name')  
            ->add('customer.company.name')
            ->add('customer.firstname')
            ->add('customer.lastname')
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
			->add('checked','date',array(
            'format' => 'd-m-Y'
            ))    
            ->add('category.name')      
            ->add('customer.company.name')
            ->add('customer.fullname')
            ->add('totalLines')
            ->add('lines')    
            ->add('invoiced')       
            ->add('_action', 'actions', array(
            'actions' => array(
                'show' => array()
            )
            ))                  
        ;
    }
	
	public function configureShowFields(ShowMapper $showMapper)
    {
		$showMapper
			->with('General',array(
                        'class'       => 'col-md-6'))    
                ->add('customer', 'sonata_type_model',
                array(
                    'class' => 'AppBundle\Entity\Customer',
                    'property' => 'fullname',
                    'query' => $customerQuery
                ))  
                ->add('category', 'sonata_type_model',
                array(
                    'class' => 'AppBundle\Entity\ItemCategory',
                    'property' => 'name',
                    'query' => $categoryQuery,
                    'btn_add' => false
                )) 
                ->add('invoiced')
            ->end()
            ->with('Lines')    
                ->add('lines', 'sonata_type_collection', array(
                    'required' => false
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable'  => 'position',
                )) 
            ->end(); 
	}
    
    public function prePersist($object)
    {
        $allcheck = true;
        foreach ($object->getLines() as $line) {
            $line->setLogistic($object);
            if ($line->getChecked() == null){
               $allcheck = false; 
            }
        }
        if ($allcheck == true) {
            $em = $this->modelManager->getEntityManager('AppBundle\Entity\Invoice');
            $object->setChecked(new \DateTime());
            $invoice = $em->getRepository('AppBundle\Entity\Invoice')->LoadorCreateInvoice($object->getCustomer());
            foreach ($object->getLines() as $line) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine->setInvoice($invoice);
                $invoiceLine->setName($line->getItem()->getName());
                $invoiceLine->setQuantity($line->getQuantity());
                $em->persist($invoiceLine);
                $em->flush();
            }
        }
    }

    public function preUpdate($object)
    {
        $allcheck = true;
        foreach ($object->getLines() as $line) {
            $line->setLogistic($object);
            if ($line->getChecked() == null){
               $allcheck = false; 
            }
        }
        if ($allcheck == true) {
            $em = $this->modelManager->getEntityManager('AppBundle\Entity\Invoice');
            $object->setChecked(new \DateTime());
            $invoice = $em->getRepository('AppBundle\Entity\Invoice')->LoadorCreateInvoice($object->getCustomer());
            foreach ($object->getLines() as $line) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine->setInvoice($invoice);
                $invoiceLine->setName($line->getItem()->getName());
                $invoiceLine->setQuantity($line->getQuantity());
                $em->persist($invoiceLine);
                $em->flush();
            }
        }
    }    
}