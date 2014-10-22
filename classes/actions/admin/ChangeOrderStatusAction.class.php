<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/OrdersManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/UserSubUsersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeOrderStatusAction extends AdminAction {

	private $ordersManager;
	private $userManager;

	public function service() {

		$only_refresh = $this->secure($_REQUEST["only_refresh"]);
		if ($only_refresh == 1) {
			$jsonArr = array('status' => "ok", "message" => "ok");
			echo json_encode($jsonArr);
			return true;
		}

		$this->ordersManager = OrdersManager::getInstance($this->config, $this->args);
		$this->userManager = UserManager::getInstance($this->config, $this->args);
		$orderId = $this->secure($_REQUEST["order_id"]);
		$orderDto = $this->ordersManager->selectByPK($orderId);
		$status = intval($this->secure($_REQUEST["status"]));
		if (!in_array($status, $this->ordersManager->orderStatusesValues)) {
			$jsonArr = array('status' => "err", "errText" => "unknown order status:'" . $status, "'");
			echo json_encode($jsonArr);
			return false;
		}

		$orderOldStatus = $this->ordersManager->getOrderStatus($orderId);
		$this->ordersManager->setOrderStatus($orderId, $status);
		if ($orderOldStatus == 2/* canceled */ && $status != 2/* canceled */) {
			//then used bonuses should be subtract
			$userDto = $this->getUserByUserOrderId($orderId);
			if ($userDto) {
				$userId = $userDto->getId();
				$usedPoints = $orderDto->getUsedPoints();
				$description = "Order number $orderId canceled. Used points restored!";
				$this->userManager->subtractUserPoints($userId, $usedPoints, $description);
			}
		}


		if ($orderOldStatus != 1/* completed */ && $status == 1/* completed */) {
			$parentId = $this->getUserParentIdByUserOrderId($orderId);

			$totalAmd = $orderDto->getOrderTotalAmd();
			//bonus to parent

			if ($parentId > 0) {
				$isParentVip = $this->userManager->isVip($parentId);
				if ($isParentVip) {
					$vip_user_profit_percent_from_subuser_order = intval($this->getCmsVar('vip_user_profit_percent_from_subuser_order'));
					$orderProfitAmd = $this->ordersManager->calcOrderProfitAmd($orderId);
					$description = $vip_user_profit_percent_from_subuser_order . "% percent from profit of order number " . $orderId . " (" . $orderDto->getCustomerEmail() . "). special for VIP parent users. Order total profit:" . $orderProfitAmd . ' Դր.';
					$points = intval($orderProfitAmd * $vip_user_profit_percent_from_subuser_order / 100);
					$this->userManager->addUserPoints($parentId, $points, $description);
				} else {
					$user_points_from_subusers_purchase_percent = intval($this->getCmsVar('user_profit_percent_from_subuser_order'));
					$orderProfitAmd = $this->ordersManager->calcOrderProfitAmd($orderId);					
					$description = $user_points_from_subusers_purchase_percent . "% percent from profit of order number " . $orderId . " (" . $orderDto->getCustomerEmail() . "). Order total profit:" . $orderProfitAmd . ' Դր.';
                                        $points = intval($orderProfitAmd * $vip_user_profit_percent_from_subuser_order / 100);
					$this->userManager->addUserPoints($parentId, $points, $description);
					
                                        //subuser first order pintont to parent
                                        $userDto = $this->getUserByUserOrderId($orderId);
                                        $ordersCount = $this->ordersManager->getCustomerTotalConfirmedOrdersCount($userDto ->getEmail());
                                        if ($ordersCount  === 1){
                                            $user_points_from_subusers_first_order = $this->getCmsVar('user_points_from_subusers_first_order');
                                            $description = $user_points_from_subusers_first_order .' points from first order of sub user ('.$userDto ->getEmail().')';
                                            $this->userManager->addUserPoints($parentId, $user_points_from_subusers_first_order, $description);
                                        }
				}
			} else {
				//TODO help children
			}
			$this->ordersManager->setOrderDeliverDateTime($orderId, date('Y-m-d H:i:s'));
		}
		if ($orderOldStatus == 1/* completed */ && $status != 1/* completed */) {
			//bonus to parent subtract	
			$parentId = $this->getUserParentIdByUserOrderId($orderId);
			$isParentVip = $this->userManager->isVip($parentId);
			if ($parentId > 0) {
				if ($isParentVip) {
					$vip_user_profit_percent_from_subuser_order = intval($this->getCmsVar('vip_user_profit_percent_from_subuser_order'));
					$orderProfitAmd = $this->ordersManager->calcOrderProfitAmd($orderId);
					$description = "Order number $orderId canceled. Inviter VIP user points reduced. Order total profit:" . $orderProfitAmd . ' Դր. (' . $vip_user_profit_percent_from_subuser_order . "%)";
					$points = intval($orderProfitAmd * $vip_user_profit_percent_from_subuser_order / 100);
					$this->userManager->subtractUserPoints($parentId, $points, $description);
				} else {					
					$user_profit_percent_from_subuser_order = intval($this->getCmsVar('user_profit_percent_from_subuser_order')) / 100;
                                        $orderProfitAmd = $this->ordersManager->calcOrderProfitAmd($orderId);
					$description = "Order number $orderId canceled. Inviter user points reduced. Order total profit:" . $orderProfitAmd . ' Դր. (' . $user_profit_percent_from_subuser_order . "%)";
					$points = intval($orderProfitAmd * $user_profit_percent_from_subuser_order / 100);
					$this->userManager->subtractUserPoints($parentId, $points, $description);
                                        
                                        //subuser first order pintont to parent
                                        $userDto = $this->getUserByUserOrderId($orderId);
                                        $ordersCount = $this->ordersManager->getCustomerTotalConfirmedOrdersCount($userDto ->getEmail());
                                        if ($ordersCount  === 0){
                                            $user_points_from_subusers_first_order = $this->getCmsVar('user_points_from_subusers_first_order');
                                            $description = $user_points_from_subusers_first_order .' points subtracted due to cancel first order of sub user ('.$userDto ->getEmail().')';
                                            $this->userManager->subtractUserPoints($parentId, $user_points_from_subusers_first_order, $description);
                                        }
                                        
				}
			}
		}

		if ($status == 2 /*cancelled*/) {
			$cencel_reason = $this->secure($_REQUEST["cencel_reason"]);
			if (!empty($cencel_reason)) {
				$this->ordersManager->setOrderCancelReasonText($orderId, $cencel_reason);
			}


			//if was NOT CENCELED now it's CENCELED
			//used bonuses should be restore	  
			$userDto = $this->getUserByUserOrderId($orderId);
			if ($userDto) {
				$userId = $userDto->getId();
				$usedPoints = $orderDto->getUsedPoints();
				$description = "User used bonuses restored from order number " . $orderId;
				$this->userManager->addUserPoints($userId, $usedPoints, $description);
			}
		}
		$jsonArr = array('status' => "ok", "message" => "ok");
		echo json_encode($jsonArr);
		return true;
	}

	public function getUserByUserOrderId($orderId) {
		$orderDto = $this->ordersManager->selectByPK($orderId);
		$email = $orderDto->getCustomerEmail();
		$userDto = $this->userManager->getUserByEmail($email);
		return $userDto;
	}

	public function getUserParentIdByUserOrderId($orderId) {
		$userDto = $this->getUserByUserOrderId($orderId);
		if ($userDto) {
			$userId = $userDto->getId();
			$userSubUsersManager = UserSubUsersManager::getInstance($this->config, $this->args);
			return $userSubUsersManager->getUserParentId($userId);
		}
		return 0;
	}

}

?>