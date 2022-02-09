<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
umask(0);
require '../app/Mage.php';
Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
require_once(__DIR__ . '/_common.inc.php');
$imported     = 1;
$result       = $client->GetEinamiejiLikuciai();
$result_array = object2array(simplexml_load_string($result->Data->any));
foreach ($result_array as $key => $value) {
    foreach ($value as $k => $v) {
        foreach ($v as $id => $pro) {
            //if($imported == 1){
            $sku        = trim($pro["preke"]);
            $product_id = Mage::getModel("catalog/product")->getIdBySku($sku);
            if ($product_id) {
              if ($pro['sandelis'] == 'PAGRINDIN') //check if it is PAGRINDIN
              {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
                if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {

                  print_r($pro); die;
                  $qty = $pro["kiekis"];
                  $stockItem->setQty($qty);
                  $stockItem->setIsInStock((int) ($qty > 0));
                  try {
                      $stockItem->save();
                  } catch (Exception $e) {
                      Mage::log('FVS qty error: ' . $e);
                  }
                }
                Mage::log($imported . "==> " . $pro["preke"] . "==" . $pro["kiekis"], null, "dev.log");
                $imported++;
              }
            }
            //}
        }
    }
}
echo "Imported:-" . $imported;
echo "<br>";
die("Data updated successfully.");
?>
