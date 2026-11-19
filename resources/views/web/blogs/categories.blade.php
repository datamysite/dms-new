@extends('web.includes.master')
@section('metaAddition')
@if(!empty($tags))
<meta name="robots" content="noindex, follow">
@endif
@endsection
@section('content')

	<main class="main cat-index-page">

	    <!-- Page Title -->
          <div class="page-title dark-background" data-aos="fade" style="background-image: url('{{URL::to('/public/assets/img/title/'.$titleImg)}}');">
            <div class="container">
              <h1>{{$title}} {{@$page}}</h1>
              <nav class="breadcrumbs">
                <ol>
                  <li><a href="{{route('home')}}">Home</a></li>
                  <li><a href="{{route('blogs')}}">Blogs</a></li>
                  <li class="current">{{$title}}</li>
                </ol>
              </nav>
            </div>
          </div><!-- End Page Title -->

	    <section id="category-index" class="cat-index section">

	      <div class="container">

	        <div class="cat-index__controls" data-aos="fade-up" data-aos-delay="80">
	          <div class="cat-index__search">
	            <input type="text" id="catSearch" placeholder="Search categories…" aria-label="Search categories">
	          </div>
	          <div class="cat-index__toggle" role="group" aria-label="View">
	            <button type="button" class="cat-index__toggle-btn is-active" data-view="az">A&ndash;Z index</button>
	            <button type="button" class="cat-index__toggle-btn" data-view="popular">Most published</button>
	          </div>
	        </div>

	        <p class="cat-index__count" id="catCount" aria-live="polite">Showing {{ $categories->count() }} of {{ $categories->count() }} categories</p>

	        <div class="cat-index__layout">

	          <div class="cat-index__main">

	            @php
	              $grouped = $categories->sortBy(function($c){ return Str::lower($c->name); })
	                                     ->groupBy(function($c){ return Str::upper(Str::substr($c->name, 0, 1)); });
	            @endphp

	            <div class="index-groups" id="azView" data-aos="fade-up" data-aos-delay="120">
	              @foreach($grouped as $letter => $group)
	                <div class="letter-group" id="letter-{{ $letter }}">
	                  <div class="letter-group__marker">{{ $letter }}</div>
	                  <div class="letter-group__rows">
	                    @foreach($group as $cat)
	                      <a href="{{URL::to('/blogs/'.$cat->slug)}}" class="index-row" data-name="{{ strtolower($cat->name) }}">
	                        <span class="index-row__name">{{ $cat->name }}</span>
	                        <span class="index-row__leader" aria-hidden="true"></span>
	                        <span class="index-row__count">{{ $cat->blogs_count }} {{ Str::plural('post', $cat->blogs_count) }}</span>
	                        <span class="index-row__arrow" aria-hidden="true">&rarr;</span>
	                      </a>
	                    @endforeach
	                  </div>
	                </div>
	              @endforeach
	            </div>

	            <div class="index-groups" id="popularView" hidden data-aos="fade-up" data-aos-delay="120">
	              @foreach($categories->sortByDesc('blogs_count')->values() as $i => $cat)
	                <a href="{{URL::to('/blogs/'.$cat->slug)}}" class="index-row index-row--ranked" data-name="{{ strtolower($cat->name) }}">
	                  <span class="index-row__rank">{{ sprintf('%02d', $i + 1) }}</span>
	                  <span class="index-row__name">{{ $cat->name }}</span>
	                  <span class="index-row__leader" aria-hidden="true"></span>
	                  <span class="index-row__count">{{ $cat->blogs_count }} {{ Str::plural('post', $cat->blogs_count) }}</span>
	                  <span class="index-row__arrow" aria-hidden="true">&rarr;</span>
	                </a>
	              @endforeach
	            </div>

	            <p class="cat-index__empty" id="catEmpty" hidden>No categories match your search. Try a different word.</p>

	          </div>

	          <aside class="cat-index__rail" id="azRail" aria-label="Jump to letter">
	            @foreach(range('A','Z') as $letter)
	              @if(isset($grouped[$letter]))
	                <a href="#letter-{{ $letter }}">{{ $letter }}</a>
	              @else
	                <span>{{ $letter }}</span>
	              @endif
	            @endforeach
	          </aside>

	        </div>

	      </div>

	    </section>

	</main>

<style>
/* ---------------------------------------------------------------
   All-categories page — editorial index / table-of-contents style
   Re-themed to the site's own palette: white background, dark teal
   headings/markers, coral accent (matches "Read More" / CTA buttons
   / active pagination), clean sans-serif type throughout.
----------------------------------------------------------------*/
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap');

.cat-index-page{ scroll-behavior:smooth; }

.cat-index{
  --bg:#ffffff;
  --ink:#22302c;
  --ink-soft:#6b7873;
  --teal:#0f3d37;
  --coral:#e2543f;
  --line:rgba(15,61,55,0.12);
  background:var(--bg);
  color:var(--ink);
  padding-top:70px;
  padding-bottom:90px;
  font-family:'Inter',sans-serif;
}

/* Intro */
.cat-index__intro{ max-width:640px; margin:0 auto 30px; }
.cat-index__eyebrow{
  font-family:'Poppins', sans-serif;
  font-weight:600;
  font-size:0.78rem;
  letter-spacing:0.08em;
  text-transform:uppercase;
  color:var(--coral);
  margin-bottom:12px;
}
.cat-index__lede{
  font-family:'Poppins', sans-serif;
  font-weight:500;
  font-size:1.4rem;
  line-height:1.5;
  color:var(--teal);
}

/* Controls */
.cat-index__controls{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:20px;
  flex-wrap:wrap;
  max-width:900px;
  margin:0 auto 8px;
  padding-bottom:18px;
  border-bottom:1px solid var(--line);
}
.cat-index__search{ flex:1; min-width:220px; }
.cat-index__search input{
  width:100%;
  background:#f7f7f5;
  border:1px solid var(--line);
  border-radius:10px;
  color:var(--ink);
  font-family:'Inter',sans-serif;
  font-size:0.95rem;
  padding:11px 14px;
  outline:none;
  transition:border-color .2s ease, box-shadow .2s ease;
}
.cat-index__search input::placeholder{ color:var(--ink-soft); }
.cat-index__search input:focus{
  border-color:var(--coral);
  box-shadow:0 0 0 3px rgba(226,84,63,0.12);
  background:#fff;
}

.cat-index__toggle{ display:flex; gap:4px; background:#f7f7f5; border:1px solid var(--line); border-radius:100px; padding:4px; }
.cat-index__toggle-btn{
  border:none;
  background:transparent;
  color:var(--ink-soft);
  font-family:'Poppins', sans-serif;
  font-weight:500;
  font-size:0.8rem;
  padding:8px 16px;
  border-radius:100px;
  cursor:pointer;
  transition:all .2s ease;
}
.cat-index__toggle-btn.is-active{ background:var(--coral); color:#fff; }
.cat-index__toggle-btn:not(.is-active):hover{ color:var(--teal); }

.cat-index__count{
  max-width:900px;
  margin:16px auto 26px;
  font-family:'Inter',sans-serif;
  font-size:0.85rem;
  color:var(--ink-soft);
}

/* Layout: index + rail */
.cat-index__layout{
  display:grid;
  grid-template-columns:1fr 40px;
  gap:32px;
  max-width:900px;
  margin:0 auto;
}

/* Letter groups */
.letter-group{ display:grid; grid-template-columns:60px 1fr; gap:20px; padding:24px 0; border-bottom:1px solid var(--line); }
.letter-group:first-child{ padding-top:0; }
.letter-group__marker{
  font-family:'Poppins', sans-serif;
  font-size:2rem;
  font-weight:700;
  color:var(--teal);
  line-height:1;
}
.letter-group__rows{ display:flex; flex-direction:column; gap:2px; }

/* Index rows (dot-leader table-of-contents style) */
.index-row{
  display:flex;
  align-items:baseline;
  gap:12px;
  text-decoration:none;
  color:var(--ink);
  padding:10px 8px;
  border-radius:8px;
  transition:background .2s ease, color .2s ease;
}
.index-row:hover{ background:rgba(226,84,63,0.06); color:var(--coral); }
.index-row__name{
  font-family:'Poppins', sans-serif;
  font-size:1.02rem;
  font-weight:500;
  white-space:nowrap;
}
.index-row__leader{
  flex:1;
  border-bottom:2px dotted var(--line);
  position:relative;
  top:-5px;
  margin:0 2px;
}
.index-row__count{
  font-family:'Inter',sans-serif;
  font-size:0.8rem;
  color:var(--ink-soft);
  white-space:nowrap;
}
.index-row__arrow{
  font-size:0.9rem;
  opacity:0;
  transform:translateX(-4px);
  transition:opacity .2s ease, transform .2s ease;
  color:var(--coral);
}
.index-row:hover .index-row__arrow{ opacity:1; transform:translateX(0); }
.index-row:hover .index-row__count{ color:var(--coral); }

.index-row--ranked .index-row__rank{
  font-family:'Poppins', sans-serif;
  font-weight:700;
  font-size:0.85rem;
  color:var(--coral);
  width:26px;
  flex-shrink:0;
}

.cat-index__empty{ text-align:center; color:var(--ink-soft); padding:50px 0; font-size:0.95rem; }

/* Alphabet rail */
.cat-index__rail{
  position:sticky;
  top:100px;
  align-self:start;
  display:flex;
  flex-direction:column;
  gap:5px;
  font-family:'Poppins', sans-serif;
  font-weight:600;
  font-size:0.78rem;
}
.cat-index__rail a{
  color:var(--ink-soft);
  text-decoration:none;
  transition:color .2s ease;
}
.cat-index__rail a:hover{ color:var(--coral); }
.cat-index__rail span{ color:rgba(15,61,55,0.18); }

/* Responsive */
@media (max-width:768px){
  .cat-index__layout{ grid-template-columns:1fr; }
  .cat-index__rail{ display:none; }
  .letter-group{ grid-template-columns:40px 1fr; gap:12px; }
  .letter-group__marker{ font-size:1.5rem; }
  .index-row{ flex-wrap:wrap; }
  .index-row__leader{ display:none; }
  .cat-index__lede{ font-size:1.2rem; }
}
</style>

<script>
(function(){
  var search   = document.getElementById('catSearch');
  var count    = document.getElementById('catCount');
  var empty    = document.getElementById('catEmpty');
  var azView   = document.getElementById('azView');
  var popView  = document.getElementById('popularView');
  var rail     = document.getElementById('azRail');
  var toggleBtns = document.querySelectorAll('.cat-index__toggle-btn');
  if(!azView) return;

  var total = azView.querySelectorAll('.index-row').length;

  function activeContainer(){
    return azView.hidden ? popView : azView;
  }

  function applySearch(){
    var q = (search.value || '').trim().toLowerCase();
    var visible = 0;

    [azView, popView].forEach(function(container){
      container.querySelectorAll('.index-row').forEach(function(row){
        var match = row.getAttribute('data-name').indexOf(q) !== -1;
        row.style.display = match ? '' : 'none';
        if(match && container === activeContainer()) visible++;
      });
      container.querySelectorAll('.letter-group').forEach(function(group){
        var anyVisible = Array.prototype.some.call(
          group.querySelectorAll('.index-row'),
          function(r){ return r.style.display !== 'none'; }
        );
        group.style.display = anyVisible ? '' : 'none';
      });
    });

    count.textContent = 'Showing ' + visible + ' of ' + total + ' categories';
    empty.hidden = visible !== 0;
  }

  function setView(view){
    if(view === 'popular'){
      azView.hidden = true;
      popView.hidden = false;
      rail.style.visibility = 'hidden';
    } else {
      azView.hidden = false;
      popView.hidden = true;
      rail.style.visibility = 'visible';
    }
    applySearch();
  }

  search.addEventListener('input', applySearch);

  toggleBtns.forEach(function(btn){
    btn.addEventListener('click', function(){
      toggleBtns.forEach(function(b){ b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      setView(btn.getAttribute('data-view'));
    });
  });
})();
</script>

@endsection
@section('addScript')
  <script src="{{URL::to('/public')}}/assets/js/aside.js" defer></script>
@endsection