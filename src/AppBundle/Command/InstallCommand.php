<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Company;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceLine;

/**
 * Class FromoldCommand
 * @package AppBundle\Command
 */
class InstallCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('tim:install')
            ->setDescription('install from old database');
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
        
        $sql = "SELECT ps_group.id_group, ps_group_lang.name FROM `ps_group` inner join ps_group_lang on (ps_group_lang.id_group = ps_group.id_group and ps_group_lang.id_lang = 3)";
        $results = $connold->query($sql)->fetchAll();
        foreach ($results as $result) {
            if (!$company = $em->getRepository('AppBundle:Company')->findOneBy(array(strtolower('name') => strtolower($result['name'])))) {
                $company = new Company();
                $company->setEnable(true);
                $company->setName($result['name']);
                $em->persist($company);
                $em->flush();
                $em->refresh($company);
            }    
            $output->writeln($company->getName());
            $output->writeln('***************');
            $sqlUsers = "SELECT ps_customer.`id_customer`, ps_customer.`id_default_group`, ps_customer.`firstname`, ps_customer.`lastname`, ps_customer.`email`, ps_address.phone, ps_customer.`active` FROM `ps_customer` left join ps_address on (ps_address.id_customer = ps_customer.id_customer and ps_address.active = true and ps_address.deleted = false) left join ps_country on (ps_country.id_country=ps_address.id_country) WHERE ps_customer.`id_default_group` = ".$result['id_group']." AND ps_customer.id_customer > 5500 group by ps_customer.id_customer";
            $resultsUser = $connold->query($sqlUsers)->fetchAll();
            foreach ($resultsUser as $user) {
                if ($customer = !$em->getRepository('AppBundle:Customer')->findOneBy(array(strtolower('username') => strtolower($user['email']), strtolower('email') => strtolower($user['email'])))) {
                    $customer = $userManager->createUser();
                    $customer->setCompany($company);
                    $customer->setOld($user['id_customer']);
                    $customer->setEmail($user['email']);
                    $customer->setEnabled($user['active']);
                    $customer->setFirstname($user['firstname']);
                    $customer->setLastname($user['lastname']);
                    $customer->setLocked(0);
                    $customer->setLocale('nl_BE');
                    $customer->setPlainPassword("123123123") ;
                    $customer->setSuperAdmin(false);
                    $customer->setTimezone('Europe/Brussels');
                    $customer->setUsername($user['email']);
                    $userManager->updateUser($customer);
                    $output->writeln($customer->getUsername());
                    
                    $sqladdress = "Select ps_address.company, ps_address.firstname, ps_address.lastname, ps_address.address1, ps_address.address2, ps_address.postcode, ps_address.city,ps_country.iso_code FROM ps_address left join ps_country on (ps_country.id_country=ps_address.id_country) WHERE ps_address.id_customer = ".$user['id_customer'];
                    $resultsAddress = $connold->query($sqladdress)->fetchAll();
                    foreach ($resultsAddress as $address) {
                        $location=new \AppBundle\Entity\Location;
                        $location->setCustomer($customer);
                        $location->setFirstname($address['firstname']);
                        $location->setLastname($address['lastname']);                    
                        $location->setAdress1($address['address1']);
                        $location->setAdress2($address['address2']);
                        $location->setAdress3($address['company']);
                        $location->setCity($address['city']);
                        $location->setCountry($address['iso_code']);
                        $location->setZip($address['postcode']);                    
                        $em->persist($location);
                        $output->writeln($location->getAdress1());
                    }
                    $em->flush();
                }
            }
        }


        exit();

        
        $output->writeln([
            'synchro categories & items',
            '==========================',
            '',
        ]);  
                
        $sql = "SELECT ps_category.id_category, ps_category_lang.name, ps_category.id_parent FROM `ps_category` inner join ps_category_lang on (ps_category_lang.id_category = ps_category.id_category and ps_category_lang.id_lang=1) WHERE ps_category.active = true";
        $results = $connold->query($sql)->fetchAll();
        foreach ($results as $result) {
            if (!$em->getRepository('AppBundle:ItemCategory')->findBy(array(strtolower('name') => strtolower($result['name'])))) {
                $category = new \AppBundle\Entity\ItemCategory();
                $category->setLocale('en'); 
                $category->setName($result['name']);
                $nameParent = $connold->query("SELECT name FROM ps_category_lang WHERE id_category = ".$result['id_parent']."  AND id_lang=1")->fetchAll();
                $parent=null;
                if ($nameParent) {
                    if ($parent = $em->getRepository('AppBundle:ItemCategory')->findOneBy(array(strtolower('name') => strtolower($nameParent[0]['name'])))) {
                        $category->setParent($parent); 
                    }
                }
                $category->setUseForCash(true);
                $category->setUseForLogistic(true);
                $em->persist($category);
                $em->flush();
                $fr = $connold->query("SELECT name FROM ps_category_lang WHERE id_category = ".$result['id_category']."  AND id_lang=2")->fetchAll();
                $category->setLocale('fr');
                $category->setName($fr[0]['name']);
                $em->persist($category);
                $em->flush();
                $nl = $connold->query("SELECT name FROM ps_category_lang WHERE id_category = ".$result['id_category']."  AND id_lang=3")->fetchAll();
                $category->setLocale('nl');
                $category->setName($nl[0]['name']); 
                $em->persist($category);
                $em->flush();
                $output->writeln($category->getName());
                $output->writeln('***************');
                
                $sqlItems = "SELECT ps_product.id_product, ps_product_lang.name, ps_product.id_category_default FROM `ps_product` 
                    inner join ps_product_lang on (ps_product_lang.id_product = ps_product.id_product and ps_product_lang.id_lang = 1) 
                    WHERE ps_product.`active` = 1 and ps_product.id_category_default = ".$result['id_category'];

                $resultsItems = $connold->query($sqlItems)->fetchAll();
                foreach ($resultsItems as $resultItem) {
                    if (!$em->getRepository('AppBundle:Item')->findBy(array(strtolower('name') => strtolower($resultItem['name'])))) {
                        $item = new \AppBundle\Entity\Item();
                        $item->setLocale('en'); 
                        $item->setName($resultItem['name']); 
                        $item->setCategory($category);
                        $em->persist($item);
                        $em->flush();
                        $output->writeln($item->getName());
                        $fr = $connold->query("SELECT name FROM ps_product_lang WHERE id_product = ".$resultItem['id_product']."  AND id_lang=2")->fetchAll();
                        if ($fr) {
                            $item->setLocale('fr');
                            $item->setName($fr[0]['name']);
                            $em->persist($item);
                            $em->flush();
                        }
                        $nl = $connold->query("SELECT name FROM ps_category_lang WHERE id_category = ".$resultItem['id_product']."  AND id_lang=3")->fetchAll();
                        if ($nl) {
                            $item->setLocale('nl');
                            $item->setName($nl[0]['name']); 
                            $em->persist($item);
                            $em->flush();
                        }
                    }       
                }
            }
        }         

        
        $output->writeln('install done');
    }
}