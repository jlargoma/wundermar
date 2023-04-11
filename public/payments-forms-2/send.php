<?php
  $url = $_POST['url'];
  $dni = $_POST['dni'];
  $json = json_encode($dni);
  $curl = curl_init();
  curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $json,
      CURLOPT_CONNECTTIMEOUT => 3,
  ));
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  if ($err) {
    echo 'error';
  } else {
    echo $response;
  }
