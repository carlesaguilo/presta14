<?php
require_once(_PS_MODULE_DIR_ . "megacustomers/megacustomers.class.php");


class Cart extends CartCore 
{
		
	
	public function updateQty($quantity, $id_product, $id_product_attribute = NULL, $id_customization = false, $operator = 'up') 
	{	
		global $cookie;
		
		if(isset($cookie->id_customer) && $cookie->id_customer!=0 && $operator == 'up')
		{	
			$mcc = new MegaCustomer((int)$cookie->id_customer);
			$result =  parent::updateQty($quantity, $id_product, $id_product_attribute, $id_customization, $operator);
			
			$totalamount = 0;
			if(isset($mcc->config['amount_month']) && (float)$mcc->config['amount_month']!=0)
			{
				$totalamount = (float)$mcc->config['amount_month'];
			}
			if($totalamount==0)
			{
				$totalamount = (float)Configuration::get('MEGACUSTOMER_AMOUNT_MONTH');
			}
			if(isset($mcc) && $totalamount>0)
			{
					$customerTotal = $mcc->totalOrderMonth();
					$total = $this->getOrderTotal();
					$totalPrice = $total + $customerTotal;
					if($totalPrice>$totalamount)
					{
						  parent::updateQty($quantity, $id_product, $id_product_attribute, $id_customization, 'down');
						  return false;
					}

			}
		}
		else
		{
			$result =  parent::updateQty($quantity, $id_product, $id_product_attribute, $id_customization, $operator);
		}		  
		return $result;   	
	}
	
	
	
	
}

?>