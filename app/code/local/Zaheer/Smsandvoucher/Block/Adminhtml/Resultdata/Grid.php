<?php

class Zaheer_Smsandvoucher_Block_Adminhtml_Resultdata_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId("resultdataGrid");
		$this->setDefaultSort("resultdata_id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection() {
		$collection = Mage::getModel("smsandvoucher/resultdata")->getCollection();
		$collection->getSelect()->joinLeft('sales_flat_shipment_grid', 'main_table.shipment_id = sales_flat_shipment_grid.entity_id', array('increment_id as shipment_increment_id'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns() {
		$this->addColumn("resultdata_id", array(
			"header" => Mage::helper("smsandvoucher")->__("ID"),
			"align" => "right",
			"width" => "50px",
			"type" => "number",
			"index" => "resultdata_id",
		));

		$this->addColumn("shipment_increment_id", array(
			"header" => Mage::helper("smsandvoucher")->__("Shipment Id#"),
			"index" => "shipment_increment_id",
			"filter_index" => "sales_flat_shipment_grid.increment_id",

		));
		$this->addColumn("resultdata", array(
			"header" => Mage::helper("smsandvoucher")->__("Result Data"),
			"index" => "resultdata",
			'filter' => false,
			'renderer' => 'Zaheer_Smsandvoucher_Block_Adminhtml_Resultdata_Renderer_Resultdatadecorater',
		));
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

		return parent::_prepareColumns();
	}

	public function getRowUrl($row) {
		return '#';
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('resultdata_id');
		$this->getMassactionBlock()->setFormFieldName('resultdata_ids');
		$this->getMassactionBlock()->setUseSelectAll(true);
		$this->getMassactionBlock()->addItem('remove_resultdata', array(
			'label' => Mage::helper('smsandvoucher')->__('Remove Resultdata'),
			'url' => $this->getUrl('*/adminhtml_resultdata/massRemove'),
			'confirm' => Mage::helper('smsandvoucher')->__('Are you sure?'),
		));
		return $this;
	}

}