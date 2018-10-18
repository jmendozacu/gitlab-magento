<?php 

class Born_Package_Block_Aw_Sarp2_Customer_Profile_View_Schedule extends AW_Sarp2_Block_Customer_Profile_View_Schedule
{
	public function getInfoBoxFields()
	{
		$profile = $this->getProfile();

		$startDateTime = Mage::helper('born_package/aw_sarp2_data')->getFixedStartDate($profile);

		$item = $profile->getSubscriptionItem();
		$fields = array(
			array(
				'title' => $this->__('Start Date:'),
				'value' => $this->formatDate(
					$startDateTime, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, false
					),
				)
			);
		if ($item->getTypeModel()->getTrialIsEnabled()) {
			$trialValue = Mage::helper('aw_sarp2/humanizer')->getTrialPeriodInformation($item);
			$fields[] = array(
				'title' => $this->__('Trial Period:'),
				'value' => $trialValue['period'] . "\n" . $trialValue['occurrences'],
				);
		}
		$regularValue = Mage::helper('aw_sarp2/humanizer')->getRegularPeriodInformation($item);
		$fields[] = array(
			'title' => $this->__('Billing Period:'),
			'value' => $regularValue['period'] . "\n" . $regularValue['occurrences'],
			);
		return $fields;
	}
}

?>