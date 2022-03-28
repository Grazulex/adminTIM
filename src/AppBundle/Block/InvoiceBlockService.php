<?php
 
namespace AppBundle\Block;
 
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
 
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService ;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

 
class InvoiceBlockService extends AbstractBlockService
{
    protected $em;
    protected $max_item_dashboard;
 
    /**
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct($name, $templating, $em, $max_item_dashboard)
    {
        parent::__construct($name, $templating);
 
        $this->templating         = $templating;
        $this->em                 = $em;
        $this->max_item_dashboard = $max_item_dashboard;
    }
  
    /**
     * Define valid options for a block of this type.
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'url'      => false,
            'title'    => 'Invoices',
            'template' => 'AppBundle:Block:invoice.html.twig'
        ));
    }
     
 
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        // just an example
        $invoices = $this->em->getRepository('AppBundle:Invoice')->retrieveDraft($this->max_item_dashboard);
        $nbr = $this->em->getRepository('AppBundle:Invoice')->retrieveAllDraft();
 
        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'    => $blockContext->getBlock(),
            'settings' => $settings,
            'invoices' => $invoices,
            'nbr' => $nbr
        ), $response);
    }
}