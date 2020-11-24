<?php

$currentCode = substr($_POST["field_values"], -5, 4);
$nextLocation = "";
if ($currentCode == "AA01") {
  $nextLocation = "AA08";
} else if ($currentCode == "AA08") {
  $nextLocation = "AA01";
} else {
  $nextLocation = $currentCode;
}
curl_setopt_array($ch = curl_init(), array(
  CURLOPT_URL => "https://api.pushover.net/1/messages.json",
  CURLOPT_POSTFIELDS => array(
  "token" => "aUW26RxWMLHWcMwGsoL185ETNbc3on",
  "user" => "uSRxDBrnA4Dor2jVJqxdrRL4xKiDN8",
  "message" => "Reader: ".$_POST["reader_name"]."\nGo to: ".$nextLocation,
)));
curl_exec($ch);
curl_close($ch);
?>

success