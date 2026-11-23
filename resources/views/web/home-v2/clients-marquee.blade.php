{{-- Brands marquee: two endless rows of logo tiles (row 1 slides right→left, row 2 left→right; pauses on hover).
     Shared by home, about, services landing and every service page. The brand list is config/clients.php via v2_clients();
     the first (top) row holds the priority brands from config/clients_featured.php, the second row everything else.
     Include after the helpers are loaded:  @include('web.home-v2.clients-marquee')
     The second pass of every row is a visual duplicate for the endless loop: aria-hidden, same alt text so crawlers see no
     image without alt. --}}
@php
	require_once resource_path('views/web/home-v2/helpers.php');
	$cmClients = v2_clients();
	// row 1 = the priority brands in the order set in config/clients_featured.php; row 2 = every other brand (order of config/clients.php)
	$cmRows = [[], []];
	foreach ((array) config('clients_featured', []) as $cmNo) { if (isset($cmClients[$cmNo])) $cmRows[0][$cmNo] = $cmClients[$cmNo]; }
	foreach ($cmClients as $cmNo => $cmBrand) { if (!isset($cmRows[0][$cmNo])) $cmRows[1][$cmNo] = $cmBrand; }
	// the rows have different lengths: scale each row's loop time with its tile count so both move at about the same speed
	$cmSecPerTile = [3.6, 4.0];
@endphp
<div class="hv2-marquee-wrap" role="group" aria-label="Our clients — {{ count($cmClients) }} brands" data-reveal="up" data-spotlight>
	@foreach($cmRows as $r => $row)
		<div class="hv2-marquee {{ $r === 1 ? 'hv2-marquee--reverse' : '' }}">
			<div class="hv2-marquee-track" style="animation-duration: {{ (int) round(max(1, count($row)) * $cmSecPerTile[$r]) }}s">
				@for($dup = 0; $dup < 2; $dup++)
					@foreach($row as $i => $brand)
						<div class="hv2-brand hv2-brand--lg" style="--n: {{ $loop->index }}" @if($dup) aria-hidden="true" @endif>
							<div class="hv2-brand-logo hv2-media hv2-media--logo">
								<img src="{{ $brand['src'] }}" alt="{{ $brand['alt'] }}" loading="lazy" width="300" height="300">
							</div>
						</div>
					@endforeach
				@endfor
			</div>
		</div>
	@endforeach
</div>
