{{-- The four contact cards (Location, Email, Phone, Work Hours), shared by the Contact and About pages.
     All facts come from config/business.php. @include('web.home-v2.contact-cards', ['cardClass' => 'ctv-card'])  (or 'abv-cc')
     $cardClass is the page's own card class, so each page keeps its own styling. Output goes inside the page's card grid. --}}
@php
	require_once resource_path('views/web/home-v2/helpers.php');
	$ccClass = $cardClass ?? 'ctv-card';
	$ccHours = v2_biz('hours_text', []);
@endphp
<div class="{{ $ccClass }}" data-tilt data-tilt-max="4" data-reveal="up" data-delay="1">
	<i class="bi bi-pin-map" aria-hidden="true"></i>
	<div><p class="working-title">Location</p><p>{{ v2_biz('address_line') }}</p></div>
</div>
<div class="{{ $ccClass }}" data-tilt data-tilt-max="4" data-reveal="up" data-delay="2">
	<i class="bi bi-envelope-at" aria-hidden="true"></i>
	<div><p class="working-title">Email</p><p><strong class="email-slot">{{ v2_biz('email') }}</strong> <button type="button" class="copy-email" data-copy="{{ v2_biz('email') }}"><span class="bi bi-copy"></span>&nbsp;&nbsp;Copy</button></p></div>
</div>
<div class="{{ $ccClass }}" data-tilt data-tilt-max="4" data-reveal="up" data-delay="3">
	<i class="bi bi-telephone" aria-hidden="true"></i>
	<div><p class="working-title">Phone</p><p class="mb-0">TEL: <a href="tel:{{ v2_biz('telephone') }}" class="webCellPhone">{{ v2_biz('telephone_display') }}</a></p><p class="mb-0">Cell: <a href="tel:{{ v2_biz('phone') }}" class="webCellPhone">{{ v2_biz('phone_display') }}</a></p></div>
</div>
<div class="{{ $ccClass }}" data-tilt data-tilt-max="4" data-reveal="up" data-delay="4">
	<i class="bi bi-clock" aria-hidden="true"></i>
	<div><p class="working-title">Work Hours</p><p><strong>{{ $ccHours[0] ?? '' }}</strong> <br>{{ $ccHours[1] ?? '' }}</p></div>
</div>
