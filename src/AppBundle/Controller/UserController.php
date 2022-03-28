<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\Credit;



class UserController extends Controller
{    
    public function creditedAction($id = null, Request $request = null)
    {
        if ( $request == null ) {
            $request = $this->getRequest();
        }
        $id = $request->get($this->admin->getIdParameter());

        $user = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $credit = new Credit();
        $credit->setUser($user);
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($credit);
        $em->flush();

        $this->addFlash('sonata_flash_success', 'Credit created');

        return new RedirectResponse($this->generateUrl('credit_draft_edit',array('id'=>$credit->getId())));         
    }    
    
    public function invoicedAction($id = null, Request $request = null)
    {
        if ( $request == null ) {
            $request = $this->getRequest();
        }
        $id = $request->get($this->admin->getIdParameter());

        $user = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $invoice = new Invoice();
        $invoice->setUser($user);
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($invoice);
        $em->flush();

        $this->addFlash('sonata_flash_success', 'Invoice created');

        return new RedirectResponse($this->generateUrl('invoice_draft_edit',array('id'=>$invoice->getId())));         
    }
}