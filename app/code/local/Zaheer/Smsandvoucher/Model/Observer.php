<?php
class Zaheer_Smsandvoucher_Model_Observer {
	protected $_stoerId;
	protected $_orderIncrementId;
	public function generateVoucherAndSendSMS(Varien_Event_Observer $observer) {
		$shipment = $observer->getEvent()->getShipment();
		$this->_generateShipmentVoucher($shipment);
		if ($shipment->getOrigData('entity_id')) {
			// in case of updating shipment do nothing
			return;
		}
		$order = $shipment->getOrder();
		$this->_stoerId = $shipment->getStoreId();
		$this->_orderIncrementId = $order->getIncrementId();
		/* send message to customer about shipment generation */
		$mobileNumber = $order->getShippingAddress()->getTelephone();
		if ($this->_getSmsAPIKey() && $this->_checkIsMobileValid($mobileNumber)) {
			$smsResult = $this->_sendSMSMessage($this->_getSmsAPIKey(), $mobileNumber);
		}
		/* end send message */

		return $this;
	}

	protected function _sendSMSMessage($smsAPIkey, $mobileNumber) {
		$endpoint = 'https://smscenter.gr/api/sms/send';
		$parameters = array(
			'key' => $smsAPIkey,
			'text' => 'Your Shipment generated for order #' . $this->_orderIncrementId,
			'from' => 'sender',
			'to' => $mobileNumber,
			'type' => 'json', // type of return format
		);
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $endpoint);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($c);
		curl_close($c);
		$resultArray = json_decode($output);
		return $resultArray;

	}
	protected function _generateShipmentVoucher($shipment) {
		try {
			$voucherUser = Mage::getStoreConfig('zaheersection/smsgroup/voucheruser', $this->_stoerId);
			$voucherPass = Mage::getStoreConfig('zaheersection/smsgroup/voucherpass', $this->_stoerId);
			$voucherAppKey = Mage::getStoreConfig('zaheersection/smsgroup/voucherappkey', $this->_stoerId);
			$order = $shipment->getOrder();
			foreach ($shipment->getAllItems() as $item) {
				$shipmentTotal += $item->getPrice() * $shipment->getTotalQty();
			}
			$result = [];
			$createdtime = new Zend_Date(strtotime($shipment->getCreatedAt()));
			$shipmentDate = Mage::getModel('core/date')->date('Y-m-d', $createdtime);
			$shippingAddress = $order->getShippingAddress();
			$soap = new SoapClient("http://testvoucher.taxydromiki.gr/JobServices.asmx?WSDL");
			$oAuthResult = $soap->Authenticate(
				array(
					'sUsrName' => $voucherUser,
					'sUsrPwd' => $voucherPass,
					'applicationKey' => $voucherAppKey,
				)
			);
			if ($oAuthResult->AuthenticateResult->Result != 0) {
				$result[] = "Error authenticating!!<br>";
				return $result;
			}
			$oVoucher = array(
				'OrderId' => $order->getIncrementId(),
				'Name' => $order->getCustomerName(),
				'Address' => $shippingAddress->getStreet(),
				'City' => $shippingAddress->getCity(),
				'Telephone' => $shippingAddress->getTelephone(),
				'Zip' => $shippingAddress->getPostcode(), 'Destination' => "",
				'Courier' => "",
				'Pieces' => 3,
				'Weight' => 0,
				'Comments' => 'voucher generated',
				'Services' => "αν",
				'CodAmount' => number_format($shipmentTotal),
				'InsAmount' => 0,
				'VoucherNumber' => "",
				'SubCode' => "",
				'BelongsTo' => "",
				'DeliverTo' => "",
				'ReceivedDate' => $shipmentDate,
			);
			$xml = array(
				'sAuthKey' => $oAuthResult->AuthenticateResult->Key,
				'oVoucher' => $oVoucher,
				'eType' => "Voucher",
			);
			$oResult = $soap->CreateJob($xml);
			if ($oResult->CreateJobResult->Result != 0) {
				$result[] = "Error Creating a voucher!!<br>";
				return $result;
			}
			$xml = array(
				'authKey' => $oAuthResult->AuthenticateResult->Key,
				'voucherNo' => $oResult->CreateJobResult->Voucher,
				'language' => 'el',
			);
			$TT = $soap->TrackAndTrace($xml);
			$soap->ClosePendingJobs(
				array('sAuthKey' => $oAuthResult->AuthenticateResult->Key)
			);
			$result['success'] = $TT;
		} catch (SoapFault $fault) {
			$result['error'] = $fault;
		}
		return $result;
	}

	protected function _getSmsAPIKey() {
		$smsUser = Mage::getStoreConfig('zaheersection/smsgroup/smsuser', $this->_stoerId);
		$smsPass = Mage::getStoreConfig('zaheersection/smsgroup/smspass', $this->_stoerId);
		$endpoint = "https://smscenter.gr/api/key/get?username=$smsUser&password=$smsPass&type=json";

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $endpoint);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($c);
		curl_close($c);
		$resultArray = json_decode($output);
		if ($resultArray->status != '1') {
			return false;
		}
		$origKey = $resultArray->key;
		$resetEndPoint = "https://smscenter.gr/api/key/reset?key=$origKey&type=json";
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $resetEndPoint);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($c);
		curl_close($c);
		$resultArray = json_decode($output);
		if ($resultArray->status != '1') {
			return false;
		}
		return $resultArray->key;

	}

	protected function _checkIsMobileValid($mobileNumber = null) {
		if ($mobileNumber == null) {
			return false;
		}
		$endpoint = 'https://smscenter.gr/api/mobile/check';

		$parameters = array(
			'mobile' => $mobileNumber, // mobile to check
			'type' => 'json',
		); // type of return form
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $endpoint);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($c);
		curl_close($c);
		$resultArray = json_decode($output);
		if ($resultArray->status != '1') {
			return false;
		}
		return true;

	}

}