$path='c:/xampp/htdocs/Rm1/TODO.md'
$lines=Get-Content -LiteralPath $path
$start=118;$end=132
for($i=$start;$i -le $end;$i++){
  if($i -lt 1 -or $i -gt $lines.Count){continue}
  '{0,4}: {1}' -f $i,$lines[$i-1]
}

