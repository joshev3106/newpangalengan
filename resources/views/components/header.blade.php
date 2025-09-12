@props([
  // struktur: ['high','medium','low','not','total','avg']
  'stats' => null,
  // jumlah puskesmas
  'pkCount' => null,
])

@php
  $s  = $stats   ?? ['high'=>0,'medium'=>0,'low'=>0,'not'=>0,'total'=>0,'avg'=>0];
  $pk = $pkCount ?? 0;
@endphp

