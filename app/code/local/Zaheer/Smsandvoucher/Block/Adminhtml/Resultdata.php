<?php


class Zaheer_Smsandvoucher_Block_Adminhtml_Resultdata extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_resultdata";
	$this->_blockGroup = "smsandvoucher";
	$this->_headerText = Mage::helper("smsandvoucher")->__("Resultdata Manager");
	$this->_addButtonLabel = Mage::helper("smsandvoucher")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}