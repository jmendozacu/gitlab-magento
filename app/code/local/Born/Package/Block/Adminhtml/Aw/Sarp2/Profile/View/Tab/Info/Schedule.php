<?php 

class Born_Package_Block_Adminhtml_Aw_Sarp2_Profile_View_Tab_Info_Schedule extends AW_Sarp2_Block_Adminhtml_Profile_View_Tab_Info_Schedule
{
	protected function _initForm()
	{
		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset(
			'schedule',
			array('legend' => $this->__('Profile Schedule'))
			);

		$date = $this->getProfile()->getData('start_date');

		$date = Mage::helper('born_package/aw_sarp2_data')->getFixedStartDate($this->getProfile());

		$fieldset->addField(
			'start_date',
			'label',
			array(
				'name'  => 'start_date',
				'value' => $this->_formatDate($date),
				'label' => $this->__('Start Date'),
				'bold'  => true,
				)
			);

		$date = $this->getProfile()->getData('details/next_billing_date');
		if (!is_null($date)) {
            //if no data then no display
			$fieldset->addField(
				'next_billing_date',
				'label',
				array(
					'name'  => 'next_billing_date',
					'value' => $this->_formatDate($date),
					'label' => $this->__('Next Billing Date'),
					'bold'  => true,
					)
				);
		}

		$date = $this->getProfile()->getData('details/final_payments_date');
		if (!is_null($date)) {
            //if no data then no display
			$fieldset->addField(
				'final_billing_date',
				'label',
				array(
					'name'  => 'final_billing_date',
					'value' => $this->_formatDate($date),
					'label' => $this->__('Final Billing Date'),
					'bold'  => true,
					)
				);
		}

		$fieldset->addField(
			'trial_period',
			'label',
			array(
				'name'  => 'trial_period',
				'value' => $this->_getTrialInfo(),
				'label' => $this->__('Trial Period'),
				'bold'  => true,
				)
			);

		$fieldset->addField(
			'billing_period',
			'label',
			array(
				'name'  => 'billing_period',
				'value' => $this->_getRegularInfo(),
				'label' => $this->__('Billing Period'),
				'bold'  => true,
				)
			);

		$this->setForm($form);
	}
}

?>