<?php

class Born_Package_Helper_Aw_Sarp2_Data extends Mage_Core_Helper_Abstract
{
	//Add time from created at DateTime to Start Time
	//This resolves UTC datetime conversion issue
	public function getFixedStartDate($profile)
	{
		if (!($profile instanceof AW_Sarp2_Model_Profile)) {
			return;
		}

		$createdAtDateTime = $profile->getCreatedAt();
		$startDate = $profile->getStartDate();

		$startDateTime = $this->addTimeStartDate($createdAtDateTime, $startDate);

		return $startDateTime;
	}


	public function addTimeStartDate($createdAtDateTime, $startDate)
	{
		if ($createdAtDateTime) {
			$createAtTime = explode(' ', $createdAtDateTime);
			if ($createAtTime[1]) {
				$createAtTime = $createAtTime[1];

				$startDateTime = $startDate . ' ' . $createAtTime;

				return $startDateTime;
			}
		}

		return $startDate;
	}
}
?>