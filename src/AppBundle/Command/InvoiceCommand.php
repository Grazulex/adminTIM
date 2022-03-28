<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Company;
use AppBundle\Entity\User;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceLine;

/**
 * Class FromoldCommand
 * @package AppBundle\Command
 */
class InvoiceCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('tim:invoice')
            ->setDescription('install invoice from old database')
            ->addArgument('companyId', InputArgument::REQUIRED, 'Company id from old')
            ->addArgument('userId', InputArgument::REQUIRED, 'User Id from old');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
                
        $kernel = $this->getContainer()->get('kernel');
        $em = $this->getContainer()->get('doctrine')->getManager('default');
        $emold = $this->getContainer()->get('doctrine')->getManager('old');
        $connold = $emold->getConnection();
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $output->writeln([
            'synchro company & customer',
            '==========================',
            '',
        ]);  
        
        $sql = "SELECT inv_facture_company.nfirme, inv_facture_company.nomfirme, inv_facture_company.codefirme, inv_facture_company.Enable FROM inv_facture_company WHERE inv_facture_company.nfirme=".$input->getArgument('companyId');
        $results = $connold->query($sql)->fetchAll();
        foreach ($results as $result) {
            if (!$company = $em->getRepository('AppBundle:Company')->findOneBy(array(strtolower('name') => strtolower($result['nomfirme'])))) {
                $company = new Company();
                $company->setEnable($result['Enable']);
                $company->setName($result['nomfirme']);
                $em->persist($company);
                $em->flush();
                $em->refresh($company);
                $output->writeln('create : '.$company->getName());
            } else {
                $company->setEnable($result['Enable']);
                $em->persist($company);
                $em->flush();
                $em->refresh($company);    
                $output->writeln('update : '.$company->getName());
            }   
            $output->writeln($company->getName());
            $sqlUsers = "select inv_customer.nclient, inv_customer.firme_id, inv_customer.prenom, inv_customer.nom, inv_customer.email, inv_customer.TVA, inv_customer.adresse, inv_customer.nrue,'',inv_customer.cp, inv_customer.ville,inv_customer.telephone, '',inv_customer.password, inv_customer.Enable, inv_customer.Langue from inv_customer where inv_customer.nclient = ".$input->getArgument('userId')." AND inv_customer.firme_id =".$result['nfirme']." order by inv_customer.nclient";
            $resultsUser = $connold->query($sqlUsers)->fetchAll();
            foreach ($resultsUser as $user) {
                if (!$customer = $em->getRepository('AppBundle:User')->findOneBy(array(strtolower('username') => strtolower($user['email']), strtolower('email') => strtolower($user['email'])))) {
                    $customer = $userManager->createUser();
                    $customer->setCompany($company);
                    $customer->setOld($user['nclient']);
                    $customer->setEmail($user['email']);
                    $customer->setEnabled($user['Enable']);
                    $customer->setFirstname($user['prenom']);
                    $customer->setLastname($user['nom']);
                    $customer->setLocked(0);
                    if (strtolower($user['Langue']) == 'nl') {
                        $customer->setLocale('nl_BE');
                    }
                    if (strtolower($user['Langue']) == 'fr') {
                        $customer->setLocale('fr_BE');
                    }
                    if (strtolower($user['Langue']) == 'en') {
                        $customer->setLocale('en_GB');
                    }
                    $customer->setPlainPassword("123123123") ;
                    $customer->setSuperAdmin(false);
                    $customer->setTimezone('Europe/Brussels');
                    $customer->setUsername($user['email']);
                    $customer->setPhone($user['telephone']);
                    $userManager->updateUser($customer);
                    $output->writeln('create : '.$user['nclient'].'-'.$customer->getUsername());
                } else {
                    if (strtolower($user['Langue']) == 'nl') {
                        $customer->setLocale('nl_BE');
                    }
                    if (strtolower($user['Langue']) == 'fr') {
                        $customer->setLocale('fr_BE');
                    }
                    if (strtolower($user['Langue']) == 'en') {
                        $customer->setLocale('en_GB');
                    }    
                    $customer->setPhone($user['telephone']);
                    $customer->setCompany($company);
                    $em->persist($customer);
                    $em->flush();
                    $output->writeln('update : '.$user['nclient'].'-'.$customer->getUsername());
                }
                    
                $sqladdress = "select inv_customer.nclient, inv_customer.firme_id, inv_customer.prenom, inv_customer.nom, inv_customer.email, inv_customer.TVA, inv_customer.adresse, inv_customer.nrue,'',inv_customer.cp, inv_customer.ville,inv_customer.telephone, '',inv_customer.password, inv_customer.Enable, inv_customer.Langue from inv_customer where inv_customer.nclient =".$user['nclient'];
                $resultsAddress = $connold->query($sqladdress)->fetchAll();
                foreach ($resultsAddress as $address) {
                    if (!$location = $em->getRepository('AppBundle:Location')->findOneBy(array('user' => $customer->getId(), strtolower('adress1') => strtolower($address['adresse']),strtolower('city') => strtolower($address['ville'])))) {

                        $location=new \AppBundle\Entity\Location;
                        $location->setUser($customer);
                        $location->setFirstname($address['prenom']);
                        $location->setLastname($address['nom']);                    
                        $location->setAdress1($address['adresse']);
                        $location->setAdress2($address['nrue']);
                        $location->setCity($address['ville']);
                        $location->setCountry('BE');
                        $location->setZip($address['cp']);
                        $location->setVat($address['TVA']);
                        $em->persist($location);
                        $em->flush();
                        $output->writeln('create : '.$location->getAdress1());
                    } else {
                        $location->setVat($address['TVA']);
                        $em->persist($location);
                        $em->flush();
                        $output->writeln('update : '.$location->getId().'->'. $location->getAdress1());                        
                    }
                }
                
                $output->writeln([
                    'synchro invoices',
                    '================',
                    '',
                ]);    
                
                $sqlInvoices = "SELECT inv_facture.ID_Facture, inv_facture.ID_client, inv_facture.Date_insert, inv_facture.Date_terme,inv_facture.Total, inv_facture.Ristourne, inv_facture.Text_Ristourne, inv_facture.Reference, CONVERT(replace(inv_facture.Factur_Nummer, ' ',''),UNSIGNED INTEGER) as invoice_number, inv_facture.Date_Paiement, inv_facture.Status, inv_facture.ReferencePaie, inv_facture.Type_Ristourne, inv_facture.Total_Ristourne FROM inv_facture where inv_facture.ID_client =".$user['nclient'];
                $resultsInvoices = $connold->query($sqlInvoices)->fetchAll();
                foreach ($resultsInvoices as $invoice) {
                    if (!$em->getRepository('AppBundle:Invoice')->findOneBy(array('user' => $customer->getId(), 'invoice_number' => $invoice['invoice_number']))) {
                        $facture = new Invoice;
                        $facture->setUser($customer);
                        $facture->setLocation($location);
                        $facture->setDiscount($invoice['Ristourne']);
                        $facture->setDiscountComment($invoice['Text_Ristourne']);
                        $facture->setDiscountType($invoice['Type_Ristourne']);
                        $facture->setInvoiceNumber($invoice['invoice_number']);
                        $facture->setPaiement(new \DateTime($invoice['Date_Paiement']));
                        $facture->setReference($invoice['Reference']);
                        $facture->setReferencePaiement($invoice['ReferencePaie']);
                        $facture->setStatus($invoice['Status']);
                        $facture->setTerm(new \DateTime($invoice['Date_terme']));
                        $facture->setTotal($invoice['Total']);
                        if (doubleval($invoice['Total_Ristourne']) > 0) {
                            $facture->setTotalWithDiscount($invoice['Total_Ristourne']);
                        } else {
                            $facture->setTotalWithDiscount($invoice['Total']);
                        }        
                        $facture->setCreated(new \DateTime($invoice['Date_insert']));
                        $em->persist($facture);
                        $em->flush(); 
                        $output->writeln('create invoice : '.$facture->getInvoiceNumber());      
                        
                        //lines
                        
                        $sqlLines="SELECT inv_facture_line.ID_Facture, inv_facture_line.Text, inv_facture_line.Unit_Price,inv_facture_line.Qty,inv_facture_line.VAT/100 as vat FROM inv_facture_line where inv_facture_line.ID_Facture =".$invoice['ID_Facture'];
                        $resultsLines = $connold->query($sqlLines)->fetchAll();
                        foreach ($resultsLines as $line) {
                            $factureline = new InvoiceLine;
                            $factureline->setInvoice($facture);
                            $factureline->setName($line['Text']);
                            $factureline->setPrice($line['Unit_Price']);
                            $factureline->setQuantity($line['Qty']);
                            $factureline->setTaxrate($line['vat']);
                            $em->persist($factureline);                            
                            $output->writeln('create line : '.$factureline->getName());                              
                        } 
                        $em->flush(); 
                        
                        //reminder
                        $sqlReminders ="SELECT inv_facture_rappel.Facture_id, inv_facture_rappel.Date_send,inv_facture_rappel.type_reminder FROM inv_facture_rappel WHERE Facture_id=".$invoice['ID_Facture'];
                        $resultsReminders = $connold->query($sqlReminders)->fetchAll();
                        foreach ($resultsReminders as $reminder) {
                            $rappel = new \AppBundle\Entity\Reminder;
                            $rappel->setCreated(new \DateTime($reminder['Date_send']));
                            $rappel->setInvoice($facture);
                            $rappel->setType($reminder['type_reminder']);
                            $em->persist($rappel);                            
                            $output->writeln('create reminder');                                
                        }                        
                        $em->flush(); 
                    }
                }     
                
                $output->writeln([
                    'synchro credit',
                    '================',
                    '',
                ]);    
                
                $sqlCredits = "SELECT inv_cn.ID_Facture,inv_cn.ID_client,inv_cn.Date_insert,inv_cn.Total,inv_cn.Ristourne,inv_cn.Text_Ristourne,inv_cn.Reference,CONVERT(replace(inv_cn.Factur_Nummer, ' ',''),UNSIGNED INTEGER) as invoice_number,inv_cn.Date_Paiement,inv_cn.Status FROM inv_cn where inv_cn.ID_client =".$user['nclient'];
                $resultsCredits = $connold->query($sqlCredits)->fetchAll();
                foreach ($resultsCredits as $credit) {
                    if (!$em->getRepository('AppBundle:Credit')->findOneBy(array('user' => $customer->getId(), 'credit_number' => $invoice['invoice_number']))) {
                        $creditnote = new \AppBundle\Entity\Credit;
                        $creditnote->setUser($customer);
                        $creditnote->setLocation($location);
                        $creditnote->setDiscount($credit['Ristourne']);
                        $creditnote->setDiscountComment($credit['Text_Ristourne']);
                        $creditnote->setCreditNumber($credit['invoice_number']);
                        $creditnote->setPaiement(new \DateTime($credit['Date_Paiement']));
                        $creditnote->setReference($credit['Reference']);
                        $creditnote->setStatus($credit['Status']);
                        $creditnote->setTotal($credit['Total']);
                        if (doubleval($credit['Ristourne']) > 0) {
                            $creditnote->setTotalWithDiscount($credit['Ristourne']);
                        } else {
                            $creditnote->setTotalWithDiscount($credit['Total']);
                        }        
                        $creditnote->setCreated(new \DateTime($credit['Date_insert']));
                        $em->persist($creditnote);
                        $em->flush(); 
                        $output->writeln('create credit: '.$creditnote->getCreditNumber());      
                        
                        //lines
                        
                        $sqlLines="SELECT inv_cn_line.ID_Facture,inv_cn_line.Text,inv_cn_line.Unit_Price,inv_cn_line.Qty,inv_cn_line.VAT/100 as vat FROM inv_cn_line where inv_cn_line.ID_Facture =".$credit['ID_Facture'];
                        $resultsLines = $connold->query($sqlLines)->fetchAll();
                        foreach ($resultsLines as $line) {
                            $creditline = new \AppBundle\Entity\CreditLine;
                            $creditline->setCredit($creditnote);
                            $creditline->setName($line['Text']);
                            $creditline->setPrice($line['Unit_Price']);
                            $creditline->setQuantity($line['Qty']);
                            $creditline->setTaxrate($line['vat']);
                            $em->persist($creditline);                            
                            $output->writeln('create line : '.$creditline->getName());                              
                        }   
                        $em->flush(); 
                    }
                }  
                
            }
        }


        $output->writeln('install done');
    }
}