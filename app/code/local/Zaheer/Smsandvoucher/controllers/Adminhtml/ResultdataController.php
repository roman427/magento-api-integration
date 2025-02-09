<?php

class Zaheer_Smsandvoucher_Adminhtml_ResultdataController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('smsandvoucher/resultdata');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("smsandvoucher/resultdata")->_addBreadcrumb(Mage::helper("adminhtml")->__("Resultdata  Manager"),Mage::helper("adminhtml")->__("Resultdata Manager"));
				return $this;
		}
		public function indexAction()
		{
			    $this->_title($this->__("Smsandvoucher"));
			    $this->_title($this->__("Manager Resultdata"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{
			    $this->_title($this->__("Smsandvoucher"));
				$this->_title($this->__("Resultdata"));
			    $this->_title($this->__("Edit Item"));

				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("smsandvoucher/resultdata")->load($id);
				if ($model->getId()) {
					Mage::register("resultdata_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("smsandvoucher/resultdata");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Resultdata Manager"), Mage::helper("adminhtml")->__("Resultdata Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Resultdata Description"), Mage::helper("adminhtml")->__("Resultdata Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("smsandvoucher/adminhtml_resultdata_edit"))->_addLeft($this->getLayout()->createBlock("smsandvoucher/adminhtml_resultdata_edit_tabs"));
					$this->renderLayout();
				}
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("smsandvoucher")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Smsandvoucher"));
		$this->_title($this->__("Resultdata"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("smsandvoucher/resultdata")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("resultdata_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("smsandvoucher/resultdata");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Resultdata Manager"), Mage::helper("adminhtml")->__("Resultdata Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Resultdata Description"), Mage::helper("adminhtml")->__("Resultdata Description"));


		$this->_addContent($this->getLayout()->createBlock("smsandvoucher/adminhtml_resultdata_edit"))->_addLeft($this->getLayout()->createBlock("smsandvoucher/adminhtml_resultdata_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("smsandvoucher/resultdata")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Resultdata was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setResultdataData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					}
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setResultdataData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("smsandvoucher/resultdata");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					}
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('resultdata_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("smsandvoucher/resultdata");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'resultdata.csv';
			$grid       = $this->getLayout()->createBlock('smsandvoucher/adminhtml_resultdata_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		}
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'resultdata.xml';
			$grid       = $this->getLayout()->createBlock('smsandvoucher/adminhtml_resultdata_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
