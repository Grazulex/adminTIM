<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Swift_Attachment;


use AppBundle\Entity\Invoice;
use AppBundle\Entity\Credit;
use AppBundle\Entity\Reminder;
use AppBundle\Entity\Erase;


class CreditController extends Controller
{
    public function paidAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $object->setStatus(Invoice::STATUS_CLOSED);
        $object->setPaiement(new \DateTime());

        $this->admin->update($object);

        $this->addFlash('sonata_flash_success', 'Update successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
    
    public function printAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $translator = $this->get('translator');
        $sessionLocale = $translator->getLocale();

        $translator->setLocale($object->getCustomer()->getLocale());

        $html = $this->renderView('AppBundle:Credit:pdf.html.twig', array(
             'credit'  => $object
        ));
        

        $translator->setLocale($sessionLocale);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('page-size' => 'A4','lowquality' => false)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$object->getCreditNumber().'.pdf"'
            )
        );        
    }  
    
    public function eraseAction($id) {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $em = $this->getDoctrine()->getManager();
        
        if ($object->getCreditNumber()) {
            $old = new Erase();
            $old->setType(Erase::TYPE_CREDIT);
            $old->setNumber($object->getCreditNumber());
            $em->persist($old);
            $em->flush();            
        }
        
        $this->admin->delete($object); 
        
        $this->addFlash('sonata_flash_success', 'Update successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));        
    }
    
    public function openAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$creditNr = $em->getRepository('AppBundle:Erase')->findandRemoveOldNumber(Erase::TYPE_CREDIT)) {
            $lastCredit = $em->getRepository('AppBundle:Credit')->findLastNumber(); 
            if ($lastCredit) {
                $creditNr = $lastCredit->getCreditNumber() + 1;
            } else {
                $creditNr = date("y") . " " . sprintf('%06s', 1);
            }
        }       
                
        if ($object->getStatus() == Credit::STATUS_NEW) {
            $object->setStatus(Credit::STATUS_OPEN);
            $object->setCreditNumber($creditNr);
        }

        $this->admin->update($object);     
        
        $translator = $this->get('translator');
        $sessionLocale = $translator->getLocale();

        $translator->setLocale($object->getCustomer()->getLocale());

        $html = $this->renderView('AppBundle:Credit:pdf.html.twig', array(
             'credit'  => $object
        ));
        

        $translator->setLocale($sessionLocale);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('page-size' => 'A4','lowquality' => false)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$object->getCreditNumber().'.pdf"'
            )
        );

        
    }    
    
    
    public function sendAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$creditNr = $em->getRepository('AppBundle:Erase')->findandRemoveOldNumber(Erase::TYPE_CREDIT)) {
            $lastCredit = $em->getRepository('AppBundle:Credit')->findLastNumber(); 
            if ($lastCredit) {
                $creditNr = $lastCredit->getCreditNumber() + 1;
            } else {
                $creditNr = date("y") . " " . sprintf('%06s', 1);
            }
        }
                
        if ($object->getStatus() == Credit::STATUS_NEW) {
            $object->setStatus(Credit::STATUS_OPEN);
            $object->setCreditNumber($creditNr);
        }

        $this->admin->update($object);
        
        if ($object->getCustomer()->getEmail() != '')
        {
            $translator = $this->get('translator');
            $sessionLocale = $translator->getLocale();

            $translator->setLocale($object->getCustomer()->getLocale());

            $path = __DIR__.'/../../../web/doc/'.$object->getCreditNumber().'.pdf';

            $this->get('knp_snappy.pdf')->generateFromHtml(
                $this->renderView('AppBundle:Credit:pdf.html.twig', array(
                    'credit'  => $object
                )),
                $path
            );     

            $message = \Swift_Message::newInstance()
                    ->setSubject($this->get('translator')->trans('mail.new_credit.title',array('%creditnumber%'=>$object->getCreditNumber()),'mail'))
                    ->setFrom('info@timeismoney.be', 'Time is Money bvba')
                    ->setTo($object->getCustomer()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'email/newCredit.html.twig',
                            array('credit' => $object)
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath($path))
                ;
            $this->get('mailer')->send($message); 
            $translator->setLocale($sessionLocale);
            $this->addFlash('sonata_flash_success', 'Email send');
            
            unlink($path);
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }  
    
    public function batchActionPdf(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT'))
        {
            throw new AccessDeniedException();
        }

        $selectedModels = $selectedModelQuery->execute();

        $html = '';
        foreach ($selectedModels as $selectedModel) {
            $html .= $this->renderView('AppBundle:Credit:pdf.html.twig', array(
                 'credit'  => $selectedModel
            ));

        }

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('page-size' => 'A4','lowquality' => false)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="all.pdf"'
            )
        );
    }        
    
}