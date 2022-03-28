<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Swift_Attachment;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceLine;
use AppBundle\Entity\Reminder;
use AppBundle\Entity\Erase;


class InvoiceController extends Controller
{
    public function paidAction(Request $request)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $object->setStatus(Invoice::STATUS_CLOSED);
        $object->setPaiement(new \DateTime());

        $this->admin->update($object);

        $this->addFlash('sonata_flash_success', 'Update successfully');

        return new RedirectResponse($request->headers->get('referer'));
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

        $html = $this->renderView('AppBundle:Invoice:pdf.html.twig', array(
             'invoice'  => $object
        ));
        

        $translator->setLocale($sessionLocale);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('page-size' => 'A4','lowquality' => false)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$object->getInvoiceNumber().'.pdf"'
            )
        );

    }  
    
    public function eraseAction($id) {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $em = $this->getDoctrine()->getManager();
        
        if ($object->getInvoiceNumber()) {
            $old = new Erase();
            $old->setType(Erase::TYPE_INVOICE);
            $old->setNumber($object->getInvoiceNumber());
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
        if (!$invoiceNr = $em->getRepository('AppBundle:Erase')->findandRemoveOldNumber(Erase::TYPE_INVOICE)) {
            $lastInvoice = $em->getRepository('AppBundle:Invoice')->findLastNumber(); 
            if ($lastInvoice) {
                $invoiceNr = $lastInvoice->getInvoiceNumber() + 1;
            } else {
                $invoiceNr = date("y") . " " . sprintf('%06s', 1);
            }
        }
        
        $comm = sprintf('%010s', $invoiceNr);
                
        if ($object->getStatus() == Invoice::STATUS_NEW) {
            $object->setStatus(Invoice::STATUS_OPEN);
            $object->setInvoiceNumber($invoiceNr);
            $object->setReferencePaiement($this->generate_structured_communication($comm));
        }

        $this->admin->update($object);     
        
        $translator = $this->get('translator');
        $sessionLocale = $translator->getLocale();

        $translator->setLocale($object->getCustomer()->getLocale());

        $html = $this->renderView('AppBundle:Invoice:pdf.html.twig', array(
             'invoice'  => $object
        ));
        

        $translator->setLocale($sessionLocale);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('page-size' => 'A4','lowquality' => false)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$object->getInvoiceNumber().'.pdf"'
            )
        );

        
    }
    
    public function sendAction(Request $request)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$invoiceNr = $em->getRepository('AppBundle:Erase')->findandRemoveOldNumber(Erase::TYPE_INVOICE)) {
            $lastInvoice = $em->getRepository('AppBundle:Invoice')->findLastNumber(); 
            if ($lastInvoice) {
                $invoiceNr = $lastInvoice->getInvoiceNumber() + 1;
            } else {
                $invoiceNr = date("y") . " " . sprintf('%06s', 1);
            }
        } 
        
        $comm = sprintf('%010s', $invoiceNr);
                
        if ($object->getStatus() == Invoice::STATUS_NEW) {
            $object->setStatus(Invoice::STATUS_OPEN);
            $object->setInvoiceNumber($invoiceNr);
            $object->setReferencePaiement($this->generate_structured_communication($comm));
        }

        $this->admin->update($object);
        
        if ($object->getCustomer()->getEmail() != '')
        {
            $translator = $this->get('translator');
            $sessionLocale = $translator->getLocale();

            $translator->setLocale($object->getCustomer()->getLocale());

            $path = __DIR__.'/../../../web/doc/'.$object->getInvoiceNumber().'.pdf';

            $this->get('knp_snappy.pdf')->generateFromHtml(
                $this->renderView('AppBundle:Invoice:pdf.html.twig', array(
                    'invoice'  => $object
                )),
                $path
            );     

			try {
            $message = \Swift_Message::newInstance()
                    ->setSubject($this->get('translator')->trans('mail.new_invoice.title',array('%invoicenumber%'=>$object->getInvoiceNumber()),'mail'))
                    ->setFrom('info@timeismoney.be', 'Time is Money bvba')
                    ->setTo($object->getCustomer()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'email/newInvoice.html.twig',
                            array('invoice' => $object)
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath($path))
                ;
            $this->get('mailer')->send($message); 
			} catch (Exception $e) {
				print_r($e);
				die();
			}
			
            $translator->setLocale($sessionLocale);
            $this->addFlash('sonata_flash_success', 'Email send');
            
            unlink($path);
        } 

        return new RedirectResponse($request->headers->get('referer'));
    }    
    
    public function reminderAction(Request $request)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
                
        $type = Reminder::TYPE_FIRST;
        $cost = 0;
        
        $em = $this->getDoctrine()->getManager();
        $old = $em->getRepository('AppBundle:Reminder')
                ->findLastByInvoice($object);   
        if ($old) {
            if ($old->getType() == Reminder::TYPE_FIRST) {
                $type = Reminder::TYPE_SECOND;
            }elseif ($old->getType() == Reminder::TYPE_SECOND) {
                $type = Reminder::TYPE_THIRD;
                $cost = 6.05;
            }elseif ($old->getType() == Reminder::TYPE_THIRD) {
                $type = Reminder::TYPE_FOUR; 
                $cost = 25;
            }else{
                $type = Reminder::TYPE_FOUR;
            }            
        }
        $reminder = new Reminder();
        $reminder->setInvoice($object);
        $reminder->setType($type);
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($reminder);
        $em->flush();
        
        if ($cost > 0) {
            $line = new InvoiceLine();
            $line->setInvoice($object);
            $line->setName($this->get('translator')->trans('line.new_reminder_'.$type.'.name'));
            $line->setPrice($cost);
            $line->setQuantity(1);
            $line->setTaxrate(0);
            $em->persist($line);
            $em->flush();     
            
            $total = 0;
            foreach ($object->getLines() as $line) {
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

            $object->setTotalWithDiscount($total_with_discount);                        
            $object->setTotal($total);
            $em->persist($object);
            $em->flush();            
        
        }
        
        if ($object->getCustomer()->getEmail() != '')
        {
            $translator = $this->get('translator');
            $sessionLocale = $translator->getLocale();

            $translator->setLocale($object->getCustomer()->getLocale());

            $path = __DIR__.'/../../../web/doc/'.$object->getInvoiceNumber().'.pdf';

            $this->get('knp_snappy.pdf')->generateFromHtml(
                $this->renderView('AppBundle:Invoice:pdf.html.twig', array(
                    'invoice'  => $object
                )),
                $path
            );     

            $message = \Swift_Message::newInstance()
                    ->setSubject($this->get('translator')->trans('mail.new_reminder_'.$type.'.title',array('%invoicenumber%'=>$object->getInvoiceNumber()),'mail'))
                    ->setFrom('info@timeismoney.be', 'Time is Money bvba')
                    ->setTo($object->getCustomer()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'email/newReminder_'.$type.'.html.twig',
                            array('invoice' => $object)
                        ),
                        'text/html'
                    )
                    ->attach(\Swift_Attachment::fromPath($path))
                ;
            $this->get('mailer')->send($message); 
            $translator->setLocale($sessionLocale);
            $this->addFlash('sonata_flash_success', 'Reminder send');
            unlink($path);
        }        
        
        return new RedirectResponse($request->headers->get('referer'));
    }    
    
    private function generate_structured_communication($transactionID) 
    { //tansactionID = 10 int
            $base = (float) $transactionID;
            $control = $base - floor($base / 97) * 97;
            if ($control == 0) {
                $control = 97;
            }
            $base_s = (string) $base;
            $control_s = (string) $control;
            if ($control < 10) {
                $control_s = "0" . $control_s;
            } $base_len = strlen($base_s);
            $count = 10 - $base_len;
            for ($i = 0; $i < $count; $i++) {
                $base_s = "0" . $base_s;
            }
            $com = $base_s . $control_s;
            return "+++" . substr($com, 0, 3) . "/" . substr($com, 3, 4) . "/" . substr($com, 7, 5) . "+++";
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
            $html .= $this->renderView('AppBundle:Invoice:pdf.html.twig', array(
                 'invoice'  => $selectedModel
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