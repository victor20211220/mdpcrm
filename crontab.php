<?php
  $curl_handle = curl_init();
  curl_setopt($curl_handle,CURLOPT_URL,'https://my.mdpcrm.com/api/sync_email');
  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,20000000);
  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,0);
  curl_exec($curl_handle);
  curl_close($curl_handle);
  die;
