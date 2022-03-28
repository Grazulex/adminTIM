<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CashAdmin extends AbstractAdmin
{
    
    public function getBatchActions()
    {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        if (
          $this->hasRoute('edit') && $this->isGranted('EDIT')
        ) {
            $actions['repport'] = array(
                'label' => 'Repport',
                'translation_domain' => 'AppBundle',
                'ask_confirmation' => false
            );

        }

        return $actions;
    }     
    
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        
        $categoriesQuery = $this->modelManager

        ->getEntityManager('AppBundle\Entity\Item')
        ->createQuery(
            'SELECT ic
             FROM AppBundle:ItemCategory ic
             WHERE ic.useForCash = true
             ORDER BY ic.name ASC'
        );
        
        $formMapper
            ->add('created','sonata_type_date_picker')    
            ->add('company')
            ->add('category', 'sonata_type_model',
                array(
                    'class' => 'AppBundle\Entity\ItemCategory',
                    'property' => 'name',
                    'query' => $categoriesQuery,
                    'btn_add' => false
                ))   
            ->add('total')    
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper               
            ->add('created', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'))                 
            ->add('company')
            ->add('category')
            ->add('total')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        
        $listMapper
            ->addIdentifier('id','text')
            ->add('created','date',array(
            'format' => 'd-m-Y'
            ))     
            ->add('company')    
            ->add('category')
            ->add('total','currency',array('currency'=>'EUR','locale' => 'fr'))
        ;
    }
}