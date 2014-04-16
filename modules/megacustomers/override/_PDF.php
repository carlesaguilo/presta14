<?php

class PDF extends PDFCore
{
	
	public function priceBreakDownCalculation(&$priceBreakDown)
	{
		$priceBreakDown['totalsWithoutTax'] = array();
		$priceBreakDown['totalsWithTax'] = array();
		$priceBreakDown['totalsEcotax'] = array();
		$priceBreakDown['wrappingCostWithoutTax'] = 0;
		$priceBreakDown['shippingCostWithoutTax'] = 0;
		$priceBreakDown['totalWithoutTax'] = 0;
		$priceBreakDown['totalWithTax'] = 0;
		$priceBreakDown['totalProductsWithoutTax'] = 0;
		$priceBreakDown['totalProductsWithTax'] = 0;
		$priceBreakDown['hasEcotax'] = 0;
		$priceBreakDown['totalsProductsWithTaxAndReduction'] = array();
		if (self::$order->total_paid == '0.00' AND self::$order->total_discounts == 0)
			return ;

		// Setting products tax
		if (!self::$orderSlip)
		{
			if (isset(self::$order->products) && count(self::$order->products))
				$products = self::$order->products;
			else
				$products = self::$order->getProducts();
		}
		else
		{
			$products = self::$orderSlip->getOrdersSlipProducts(self::$orderSlip->id, self::$order);
		}
		$amountWithoutTax = 0;
		$taxes = array();
		/* Firstly calculate all prices */
		foreach ($products AS &$product)
		{
			if (!isset($priceBreakDown['totalsWithTax'][$product['tax_rate']]))
				$priceBreakDown['totalsWithTax'][$product['tax_rate']] = 0;
			if (!isset($priceBreakDown['totalsEcotax'][$product['tax_rate']]))
				$priceBreakDown['totalsEcotax'][$product['tax_rate']] = 0;
			if (!isset($priceBreakDown['totalsEcotaxWithTax'][$product['tax_rate']]))
				$priceBreakDown['totalsEcotaxWithTax'][$product['tax_rate']] = 0;
			if (!isset($priceBreakDown['totalsWithoutTax'][$product['tax_rate']]))
				$priceBreakDown['totalsWithoutTax'][$product['tax_rate']] = 0;
			if (!isset($taxes[$product['tax_rate']]))
				$taxes[$product['tax_rate']] = 0;
			if (!isset($priceBreakDown['totalsProductsWithTaxAndReduction'][$product['tax_rate']]))
				$priceBreakDown['totalsProductsWithTaxAndReduction'][$product['tax_rate']] = 0;
			if (!isset($priceBreakDown['totalsProductsWithoutTaxAndReduction'][$product['tax_rate']]))
				$priceBreakDown['totalsProductsWithoutTaxAndReduction'][$product['tax_rate']] = 0;


			/* Without tax */
			if (self::$_priceDisplayMethod == PS_TAX_EXC)
				$product['priceWithoutTax'] = Tools::ps_round((float)($product['product_price']) + (float)$product['ecotax'], 2);
			else
				$product['priceWithoutTax'] = ($product['product_price_wt_but_ecotax'] / (1 + $product['tax_rate'] / 100)) + (float)$product['ecotax'];

			$product['priceWithoutTax'] =  $product['priceWithoutTax'] * (int)($product['product_quantity']);

			$amountWithoutTax += $product['priceWithoutTax'];
			/* With tax */
			$product['priceWithTax'] = (float)($product['product_price_wt']) * (int)($product['product_quantity']);
			$product['priceEcotax'] = $product['ecotax'] * (1 + $product['ecotax_tax_rate'] / 100);
		}

		$priceBreakDown['totalsProductsWithoutTax'] = $priceBreakDown['totalsWithoutTax'];
		$priceBreakDown['totalsProductsWithTax'] = $priceBreakDown['totalsWithTax'];

		$tmp = 0;
		$product = &$tmp;
		/* And secondly assign to each tax its own reduction part */
		$discountAmount = 0;
		if (!self::$orderSlip)
			$discountAmount = (float)(self::$order->total_discounts);
		$extrataxes = null;
		foreach ($products as $product)
		{
			$ratio = $amountWithoutTax == 0 ? 0 : $product['priceWithoutTax'] / $amountWithoutTax;
			$priceWithTaxAndReduction = $product['priceWithTax'] - $discountAmount * $ratio;
			$discountAmountWithoutTax = ($discountAmount * $ratio) / (1 + ($product['tax_rate'] / 100));
			if (self::$_priceDisplayMethod == PS_TAX_EXC)
			{
				$vat = $priceWithTaxAndReduction - Tools::ps_round($priceWithTaxAndReduction / $product['product_quantity'] / (((float)($product['tax_rate']) / 100) + 1), 2) * $product['product_quantity'];
			//	$extra_vat = $priceWithTaxAndReduction - Tools::ps_round($priceWithTaxAndReduction / $product['product_quantity'] / (((float)($product['extra_tax']) / 100) + 1), 2) * $product['product_quantity'];;
				$priceBreakDown['totalsWithoutTax'][$product['tax_rate']] += $product['priceWithoutTax'] ;
				$priceBreakDown['totalsProductsWithoutTax'][$product['tax_rate']] += $product['priceWithoutTax'];
				$total_ecotax = ($product['priceEcotax'] * $product['product_quantity']);
				$priceBreakDown['totalsProductsWithTax'][$product['tax_rate']] += Tools::ps_round((($product['priceWithoutTax'] - $total_ecotax) * (1 + $product['tax_rate'] / 100)) + $total_ecotax, 2);

	 			$price_tax_excl_with_reduction = Tools::ps_round($product['priceWithoutTax'] - (float)$discountAmountWithoutTax, 2);
				$priceBreakDown['totalsProductsWithoutTaxAndReduction'][$product['tax_rate']] += $price_tax_excl_with_reduction;
                $priceBreakDown['totalsProductsWithTaxAndReduction'][$product['tax_rate']] += Tools::ps_round(($price_tax_excl_with_reduction - $total_ecotax) * (1 + $product['tax_rate'] / 100) + $total_ecotax, 2);
			}
			else
			{
				$vat = (float)($product['priceWithoutTax']) * ((float)($product['tax_rate'])  / 100) * $product['product_quantity'];
				//$extravat = (float)($product['priceWithoutTax']) * ((float)($product['extra_tax'])  / 100) * $product['product_quantity'];
				$priceBreakDown['totalsWithTax'][$product['tax_rate']] += $product['priceWithTax'];
				$priceBreakDown['totalsProductsWithTax'][$product['tax_rate']] += $product['priceWithTax'];
				$priceBreakDown['totalsProductsWithoutTax'][$product['tax_rate']] += $product['priceWithoutTax'];
				$priceBreakDown['totalsProductsWithTaxAndReduction'][$product['tax_rate']] += $priceWithTaxAndReduction;
			}

			$priceBreakDown['totalsEcotax'][$product['tax_rate']] += ($product['priceEcotax']  * $product['product_quantity']);
			$priceBreakDown['totalsEcotaxWithTax'][$product['tax_rate']] += ($product['priceEcotax'] * (1 + ($product['ecotax_tax_rate'] / 100))  * $product['product_quantity']);
			if ($priceBreakDown['totalsEcotax'][$product['tax_rate']])
				$priceBreakDown['hasEcotax'] = 1;
			$taxes[$product['tax_rate']] += $vat;
			if($product['extra_tax']!=0)
			{
				$priceBreakDown['extra_tax2'][$product['tax_rate']] = $product['extra_tax']; 
				$priceBreakDown['extra_tax1'][$product['tax_rate']] = $product['tax_rate']-$product['extra_tax']; 
			}
				
		}

		$carrier_tax_rate = (float)self::$order->carrier_tax_rate;
		if (($priceBreakDown['totalsWithoutTax'] == $priceBreakDown['totalsWithTax']) AND (!$carrier_tax_rate OR $carrier_tax_rate == '0.00') AND (!self::$order->total_wrapping OR self::$order->total_wrapping == '0.00'))
			return ;

		foreach ($taxes AS $tax_rate => &$vat)
		{
			if (self::$_priceDisplayMethod == PS_TAX_EXC)
			{
				$priceBreakDown['totalsWithoutTax'][$tax_rate] = Tools::ps_round($priceBreakDown['totalsWithoutTax'][$tax_rate], 2);
				$priceBreakDown['totalsProductsWithoutTax'][$tax_rate] = Tools::ps_round($priceBreakDown['totalsWithoutTax'][$tax_rate], 2);
                //$total_with_tax = Tools::ps_round(($priceBreakDown['totalsWithoutTax'][$tax_rate]- $priceBreakDown['totalsEcotax'][$tax_rate]) * (1 + $tax_rate / 100) + $priceBreakDown['totalsEcotaxWithTax'][$tax_rate], 2);
				$priceBreakDown['totalsWithTax'][$tax_rate] = $priceBreakDown['totalsProductsWithTax'][$tax_rate];
                $priceBreakDown['totalWithTax'] += $priceBreakDown['totalsProductsWithTax'][$tax_rate];
			}
			else
			{
				$priceBreakDown['totalsWithoutTax'][$tax_rate] = $priceBreakDown['totalsProductsWithoutTax'][$tax_rate];
				$priceBreakDown['totalsProductsWithoutTax'][$tax_rate] = Tools::ps_round($priceBreakDown['totalsProductsWithoutTax'][$tax_rate], 2);
				$priceBreakDown['totalsProductsWithoutTaxAndReduction'][$tax_rate] = $priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate] / (1 + ($tax_rate / 100));
                $priceBreakDown['totalWithTax'] += $priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate];
			}

			$priceBreakDown['totalWithoutTax'] += $priceBreakDown['totalsWithoutTax'][$tax_rate];
			$priceBreakDown['totalProductsWithoutTax'] += $priceBreakDown['totalsProductsWithoutTax'][$tax_rate];
			$priceBreakDown['totalProductsWithTax'] += $priceBreakDown['totalsProductsWithTax'][$tax_rate];

		}

		$priceBreakDown['taxes'] = $taxes;
		$priceBreakDown['shippingCostWithoutTax'] = ($carrier_tax_rate AND $carrier_tax_rate != '0.00') ? (self::$order->total_shipping / (1 + ($carrier_tax_rate / 100))) : self::$order->total_shipping;
		if (self::$order->total_wrapping AND self::$order->total_wrapping != '0.00')
		{
			$wrappingTax = new Tax(Configuration::get('PS_GIFT_WRAPPING_TAX'));
			$priceBreakDown['wrappingCostWithoutTax'] = self::$order->total_wrapping / (1 + ((float)($wrappingTax->rate) / 100));
		}
	}

	/**
	* Tax table
	*/
	public function TaxTab(&$priceBreakDown)
	{
		$taxable_address = new Address((int)self::$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
		if (strtoupper(Country::getIsoById((int)$taxable_address->id_country)) == 'CA')
		 	return;

		if (Configuration::get('VATNUMBER_MANAGEMENT') && !empty($taxable_address->vat_number) && $taxable_address->id_country != Configuration::get('VATNUMBER_COUNTRY'))
		{
			$this->Ln();
			$this->Cell(30, 0, self::l('Exempt of VAT according section 259B of the General Tax Code.'), 0, 0, 'L');
			return;
		}

		if (self::$order->total_paid == '0.00' OR (!(int)(Configuration::get('PS_TAX')) AND self::$order->total_products == self::$order->total_products_wt))
			return ;

    	$carrier_tax_rate = (float)self::$order->carrier_tax_rate;
		if (($priceBreakDown['totalsWithoutTax'] == $priceBreakDown['totalsWithTax']) AND (!$carrier_tax_rate OR $carrier_tax_rate == '0.00') AND (!self::$order->total_wrapping OR self::$order->total_wrapping == '0.00'))
			return ;

		// Displaying header tax
		if ($priceBreakDown['hasEcotax'])
		{
			$header = array(self::l('Tax detail'), self::l('Tax'), self::l('Pre-Tax Total'), self::l('Total Tax'), self::l('Ecotax (Tax Incl.)'), self::l('Total with Tax'));
			$w = array(60, 20, 40, 20, 30, 20);
		}
		else
		{
			$header = array(self::l('Tax detail'), self::l('Tax'), self::l('Pre-Tax Total'), self::l('Total Tax'), self::l('Total with Tax'));
			$w = array(60, 30, 40, 30, 30);
		}
		$this->SetFont(self::fontname(), 'B', 8);
		for ($i = 0; $i < sizeof($header); $i++)
			$this->Cell($w[$i], 5, $header[$i], 0, 0, 'R');

		$this->Ln();
		$this->SetFont(self::fontname(), '', 7);

		$nb_tax = 0;

		// Display product tax
		foreach (array_keys($priceBreakDown['taxes']) AS $tax_rate)
		{
			$this->SetFont(self::fontname(), 'B', 8);	
			if ($tax_rate != '0.00' AND $priceBreakDown['totalsProductsWithTax'][$tax_rate] != '0.00')
			{
				$nb_tax++;
				$before = $this->GetY();
				$lineSize = $this->GetY() - $before;
				$this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
				$this->Cell($w[0], $lineSize, self::l('Products'), 0, 0, 'R');
				$this->Cell($w[1], $lineSize, number_format($tax_rate, 3, ',', ' ').' %', 0, 0, 'R');
				$this->Cell($w[2], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($priceBreakDown['totalsProductsWithoutTaxAndReduction'][$tax_rate] - $priceBreakDown['totalsEcotax'][$tax_rate], self::$currency, true)), 0, 0, 'R');
				$this->Cell($w[3], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate] - $priceBreakDown['totalsProductsWithoutTaxAndReduction'][$tax_rate], self::$currency, true)), 0, 0, 'R');
				if ($priceBreakDown['hasEcotax'])
					$this->Cell($w[4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($priceBreakDown['totalsEcotax'][$tax_rate], self::$currency, true)), 0, 0, 'R');
				$this->Cell($w[$priceBreakDown['hasEcotax'] ? 5 : 4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate], self::$currency, true)), 0, 0, 'R');
				$this->Ln();
			}
			if ($tax_rate != '0.00' AND isset($priceBreakDown['extra_tax1'][$tax_rate]))
			{
				$this->SetFont(self::fontname(), '', 6);
				$tax1= $priceBreakDown['extra_tax1'][$tax_rate];
				$nb_tax++;

				$total_wt = $priceBreakDown['totalsProductsWithoutTaxAndReduction'][$tax_rate] - $priceBreakDown['totalsEcotax'][$tax_rate];
				$total_wt = ($total_wt*$tax1)/$tax_rate;
				
				$iva = $priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate] - $priceBreakDown['totalsProductsWithoutTaxAndReduction'][$tax_rate];
				$iva = ($iva*$tax1)/$tax_rate;
				
				$total = $priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate];
				$total = ($total*$tax1)/$tax_rate;
				
				$total_ecotax = ($priceBreakDown['totalsEcotax'][$tax_rate]*$tax1)/$tax_rate;

				
				$before = $this->GetY();
				$lineSize = $this->GetY() - $before;
				$this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
				$this->Cell($w[0], $lineSize, self::l('Normal tax'), 0, 0, 'R');
				$this->Cell($w[1], $lineSize, number_format($tax1, 3, ',', ' ').' %', 0, 0, 'R');
				
				$this->Cell($w[2], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($total_wt, self::$currency, true)), 0, 0, 'R');
				
				$this->Cell($w[3], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($iva, self::$currency, true)), 0, 0, 'R');
				if ($priceBreakDown['hasEcotax'])
					$this->Cell($w[4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($total_ecotax, self::$currency, true)), 0, 0, 'R');
				
				$this->Cell($w[$priceBreakDown['hasEcotax'] ? 5 : 4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($total, self::$currency, true)), 0, 0, 'R');
				$this->Ln();
			}
			if ($tax_rate != '0.00' AND isset($priceBreakDown['extra_tax2'][$tax_rate]))
			{
				$this->SetFont(self::fontname(), '', 6);
				$tax2 = $priceBreakDown['extra_tax2'][$tax_rate];
				
				$total_wt = $priceBreakDown['totalsProductsWithoutTaxAndReduction'][$tax_rate] - $priceBreakDown['totalsEcotax'][$tax_rate];
				$total_wt = ($total_wt*$tax2)/$tax_rate;
				
				$iva = $priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate] - $priceBreakDown['totalsProductsWithoutTaxAndReduction'][$tax_rate];
				$iva = ($iva*$tax2)/$tax_rate;
				
				$total = $priceBreakDown['totalsProductsWithTaxAndReduction'][$tax_rate];
				$total = ($total*$tax2)/$tax_rate;
				
				$total_ecotax = ($priceBreakDown['totalsEcotax'][$tax_rate]*$tax2)/$tax_rate;
				
				$nb_tax++;
				$before = $this->GetY();
				$lineSize = $this->GetY() - $before;
				$this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
				$this->Cell($w[0], $lineSize, self::l('Equivalence Tax'), 0, 0, 'R');
				$this->Cell($w[1], $lineSize, number_format($tax2, 3, ',', ' ').' %', 0, 0, 'R');
				$this->Cell($w[2], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($total_wt, self::$currency, true)), 0, 0, 'R');
					$this->Cell($w[3], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($iva, self::$currency, true)), 0, 0, 'R');
				if ($priceBreakDown['hasEcotax'])
					$this->Cell($w[4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($total_ecotax, self::$currency, true)), 0, 0, 'R');
				$this->Cell($w[$priceBreakDown['hasEcotax'] ? 5 : 4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($total, self::$currency, true)), 0, 0, 'R');
				$this->Ln();
			}
		}
		
	
		$this->SetFont(self::fontname(), 'B', 8);	

		// Display carrier tax
		if ($carrier_tax_rate AND $carrier_tax_rate != '0.00' AND ((self::$order->total_shipping != '0.00' AND !self::$orderSlip) OR (self::$orderSlip AND self::$orderSlip->shipping_cost)))
		{
			$nb_tax++;
			$before = $this->GetY();
			$lineSize = $this->GetY() - $before;
			$this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
			$this->Cell($w[0], $lineSize, self::l('Carrier'), 0, 0, 'R');
			$this->Cell($w[1], $lineSize, number_format($carrier_tax_rate, 3, ',', ' ').' %', 0, 0, 'R');
			$this->Cell($w[2], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($priceBreakDown['shippingCostWithoutTax'], self::$currency, true)), 0, 0, 'R');
			$this->Cell($w[3], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice(self::$order->total_shipping - $priceBreakDown['shippingCostWithoutTax'], self::$currency, true)), 0, 0, 'R');
			if ($priceBreakDown['hasEcotax'])
				$this->Cell($w[4], $lineSize, (self::$orderSlip ? '-' : '').'', 0, 0, 'R');
			$this->Cell($w[$priceBreakDown['hasEcotax'] ? 5 : 4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice(self::$order->total_shipping, self::$currency, true)), 0, 0, 'R');
			$this->Ln();
		}

		// Display wrapping tax
		if (self::$order->total_wrapping AND self::$order->total_wrapping != '0.00')
		{
			$tax = new Tax((int)(Configuration::get('PS_GIFT_WRAPPING_TAX')));
			$taxRate = $tax->rate;

			$nb_tax++;
			$before = $this->GetY();
			$lineSize = $this->GetY() - $before;
			$this->SetXY($this->GetX(), $this->GetY() - $lineSize + 3);
			$this->Cell($w[0], $lineSize, self::l('Gift-wrapping'), 0, 0, 'R');
			$this->Cell($w[1], $lineSize, number_format($taxRate, 3, ',', ' ').' %', 0, 0, 'R');
			$this->Cell($w[2], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice($priceBreakDown['wrappingCostWithoutTax'], self::$currency, true)), 0, 0, 'R');
			$this->Cell($w[3], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice(self::$order->total_wrapping - $priceBreakDown['wrappingCostWithoutTax'], self::$currency, true)), 0, 0, 'R');
			$this->Cell($w[4], $lineSize, (self::$orderSlip ? '-' : '').self::convertSign(Tools::displayPrice(self::$order->total_wrapping, self::$currency, true)), 0, 0, 'R');
		}

		if (!$nb_tax)
			$this->Cell(190, 10, self::l('No tax'), 0, 0, 'C');
	}

}

