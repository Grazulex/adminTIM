<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class LogisticLineAdmin extends AbstractAdmin
{
    
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $itemsQuery = $this->modelManager

        ->getEntityManager('AppBundle\Entity\Item')
        ->createQueryBuilder()
        ->select('i')
        ->from('AppBundle:Item','i');
        if ($this->getRoot()->getSubject()->getCategory()) {
            $itemsQuery->join('i.category','c','WHERE', 'c.useForLogistic = true AND c.id='.$this->getRoot()->getSubject()->getCategory()->getId());
        }
        $itemsQuery->orderBy('i.name');
        
        $formMapper
            ->add('item', 'sonata_type_model',
                array(
                    'class' => 'AppBundle\Entity\Item',
                    'property' => 'name',
                    'query' => $itemsQuery,
                    'btn_add' => false
                ))      
            ->add('quantity') 
            ->add('comment')   
            ->add('controle') 
            ->add('checked')     
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')                 
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')     
        ;
    }
}