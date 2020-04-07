<?php

class Zaheer_Smsandvoucher_Block_Adminhtml_Resultdata_Renderer_Resultdatadecorater extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
		$formatedValue = json_decode($row->getData($this->getColumn()->getIndex()), true);
		$smsResult = $formatedValue['SMS']['remarks'];
		$voucherResult = $formatedValue['Voucher'][0];
		return "<strong>SMS : </strong> $smsResult<br><strong>Voucher :</strong> $voucherResult ";

	}

}