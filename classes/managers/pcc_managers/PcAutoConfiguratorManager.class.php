<?php

require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectMbLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectCaseLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectRamLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectCpuLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectHddLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectCoolerLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectMonitorLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectOptLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectGraphicsLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectKeyboardLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectMouseLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectSpeakerLoad.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectPowerLoad.class.php");

class PcAutoConfiguratorManager extends AbstractManager {

	private $office_pc_components_ratio;
	private $gaming_pc_components_ratio;
	private $office_pc_components_ratio_only_case;
	private $gaming_pc_components_ratio_only_case;
	private $customer;

	/**
	 * @var singleton instnce of class
	 */
	private static $instance = null;

	/**
	 * Initializes DB mappers
	 *
	 * @param object $config
	 * @param object $args
	 * @return
	 */
	function __construct($config, $args, $user) {
		$this->config = $config;
		$this->args = $args;
		$this->user = $user;
		$this->pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		$this->itemManager = ItemManager::getInstance($this->config, $this->args);
		$this->office_pc_components_ratio = explode(',', $this->getCmsVar('office_pc_components_ratio'));
		$this->gaming_pc_components_ratio = explode(',', $this->getCmsVar('gaming_pc_components_ratio'));
		$this->office_pc_components_ratio_only_case = explode(',', $this->getCmsVar('office_pc_components_ratio_only_case'));
		$this->gaming_pc_components_ratio_only_case = explode(',', $this->getCmsVar('gaming_pc_components_ratio_only_case'));

		$userManager = UserManager::getInstance($config, $args);
		$vipCustomer = false;
		if ($this->user->getLevel() == UserGroups::$USER) {
			$userDto = $userManager->selectByPK($this->user->getId());
			$vipCustomer = $userManager->isVipAndVipEnabled($userDto);
		}
		if ($vipCustomer) {
			$pc_configurator_discount = $this->getCmsVar('vip_pc_configurator_discount');
		} else {
			$pc_configurator_discount = $this->getCmsVar('pc_configurator_discount');
		}
	}

	/**
	 * Returns an singleton instance of this class
	 *
	 * @param object $config
	 * @param object $args
	 * @return
	 */
	public static function getInstance($config, $args, $customer) {

		if (self::$instance == null) {

			self::$instance = new PcAutoConfiguratorManager($config, $args, $customer);
		}
		return self::$instance;
	}

	/**
	 * return format is array containing the components ids
	 * array(case, motherboard,cpu, cooler,ram, hdd, opt, monitor, graphics, power, keyboar, mouse, speaker)
	 */
	public function suggestPcByPrice($customerPriceInUsd, $gaming, $onlyCase) {
		$customerPriceInUsd = floatval($customerPriceInUsd);
		$mb_socket = $this->getCmsVar('suggested_pc_default_motherboard_socket_category_id');
		//step 1 selecting Motherboard
		$deviation = 0;
		list($mb, $deviation) = $this->getMbForSuggestedPc($customerPriceInUsd, $gaming, $onlyCase, $mb_socket);

		//step 2 selecting Case
		if ($mb > 0) {
			list($case, $deviation) = $this->getCaseForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 3 selecting Cpu
			list($cpu, $deviation) = $this->getCpuForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);
			//step 4 selecting Cooler
			list($cooler, $deviation) = $this->getCoolerForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 5 selecting Ram
			list($ram, $deviation) = $this->getRamForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 6 selecting Hdd
			list($hdd, $deviation) = $this->getHddForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);
			//step 7 selecting Optical Drive
			list($opt, $deviation) = $this->getOptForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 8 selecting Monitor
			list($monitor, $deviation) = $this->getMonitorForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 9 selecting Graphice Card
			list($graphics, $deviation) = $this->getGraphicsForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 10 selecting Power
			list($power, $deviation) = $this->getPowerForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 11 selecting Keyboard
			list($keyboard, $deviation) = $this->getKeyboardForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 12 selecting Mouse
			list($mouse, $deviation) = $this->getMouseForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);

			//step 13 selecting Speaker
			list($speaker, $deviation) = $this->getSpeakerForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $mb);
		}
		return array($case, $mb, $cpu, $cooler, $ram, $hdd, $opt, $monitor, $graphics, $power, $keyboard, $mouse, $speaker);
	}

	private function findNearestComponentByPrice($targetPrice, $deviation, $components) {
		if (empty($components)) {
			return array(null, 0);
		}

		foreach ($components as $key => $component) {
			$itemPrice = $this->calcItemPriceWithDiscount($component);
			if ($itemPrice > $targetPrice) {
				if ($key == 0) {
					//var_dump('arachin@ vercnem');
					return array($component, $itemPrice);
				}
				if (abs($targetPrice - $this->calcItemPriceWithDiscount($components[$key - 1])) > abs($targetPrice - $itemPrice)) {
					//if ($deviation < 0) {
					//var_dump('mec vercnem');
					return array($component, $itemPrice);
				} else {
					//var_dump('pokr vercnem');
					return array($components[$key - 1], $this->calcItemPriceWithDiscount($components[$key - 1]));
				}
			}
		}
		//var_dump('verchin@ vercnem');
		return array(end($components), $this->calcItemPriceWithDiscount(end($components)));
	}

	private function calcItemPriceWithDiscount($item) {
		if ($this->user->getLevel() === UserGroups::$GUEST || ($this->user->getLevel() === UserGroups::$USER && $item->getIsDealerOfThisCompany() == 0)) {
			$itemPrice = floatval($item->getCustomerItemPrice());
			$itemPrice = $itemPrice * (1 - floatval(dddd) / 100);
		} else {
			$itemPrice = floatval($item->getDealerPrice());
		}
		return $itemPrice;
	}

	private function getMbForSuggestedPc($customerPriceInUsd, $gaming, $onlyCase, $mb_socket) {
		$mbRatio = floatval($this->getMbRatioForSuggestedPc($gaming, $onlyCase));
		if ($mbRatio > 0) {
			$targetPrice = $customerPriceInUsd * $mbRatio;
			$requiredCategoriesFormulasArray = array('(', CategoriesConstants::MB_FORM_FACTOR_ATX, 'or', CategoriesConstants::MB_FORM_FACTOR_MINI_ATX, 'or', CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX, ')', 'and', $mb_socket, 'and', '(', CategoriesConstants::MB_RAM_SLOT_COUNT_2, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_4, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_6, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_8, ')', 'and', CategoriesConstants::MB_RAM_TYPE_DDR3, 'and', CategoriesConstants::MB_SATA_SUPPORTED);
			if ($gaming) {
				$requiredCategoriesFormulasArray = array_merge($requiredCategoriesFormulasArray, array('and', CategoriesConstants::MB_GRAPHICS_PCI_EXPRESS));
			} else {
				$requiredCategoriesFormulasArray = array_merge($requiredCategoriesFormulasArray, array('and', CategoriesConstants::MB_GRAPHICS_ONBOARD));
			}
			$mbs = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedMb, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, 0, $mbs);
			if ($selctedMb !== null) {
				$deviation = $targetPrice - $customerPriceWithDiscount;
				//echo "mb";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedMb->getId(), $deviation);
			}
		}
		return array(0, 0);
	}

	private function getCaseForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		$caseRatio = floatval($this->getCaseRatioForSuggestedPc($gaming, $onlyCase));

		if ($caseRatio > 0) {
			$targetPrice = $customerPriceInUsd * $caseRatio;
			$case_size = $this->pccm->getMbCorrespondingCaseSizes($selectedMotherboard);
			$case_size[] = ')';
			$case_size = array_merge(array('('), $case_size);
			$requiredCategoriesFormulasArray = array_merge($case_size, array('and', CategoriesConstants::CASE_POWER_SUPPLY, 'and', 'not', CategoriesConstants::CASE_NO_POWER_SUPPLY));
			$cases = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedCase, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $cases);
			if ($selctedCase !== null) {
				$deviation -= $targetPrice - $customerPriceWithDiscount;
				//echo "case";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedCase->getId(), $deviation);
			}
		}
		return array(0, $deviation);
	}

	private function getCpuForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		$cpuRatio = floatval($this->getCpuRatioForSuggestedPc($gaming, $onlyCase));
		if ($cpuRatio > 0) {
			$targetPrice = $customerPriceInUsd * $cpuRatio;
			$cpu_socket = $this->pccm->getMbCorrespondingCpuSocket($selectedMotherboard);
			$requiredCategoriesFormulasArray = array($cpu_socket);
			$cpus = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedCpu, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $cpus);
			if ($selctedCpu !== null) {
				$deviation -= $targetPrice - $customerPriceWithDiscount;
				//echo "cpu";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedCpu->getId(), $deviation);
			}
		}
		return array(0, $deviation);
	}

	private function getCoolerForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		$coolerRatio = floatval($this->getCoolerRatioForSuggestedPc($gaming, $onlyCase));
		if ($coolerRatio > 0) {
			$targetPrice = $customerPriceInUsd * $coolerRatio;
			$cooler_socket = $this->pccm->getMbCorrespondingCoolerSocket($selectedMotherboard);
			$requiredCategoriesFormulasArray = array($cooler_socket);
			$coolers = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedCooler, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $coolers);
			if ($selctedCooler !== null) {
				$deviation -= $targetPrice - $customerPriceWithDiscount;
				//echo "cooler";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedCooler->getId(), $deviation);
			}
		}
		return array(0, $deviation);
	}

	private function getRamForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {

		$ram_speed = $this->getCmsVar('suggested_pc_default_ram_speed_category_id');
		list($ram_capacity_variant1, $ram_capacity_variant2) = explode(',', $this->getCmsVar('suggested_pc_default_rams_capacity_categories_ids'));

		$ramRatio = floatval($this->getRamRatioForSuggestedPc($gaming, $onlyCase));
		if ($ramRatio > 0) {
			$targetPrice = $customerPriceInUsd * $ramRatio;
			$ram_type = $this->pccm->getMbCorrespondingRamType($selectedMotherboard);
			$requiredCategoriesFormulasArray = array(CategoriesConstants::RAM_KIT_COUNT_1, 'and', $ram_type, 'and', $ram_speed, 'and', '(', $ram_capacity_variant1, 'or', $ram_capacity_variant2, ')');
			$rams = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedRam, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $rams);
			if ($selctedRam !== null) {
				$deviation -= $targetPrice - $customerPriceWithDiscount;
				//echo "ram";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedRam->getId(), $deviation);
			}
		}
		return array(0, $deviation);
	}

	private function getHddForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		list($hdd_capacity_variant1, $hdd_capacity_variant2) = explode(',', $this->getCmsVar('suggested_pc_default_hdds_capacity_categories_ids'));
		$hddRatio = floatval($this->getHddRatioForSuggestedPc($gaming, $onlyCase));
		if ($hddRatio > 0) {
			$targetPrice = $customerPriceInUsd * $hddRatio;
			$requiredCategoriesFormulasArray = array($hdd_capacity_variant1, 'or', $hdd_capacity_variant2);
			$hdds = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedHdd, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $hdds);
			if ($selctedHdd !== null) {
				$deviation -= $targetPrice - $customerPriceWithDiscount;
				//echo "hdd";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedHdd->getId(), $deviation);
			}
		}
		return array(0, $deviation);
	}

	private function getOptForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		$optRatio = floatval($this->getOptRatioForSuggestedPc($gaming, $onlyCase));
		if ($optRatio > 0) {
			$targetPrice = $customerPriceInUsd * $optRatio;
			$requiredCategoriesFormulasArray = array(CategoriesConstants::OPTICAL_DRIVE_SATA, 'and', CategoriesConstants::OPTICAL_DRIVE_TYPE_DVD);
			$opts = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedOpt, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $opts);
			if ($selctedOpt !== null) {
				$deviation -= $targetPrice - $customerPriceWithDiscount;
				//echo "opt";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedOpt->getId(), $deviation);
			}
		}
		return array(0, $deviation);
	}

	private function getMonitorForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {

		if (!$onlyCase) {
			$monitorRatio = floatval($this->getMonitorRatioForSuggestedPc($gaming, $onlyCase));
			if ($monitorRatio > 0) {
				$targetPrice = $customerPriceInUsd * $monitorRatio;
				$requiredCategoriesFormulasArray = array(CategoriesConstants::MONITOR);
				$monitors = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
				list($selctedMonitor, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $monitors);
				if ($selctedMonitor !== null) {
					$deviation -= $targetPrice - $customerPriceWithDiscount;
					//echo "monitor";
					//var_dump($targetPrice,$customerPriceWithDiscount, $deviation);
					return array($selctedMonitor->getId(), $deviation);
				}
			}
		}
		return array(0, $deviation);
	}

	private function getGraphicsForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		if ($gaming) {
			$graphicsRatio = floatval($this->getGraphicsRatioForSuggestedPc($gaming, $onlyCase));
			if ($graphicsRatio > 0) {
				$targetPrice = $customerPriceInUsd * $graphicsRatio;
				$requiredCategoriesFormulasArray = array(CategoriesConstants::VIDEO_INTERFACE_PCI_EXPRESS);
				$graphicses = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
				list($selctedGraphics, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $graphicses);
				if ($selctedGraphics !== null) {
					$deviation -= $targetPrice - $customerPriceWithDiscount;
					//echo "graphics";
					//var_dump($targetPrice,$customerPriceWithDiscount, $deviation);
					return array($selctedGraphics->getId(), $deviation);
				}
			}
		}
		return array(0, $deviation);
	}

	private function getPowerForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		$powerRatio = floatval($this->getPowerRatioForSuggestedPc($gaming, $onlyCase));
		if ($powerRatio > 0) {
			$targetPrice = $customerPriceInUsd * $powerRatio;
			$requiredCategoriesFormulasArray = array(CategoriesConstants::POWER);
			$powers = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
			list($selctedPower, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $powers);
			if ($selctedPower !== null) {
				$deviation -= $targetPrice - $customerPriceWithDiscount;
				//echo "power";
				//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
				return array($selctedPower->getId(), $deviation);
			}
		}
		return array(0, $deviation);
	}

	private function getKeyboardForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		if (!$onlyCase) {
			$keyboardRatio = floatval($this->getKeyboardRatioForSuggestedPc($gaming, $onlyCase));
			if ($keyboardRatio > 0) {
				$targetPrice = $customerPriceInUsd * $keyboardRatio;
				$requiredCategoriesFormulasArray = array(CategoriesConstants::KEYBOARD);
				$keyboards = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
				list($selctedKeyboard, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $keyboards);
				if ($selctedKeyboard !== null) {
					$deviation -= $targetPrice - $customerPriceWithDiscount;
					//echo "keyboard";
					//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
					return array($selctedKeyboard->getId(), $deviation);
				}
			}
		}
		return array(0, $deviation);
	}

	private function getMouseForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		if (!$onlyCase) {
			$mouseRatio = floatval($this->getKeyboardRatioForSuggestedPc($gaming, $onlyCase));
			if ($mouseRatio > 0) {
				$targetPrice = $customerPriceInUsd * $mouseRatio;
				$requiredCategoriesFormulasArray = array(CategoriesConstants::MOUSE);
				$mouses = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
				list($selctedMouse, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $mouses);
				if ($selctedMouse !== null) {
					$deviation -= $targetPrice - $customerPriceWithDiscount;
					//echo "mouse";
					//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
					return array($selctedMouse->getId(), $deviation);
				}
			}
		}
		return array(0, $deviation);
	}

	private function getSpeakerForSuggestedPc($customerPriceInUsd, $deviation, $gaming, $onlyCase, $selectedMotherboard) {
		if (!$onlyCase) {
			$speakerRatio = floatval($this->getSpeakerRatioForSuggestedPc($gaming, $onlyCase));
			if ($speakerRatio > 0) {
				$targetPrice = $customerPriceInUsd * $speakerRatio;
				$requiredCategoriesFormulasArray = array(CategoriesConstants::SPEAKER);
				$speakers = $this->itemManager->getPccItemsByCategoryFormula($this->user->getId(), $this->user->getLevel(), $requiredCategoriesFormulasArray, null, 0, 200);
				list($selctedSpeaker, $customerPriceWithDiscount) = $this->findNearestComponentByPrice($targetPrice, $deviation, $speakers);
				if ($selctedSpeaker !== null) {
					$deviation -= $targetPrice - $customerPriceWithDiscount;
					//echo "speaker";
					//var_dump($targetPrice, $customerPriceWithDiscount, $deviation);
					return array($selctedSpeaker->getId(), $deviation);
				}
			}
		}
		return array(0, $deviation);
	}

	private function getCaseRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[0]) / 100;
	}

	private function getMbRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[1]) / 100;
	}

	private function getCpuRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[2]) / 100;
	}

	private function getCoolerRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[3]) / 100;
	}

	private function getRamRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[4]) / 100;
	}

	private function getHddRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[5]) / 100;
	}

	private function getOptRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[6]) / 100;
	}

	private function getMonitorRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[7]) / 100;
	}

	private function getGraphicsRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[8]) / 100;
	}

	private function getPowerRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[9]) / 100;
	}

	private function getKeyboardRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[10]) / 100;
	}

	private function getMouseRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[11]) / 100;
	}

	private function getSpeakerRatioForSuggestedPc($gaming, $onlyCase) {
		$componentsRatio = $this->getComponentsRatioArrayByFilter($gaming, $onlyCase);
		return floatval($componentsRatio[12]) / 100;
	}

	private function getComponentsRatioArrayByFilter($gaming, $onlyCase) {
		if ($onlyCase) {
			if ($gaming) {
				return $this->gaming_pc_components_ratio_only_case;
			} else {
				return $this->office_pc_components_ratio_only_case;
			}
		} else {
			if ($gaming) {
				return $this->gaming_pc_components_ratio;
			} else {
				return $this->office_pc_components_ratio;
			}
		}
	}

	public function getMapper() {
		return null;
	}

}

?>