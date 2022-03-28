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
class DailyImportCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('tim:daily:import')
            ->setDescription('import from old database');
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
        $date = new \DateTime();
        $date->sub(new \DateInterval('P1D'));
        $dateCheck = $date->format('Y-m-d');

        $output->writeln([
            'synchro company',
            '===============',
            '',
        ]);  
        
        $sql = "SELECT ps_group.id_group, ps_group_lang.name FROM `ps_group` inner join ps_group_lang on (ps_group_lang.id_group = ps_group.id_group and ps_group_lang.id_lang = 3) WHERE ps_group.date_add > '".$dateCheck."'";
        $results = $connold->query($sql)->fetchAll();
        foreach ($results as $result) {
            if (!$em->getRepository('AppBundle:Company')->findBy(array(strtolower('name') => strtolower($result['name'])))) {
                $company = new Company();
                $company->setEnable(true);
                $company->setName($result['name']);
                $em->persist($company);
                $em->flush();
            }
        }

        
        $output->writeln([
            'synchro Customer & location',
            '===========================',
            '',
        ]);  

        $sql = "SELECT ps_group_lang.name, ps_customer.`id_customer`, ps_customer.`id_default_group`, ps_customer.`firstname`, ps_customer.`lastname`, ps_customer.`email`, ps_address.address1, ps_address.address2, ps_address.postcode, ps_address.city,ps_country.iso_code, ps_address.phone, ps_customer.`active` FROM `ps_customer` left join ps_address on (ps_address.id_customer = ps_customer.id_customer and ps_address.active = true and ps_address.deleted = false) left join ps_country on (ps_country.id_country=ps_address.id_country) inner join ps_group_lang on (ps_group_lang.id_group = ps_customer.`id_default_group` and ps_group_lang.id_lang = 3) WHERE ps_customer.`date_add` > '".$dateCheck."' group by ps_customer.id_customer";
        $results = $connold->query($sql)->fetchAll();
        foreach ($results as $user) {
                if (!$customer = $em->getRepository('AppBundle:Customer')->findOneBy(array('adress1' => strtolower($user['address1']), strtolower('email') => strtolower($user['email'])))) {
                    $customer=new \AppBundle\Entity\Customer();
                    
                    $sql = "SELECT name FROM `ps_group_lang`
			WHERE id_group =" . $user['id_default_group'] . " AND id_lang =3 ";
                    $resultCompany = $connold->query($sql)->fetchAll();
                    foreach ($resultCompany as $company) {
                        $companyname = $company['name'];
                    }                                        
                    $company = $em->getRepository('AppBundle:Company')->findOneBy(array('name'=>$companyname));
                    $customer->setCompany($company);
                    $customer->setOld($user['id_customer']);
                    $customer->setEmail($user['email']);
                    $customer->setFirstname($user['firstname']);
                    $customer->setLastname($user['lastname']);
                    $customer->setLocale('nl_BE');
                    $customer->setAdress1($user['address1']);
                    $customer->setAdress2($user['address2']);
                    $customer->setCity($user['city']);
                    $customer->setCountry($user['iso_code']);
                    $customer->setZip($user['postcode']);
                    $em->persist($customer);
                    $em->flush();
                    $output->writeln('create : '.$user['id_customer'].'-'.$customer->getFullname());
                    
                }

                //$em->persist($customer);
                //$em->flush();
            
        }
        
        $output->writeln([
            'synchro order',
            '=============',
            '',
        ]);       
        
        $sql = "SELECT ps_orders.`date_add`, ps_orders.id_order, ps_orders.id_cart, ps_orders.id_customer, ps_customer.email FROM `ps_orders` inner join ps_customer on (ps_customer.id_customer = ps_orders.id_customer) WHERE ps_orders.valid = 1 AND ps_orders.`date_add` > '".$dateCheck."'";
        
        $results = $connold->query($sql)->fetchAll();
        foreach ($results as $result) {
            $output->writeln($result['id_order']);
            $start = true;
            $sql = "SELECT ps_order_detail.product_id, name as category , id_order, `product_name` , `product_quantity` , (
                product_price + ( `product_price` * ( tax_rate /100 ) )
                ) AS product_price, `product_quantity` * ( product_price + ( `product_price` * ( tax_rate /100 ) ) ) AS price, `tax_rate`
                FROM `ps_order_detail`
                LEFT JOIN ps_product ON ( ps_product.id_product = ps_order_detail.product_id )
                LEFT JOIN ps_category_lang ON (ps_category_lang.id_category = ps_product.id_category_default and id_lang=1)
                WHERE id_order =" . $result['id_order'] . " ORDER BY `id_order_detail`";
            $resultItems = $connold->query($sql)->fetchAll();
            foreach ($resultItems as $item) {
                $sql = "SELECT name, value, id_cart
				FROM `ps_customization_field`
				INNER JOIN ps_customization_field_lang ON ( ps_customization_field_lang.`id_customization_field` = ps_customization_field.`id_customization_field`
				AND `id_lang` =1 )
				INNER JOIN ps_customized_data ON ( ps_customized_data.index = ps_customization_field.id_customization_field )
				INNER JOIN ps_customization ON ( ps_customization.`id_customization` = ps_customized_data.id_customization )
				WHERE `required` =1 and (name = 'Marque' or name = 'Nummer plaat' or name = 'Immatriculation')
				AND ps_customization.id_cart = '" . $result['id_cart'] . "' AND ps_customization.`id_product` ='" . $item['product_id'] . "'";
                $resultExtras = $connold->query($sql)->fetchAll();
                $nameextra = "";
                foreach ($resultExtras as $extra) {
                    if ($nameextra != "") {
                        $nameextra .= " - ";
                    } else {
                        $nameextra .= "(";
                    }
                    $nameextra .= $extra['value'];
                }
                if ($nameextra != "") {
                    $nameextra .= ")";
                }

                $date = new \DateTime($result['date_add']);
                $name = $date->format('d-m-Y') . " - ";
                if (trim($item['category']) != "") {
                    $name .= ucfirst($item['category']) . " - " . ucfirst($item['product_name']);
                }
                if ($nameextra != "") {
                    $name .= " " . $nameextra . " ";
                } 
                $output->writeln($name.' : '.$item['price'].'/'.$item['product_price']);
                

                
                if ($start == true) {
                    if(!$customer = $em->getRepository('AppBundle:Customer')->findOneBy(array('email' => strtolower($result['email'])))) {
                        $sql = "SELECT ps_group_lang.name, ps_customer.`id_customer`, ps_customer.`id_default_group`, ps_customer.`firstname`, ps_customer.`lastname`, ps_customer.`email`, ps_address.address1, ps_address.address2, ps_address.postcode, ps_address.city,ps_country.iso_code, ps_address.phone, ps_customer.`active` FROM `ps_customer` left join ps_address on (ps_address.id_customer = ps_customer.id_customer and ps_address.active = true and ps_address.deleted = false) left join ps_country on (ps_country.id_country=ps_address.id_country) inner join ps_group_lang on (ps_group_lang.id_group = ps_customer.`id_default_group` and ps_group_lang.id_lang = 3) WHERE ps_customer.`email` = '".$result['email']."' group by ps_customer.id_customer";
                        $results = $connold->query($sql)->fetchAll();
                        foreach ($results as $user) {
                            $customer=new \AppBundle\Entity\Customer();
                            $sql = "SELECT name FROM `ps_group_lang`
                                WHERE id_group =" . $user['id_default_group'] . " AND id_lang =3 ";
                            $resultCompany = $connold->query($sql)->fetchAll();
                            foreach ($resultCompany as $company) {
                                $companyname = $company['name'];
                            }                                        
                            $company = $em->getRepository('AppBundle:Company')->findOneBy(array('name'=>$companyname));
                            $customer->setCompany($company);
                            $customer->setOld($user['id_customer']);
                            $customer->setEmail($user['email']);
                            $customer->setFirstname($user['firstname']);
                            $customer->setLastname($user['lastname']);
                            $customer->setLocale('nl_BE');
                            $customer->setAdress1($user['address1']);
                            $customer->setAdress2($user['address2']);
                            $customer->setCity($user['city']);
                            $customer->setCountry($user['iso_code']);
                            $customer->setZip($user['postcode']);
                            $em->persist($customer);
                            $em->flush();
                            $output->writeln('create : '.$user['id_customer'].'-'.$customer->getFullname());

                        }                     
                    }


                    if (!$invoice = $em->getRepository('AppBundle:Invoice')->findOneBy (array('customer' => $customer, 'status' => 0))) {
                        $invoice = new Invoice;
                        $invoice->setCreated(new \DateTime());
                        $invoice->setCustomer($customer);
                        $invoice->setStatus(0);
                        $em->persist($invoice);
                        $em->flush();
                    }
                    $sql = "SELECT name , id_order, 1 as `product_quantity` , value AS price, 0 as tax_rate
                        FROM `ps_order_discount`
                        WHERE id_order =" . $result['id_order'] . " ";
                    $resultDiscounts = $connold->query($sql)->fetchAll();
                    foreach ($resultDiscounts as $discount) {
                        $invoice->setDiscountType('€');
                        $invoice->setDiscount($discount['price']);
                        $invoice->setDiscountComment($discount['name']);
                        $em->persist($invoice);
                        $em->flush();
                    }
                    $output->writeln($invoice->getId());
                }

                $line = new InvoiceLine();
                $line->setInvoice($invoice);
                $line->setName($name);
                $line->setPrice($item['product_price']);
                $line->setQuantity($item['product_quantity']);
                $line->setTaxrate($item['tax_rate']/100);;
                $em->persist($line);
                $em->flush();  

                $start = false;
            }
                
            $total = 0;
            $em->refresh($invoice);
            foreach ($invoice->getLines() as $line2) {
                $total=$total+$line2->getTotalPrice();
            }
            $output->writeln($invoice->getId().'->total : '.$total);
            $total_with_discount = $total;
            if ($invoice->getDiscount() > 0) {
                if ($invoice->getDiscountType() === '€') 
                {
                    $total_with_discount = $total_with_discount - $invoice->getDiscount();
                } else {
                    $total_with_discount = $total_with_discount- ($total_with_discount * ($invoice->getDiscount()/100));
                }
            }
            $output->writeln($invoice->getId().'->totalwithdiscount : '.$total_with_discount);
            $invoice->setTotal($total);
            $invoice->setTotalWithDiscount($total_with_discount);
            $em->persist($invoice);
            $em->flush();                
        }
        $output->writeln('import done');
    }        
    
}