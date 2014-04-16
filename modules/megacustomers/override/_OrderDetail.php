<?php

class OrderDetail extends OrderDetailCore
{

	public 		$extra_tax;

	public function getFields()
	{
		$fields = parent::getFields();
		$fields['extra_tax'] = (float)($this->extra_tax);
		return $fields;
	}


}