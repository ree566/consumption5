<?php
/*
 * Set the host address first before begin send mail jobs.
 * host: Relay.advantech.com.tw
 * 倉庫管理Add: 訂購一定數量可統一發mail(用schedule一天一次or使用者自行點選)
 */
$to ="Wei.Cheng@advantech.com.tw"; //收件者
$subject = "test"; //信件標題
$msg = "smtp發信測試";//信件內容
$headers='MIME-Version: 1.0'."\r\n";
$headers.='Content-type: text/html; charset=UTF-8'."\r\n"; //設定為html郵件
$headers = "From: admin@your.com"; //寄件者

if(mail("$to", "$subject", "$msg", "$headers")):
    echo "信件已經發送成功。";//寄信成功就會顯示的提示訊息
else:
    echo "信件發送失敗！";//寄信失敗顯示的錯誤訊息
endif;
