<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Swift_Attachment;

use AppBundle\Entity\Logistic;


class CashController extends Controller
{
    
    public function batchActionRepport(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT'))
        {
            throw new AccessDeniedException();
        }

        $selectedModels = $selectedModelQuery->execute();
        $categories = array();
        foreach ($selectedModels as $selectedModel) {
            if (isset($categories[$selectedModel->getCategory()->getId()])) {
                $categories[$selectedModel->getCategory()->getId()] =  array('reference' => $selectedModel->getCategory()->getReference(), 'name' => $selectedModel->getCategory()->getName(), 'total' => $categories[$selectedModel->getCategory()->getId()]['total'] + $selectedModel->getTotal());
            } else {
                $categories[$selectedModel->getCategory()->getId()] =  array('reference' => $selectedModel->getCategory()->getReference(), 'name' => $selectedModel->getCategory()->getName(), 'total' => $selectedModel->getTotal());
            }
        }
        $html = $this->renderView('AppBundle:Cash:pdf.html.twig', array(
             'categories'  => $categories
        ));        
        
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('page-size' => 'A4','lowquality' => false)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="cash.pdf"'
            )
        );
    }    
    
}