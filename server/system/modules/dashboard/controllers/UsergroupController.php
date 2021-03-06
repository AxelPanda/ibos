<?php

class DashboardUsergroupController extends DashboardBaseController
{
    public function actionIndex()
    {
        $formSubmit = EnvUtil::submitCheck("userGroupSubmit");

        if ($formSubmit) {
            $groups = $_POST["groups"];
            $newGroups = (isset($_POST["newgroups"]) ? $_POST["newgroups"] : array());
            $groupNewAdd = DashboardUtil::arrayFlipKeys($newGroups);

            foreach ($groupNewAdd as $k => $v) {
                if (!$v["title"]) {
                    unset($groupNewAdd[$k]);
                } elseif (!$v["creditshigher"]) {
                    $this->error(Ibos::lang("Usergroups update creditshigher invalid"));
                }
            }

            $groupNewKeys = array_keys($groups);
            $maxGroupId = max($groupNewKeys);

            foreach ($groupNewAdd as $k => $v) {
                $groups[$k + $maxGroupId + 1] = $v;
            }

            $orderArray = array();

            if (is_array($groups)) {
                foreach ($groups as $id => $group) {
                    if (($id == 0) && (!$group["title"] || ($group["creditshigher"] == ""))) {
                        unset($groups[$id]);
                    } else {
                        $orderArray[$group["creditshigher"]] = $id;
                    }
                }
            }

            if (empty($orderArray) || (0 <= min(array_flip($orderArray)))) {
                $this->error(Ibos::lang("Usergroups update credits invalid"));
            }

            ksort($orderArray);
            $rangeArray = array();
            $lowerLimit = array_keys($orderArray);

            for ($i = 0; $i < count($lowerLimit); $i++) {
                $rangeArray[$orderArray[$lowerLimit[$i]]] = array("creditshigher" => isset($lowerLimit[$i - 1]) ? $lowerLimit[$i] : -999999999, "creditslower" => isset($lowerLimit[$i + 1]) ? $lowerLimit[$i + 1] : 999999999);
            }

            foreach ($groups as $id => $group) {
                $creditshigherNew = $rangeArray[$id]["creditshigher"];
                $creditslowerNew = $rangeArray[$id]["creditslower"];

                if ($creditshigherNew == $creditslowerNew) {
                    $this->error(Ibos::lang("Usergroups update credits duplicate"));
                }

                if (in_array($id, $groupNewKeys)) {
                    UserGroup::model()->modify($id, array("title" => $group["title"], "creditshigher" => $creditshigherNew, "creditslower" => $creditslowerNew));
                } else {
                    if ($group["title"] && ($group["creditshigher"] != "")) {
                        $data = array("title" => $group["title"], "creditshigher" => $creditshigherNew, "creditslower" => $creditslowerNew);
                        UserGroup::model()->add($data);
                    }
                }
            }

            $removeId = $_POST["removeId"];

            if (!empty($removeId)) {
                UserGroup::model()->deleteById($removeId);
            }

            CacheUtil::update(array("UserGroup"));
            $this->success(Ibos::lang("Save succeed", "message"));
        } else {
            $groups = UserGroup::model()->fetchAll(array("order" => "creditshigher"));
            $data = array("data" => $groups);
            $this->render("index", $data);
        }
    }
}
