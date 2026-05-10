@php
$cfg = [
    'Completed' => ['class'=>'b-completed','dot'=>'#16a34a','icon'=>'bi-check-circle-fill'],
    'Ongoing'   => ['class'=>'b-ongoing',  'dot'=>'#2563eb','icon'=>'bi-arrow-repeat'],
    'Proposed'  => ['class'=>'b-proposed', 'dot'=>'#ca8a04','icon'=>'bi-hourglass-split'],
];
$c = $cfg[$status] ?? ['class'=>'b-proposed','dot'=>'#ca8a04','icon'=>'bi-circle'];
@endphp
<span class="pill {{ $c['class'] }}">
    <i class="bi {{ $c['icon'] }}" style="font-size:9px;"></i>
    {{ $status }}
</span>
