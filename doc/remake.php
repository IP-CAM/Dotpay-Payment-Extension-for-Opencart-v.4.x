#!/usr/bin/php

<?php

# здесь я храню свой код плагина
$DIR='/home/work/RUST/STORE/docker/work/dotpay';

exec("sudo chown -R lleo:lleo '".$DIR."'");
exec("chmod -R a+rwX,u+w,g-w,o-w '".$DIR."'");

// exit;

# так будет называться файл инсталлятора
$FILE='dotpay.ocmod.zip';


# здесь бэкапы
$BAK='/home/work/RUST/STORE/docker/work/backup';

$ZIP=$DIR.'/../'.$FILE;
unlink($ZIP); if(is_file($ZIP)) die('err');

# не забываем сделать чтобы монтировалось хорошо: chmod -R a+rwX /home/work/RUST/STORE/docker/work/dotpay

# в файле конфигурации меняем версию
$cf=$DIR.'/install.json';
$s=file_get_contents($cf);
$s=preg_replace_callback("/(\"version\"\:\s*\"\d+\.)(\d+)(\")/si",function($t){	$t[2]++;
	echo "\n".$t[1].$t[2].$t[3]."\n";
	return $t[1].$t[2].$t[3];
    },$s);
file_put_contents($cf,$s);

exec("cd '".$DIR."' ; zip -r '".$ZIP."' ./");

exec("chmod a+rw '".$ZIP."'");


copy($ZIP,$BAK."/BAK_".date("Y-m-d_H_i_s").".zip");

echo "\n--------------------------";
echo "\n  opencart:";
echo "\n    volumes:";
echo "\n      - '".$ZIP.":/bitnami/opencart_storage/marketplace/".$FILE."'";
echo "\n      - '".$DIR.":/opt/bitnami/opencart/extension/dotpay'";
echo "\n--------------------------\n\n";

?>