@extends('admin.layout.main')
@section('title', 'Chat Dashboard')
@section('content')
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Chat Dashboard</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Chat Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      @verbatim
      <style>
      #cd-root{--ink:#0E443B;--copper:#B06A48;--line:#E4EAE7;--muted:#6B7280;color:#111827;}
      #cd-root *{box-sizing:border-box;}
      #cd-root .cd-controls{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin:0 0 14px;position:relative;}
      #cd-root .cd-ranges{display:flex;gap:6px;flex-wrap:wrap;}
      #cd-root .cd-ranges button{border:1px solid var(--line);background:#fff;color:#374151;font-size:13px;font-weight:600;padding:7px 13px;border-radius:9px;cursor:pointer;transition:background .15s ease,color .15s ease,border-color .15s ease;}
      #cd-root .cd-ranges button:hover{border-color:var(--ink);}
      #cd-root .cd-ranges button.active{background:var(--ink);color:#fff;border-color:var(--ink);}
      #cd-root .cd-live{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--muted);margin-left:auto;}
      #cd-root .cd-dot{width:9px;height:9px;border-radius:50%;background:#22C55E;transition:opacity .8s ease;}
      #cd-root .cd-customwrap{display:flex;align-items:center;gap:8px;flex-wrap:wrap;width:100%;background:#F7F9F8;border:1px solid var(--line);border-radius:10px;padding:10px 12px;margin-top:2px;}
      #cd-root .cd-customwrap.hidden{display:none;}
      #cd-root .cd-customwrap input[type=date]{border:1px solid var(--line);border-radius:7px;padding:6px 9px;font-size:13px;color:#111827;background:#fff;}
      #cd-root .cd-customwrap span{color:var(--muted);font-size:12.5px;}
      #cd-root .cd-apply-btn{border:1px solid var(--ink);background:var(--ink);color:#fff;font-size:12.5px;font-weight:600;padding:6px 14px;border-radius:7px;cursor:pointer;}
      #cd-root .cd-apply-btn:hover{background:#12594E;}
      #cd-root .cd-stagefilter{display:none;align-items:center;gap:8px;font-size:12.5px;color:var(--ink);background:#E7F0EC;border:1px solid #CFE1DA;border-radius:999px;padding:6px 8px 6px 14px;margin:0 0 14px;width:fit-content;}
      #cd-root .cd-stagefilter.show{display:flex;}
      #cd-root .cd-stagefilter button{border:none;background:#fff;color:var(--ink);font-size:11.5px;font-weight:700;padding:4px 10px;border-radius:999px;cursor:pointer;}
      #cd-root .cd-cards{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:18px;}
      @media(max-width:900px){#cd-root .cd-cards{grid-template-columns:repeat(2,1fr);}}
      #cd-root .cd-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:15px 16px;cursor:pointer;transition:transform .15s ease,box-shadow .15s ease,border-color .15s ease;}
      #cd-root .cd-card:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(16,24,40,.08);}
      #cd-root .cd-card.cd-active{border-color:var(--ink);box-shadow:0 0 0 2px rgba(14,68,59,.15);}
      #cd-root .cd-card .cd-label{font-size:11.5px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.05em;}
      #cd-root .cd-card .cd-num{font-size:28px;font-weight:800;margin-top:5px;letter-spacing:-0.02em;}
      #cd-root .cd-card .cd-pct{font-size:12px;color:var(--muted);margin-top:2px;}
      #cd-root .cd-card.cd-hero{background:linear-gradient(135deg,var(--ink),#12594E);color:#fff;}
      #cd-root .cd-card.cd-hero .cd-label,#cd-root .cd-card.cd-hero .cd-pct{color:#B9C7C2;}
      #cd-root .cd-card.cd-hero.cd-active{box-shadow:0 0 0 2px rgba(176,106,72,.55);border-color:var(--copper);}
      #cd-root .cd-panel{background:#fff;border:1px solid var(--line);border-radius:16px;padding:18px 20px;margin-bottom:18px;box-shadow:0 1px 2px rgba(16,24,40,.04);}
      #cd-root .cd-panel h2{font-size:14px;margin:0 0 16px;font-weight:700;}
      #cd-root .cd-hint{font-weight:400;color:var(--muted);font-size:12px;}
      #cd-root .cd-charts-row{display:flex;gap:18px;}
      #cd-root .cd-charts-row .cd-panel{margin-bottom:0;}
      #cd-root .cd-charts-row .cd-panel.cd-trend{flex:2;min-width:0;}
      #cd-root .cd-charts-row .cd-panel.cd-conv{flex:1;min-width:220px;}
      @media(max-width:900px){#cd-root .cd-charts-row{flex-direction:column;}}
      #cd-root .cd-chart-wrap{position:relative;height:260px;}
      #cd-root .cd-chart-wrap.cd-small{height:220px;}
      #cd-root .cd-conv-figure{text-align:center;margin-top:-6px;}
      #cd-root .cd-conv-figure .cd-num{font-size:26px;font-weight:800;color:var(--ink);}
      #cd-root .cd-conv-figure .cd-lbl{font-size:12px;color:var(--muted);margin-top:2px;}
      #cd-root .cd-funnel-row{display:flex;align-items:center;gap:14px;margin-bottom:12px;}
      #cd-root .cd-funnel-row .cd-name{width:150px;flex:none;font-size:13.5px;font-weight:600;}
      #cd-root .cd-track{flex:1;background:#EEF2F0;border-radius:8px;height:30px;overflow:hidden;}
      #cd-root .cd-fill{height:100%;border-radius:8px;background:linear-gradient(90deg,var(--ink),#1C6E60);transition:width .5s ease;min-width:2px;}
      #cd-root .cd-fill.cd-copper{background:linear-gradient(90deg,var(--copper),#9A5638);}
      #cd-root .cd-funnel-row .cd-val{width:120px;flex:none;text-align:right;font-size:13px;color:var(--muted);}
      #cd-root .cd-funnel-row .cd-val b{color:#111827;font-size:15px;}
      #cd-root .cd-feed{max-height:550px;overflow-y:auto;padding-right: 25px;}
      #cd-root .cd-feed .cd-item{display:flex;align-items:center;gap:10px;padding:9px 0;border-top:1px solid var(--line);font-size:13px;}
      #cd-root .cd-feed .cd-item:first-child{border-top:none;}
      #cd-root .cd-badge{font-size:11px;font-weight:700;padding:3px 8px;border-radius:999px;white-space:nowrap;color:#374151;}
      #cd-root .cd-b-loaded{background:#EEF2F0;color:#374151;}
      #cd-root .cd-b-open{background:#E7F0EC;color:#0E443B;}
      #cd-root .cd-b-message{background:#EAF2FF;color:#1E40AF;}
      #cd-root .cd-b-intent{background:#FBEAE0;color:#8A4A2B;}
      #cd-root .cd-b-converted{background:#DCFCE7;color:#166534;}
      #cd-root .cd-feed .cd-who{color:var(--muted);}
      #cd-root .cd-feed .cd-ago{margin-left:auto;color:var(--muted);font-size:12px;}
      #cd-root .cd-empty{color:var(--muted);font-size:13px;font-style:italic;padding:8px 0;}
      #cd-root .cd-cvrow{display:flex;align-items:flex-start;gap:12px;padding:11px 0;border-top:1px solid var(--line);}
      #cd-root .cd-cvrow:first-child{border-top:none;}
      #cd-root .cd-cvrow-main{flex:1;min-width:0;}
      #cd-root .cd-cvrow-top{font-size:13.5px;display:flex;align-items:center;gap:8px;}
      #cd-root .cd-count{color:var(--muted);font-size:12px;font-weight:400;}
      #cd-root .cd-cvrow-prev{color:var(--muted);font-size:12.5px;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
      #cd-root .cd-cvrow-side{display:flex;align-items:center;gap:12px;flex:none;}
      #cd-root .cd-view{border:1px solid var(--ink);background:var(--ink);color:#fff;font-size:12.5px;font-weight:600;padding:7px 13px;border-radius:9px;cursor:pointer;white-space:nowrap;}
      #cd-root .cd-view:hover{background:#12594E;}
      #cd-root .cd-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,32,.55);z-index:9000;align-items:center;justify-content:center;padding:20px;}
      #cd-root .cd-modal{background:#fff;border-radius:16px;width:440px;max-width:100%;max-height:82vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 24px 70px rgba(16,24,40,.4);}
      #cd-root .cd-modal-head{background:var(--ink);color:#fff;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;}
      #cd-root .cd-modal-sub{display:block;font-size:12px;color:#B9C7C2;margin-top:2px;}
      #cd-root .cd-modal-close{background:transparent;border:none;color:#B9C7C2;font-size:20px;cursor:pointer;line-height:1;}
      #cd-root .cd-modal-close:hover{color:#fff;}
      #cd-root .cd-modal-body{padding:16px;overflow-y:auto;background:#fff;}
      #cd-root .cd-loading{color:var(--muted);font-size:13px;text-align:center;padding:24px;}
      #cd-root .cd-msg{display:flex;margin-bottom:10px;}
      #cd-root .cd-msg.cd-user{justify-content:flex-end;}
      #cd-root .cd-bubble{max-width:80%;padding:10px 13px;border-radius:15px;font-size:13.5px;line-height:1.45;word-wrap:break-word;}
      #cd-root .cd-msg.cd-bot .cd-bubble{background:#F2F5F3;color:#15181F;border-bottom-left-radius:5px;}
      #cd-root .cd-msg.cd-user .cd-bubble{background:var(--copper);color:#fff;border-bottom-right-radius:5px;}
      </style>
      @endverbatim

      <div id="cd-root">
        <div class="cd-controls">
          <div class="cd-ranges" id="cd-ranges">
            <button data-range="today">Today</button>
            <button data-range="7d" class="active">7 days</button>
            <button data-range="30d">30 days</button>
            <button data-range="custom">Custom range</button>
          </div>
          <div class="cd-live"><span class="cd-dot" id="cd-dot"></span><span id="cd-updated">connecting…</span></div>

          <div class="cd-customwrap hidden" id="cd-customRange">
            <input type="date" id="cd-fromDate">
            <span>to</span>
            <input type="date" id="cd-toDate">
            <button class="cd-apply-btn" id="cd-applyCustom">Apply</button>
          </div>
        </div>

        <div class="cd-stagefilter" id="cd-stageFilter">
          Showing only visitors who reached: <b id="cd-stageFilterLabel"></b>
          <button id="cd-stageFilterClear">Clear filter</button>
        </div>

        <div class="cd-cards" id="cd-cards"></div>

        <div class="cd-charts-row">
          <div class="cd-panel cd-trend">
            <h2>Visitor activity over time <span class="cd-hint" id="cd-trendHint">— distinct visitors per day, per stage</span></h2>
            <div class="cd-chart-wrap"><canvas id="cd-trendChart"></canvas></div>
          </div>
          <div class="cd-panel cd-conv">
            <h2>Conversion overview</h2>
            <div class="cd-chart-wrap cd-small"><canvas id="cd-convChart"></canvas></div>
            <div class="cd-conv-figure">
              <div class="cd-num" id="cd-convRateNum">0%</div>
              <div class="cd-lbl">of visitors who asked a question converted</div>
            </div>
          </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="cd-panel">
                  <h2>Recent conversations <span class="cd-hint">— latest chat per visitor, with at least 1 message</span></h2>
                  <div id="cd-convlist" class="cd-feed"><div class="cd-empty">Loading…</div></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="cd-panel">
                  <h2>Live activity</h2>
                  <div class="cd-feed" id="cd-feed"></div>
                </div>
            </div>
        </div>

        <div class="cd-overlay" id="cd-cvOverlay">
          <div class="cd-modal">
            <div class="cd-modal-head">
              <div><b>Conversation</b><span class="cd-modal-sub" id="cd-cvSub"></span></div>
              <button class="cd-modal-close" id="cd-cvClose" aria-label="Close">&times;</button>
            </div>
            <div class="cd-modal-body" id="cd-cvBody"></div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>
@endsection

@section('addScript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
(function(){
  const KEY  = @json($key);
  const BASE = location.pathname.replace(/\/+$/,'');   // e.g. /admin/chat-dashboard
  let range = "7d";
  let stage = "";           // "" = all, or one of loaded/opened/asked/intent/converted
  let customFrom = "", customTo = "";
  const $id = id => document.getElementById(id);

  const STAGE_LABELS = {loaded:"Visited page", opened:"Opened chat", asked:"Asked a question", intent:"Reached intent", converted:"Converted"};

  function withKey(u){ return KEY ? (u + (u.includes('?')?'&':'?') + "key=" + encodeURIComponent(KEY)) : u; }
  function rangeParams(){
    const p = new URLSearchParams();
    p.set("range", range);
    if(range === "custom"){ p.set("from", customFrom); p.set("to", customTo); }
    if(stage) p.set("stage", stage);
    return p.toString();
  }
  function qStats(){ return withKey(BASE + "/stats?" + rangeParams()); }
  function qConvs(){ return withKey(BASE + "/conversations?" + rangeParams()); }
  function qConv(sid){ return withKey(BASE + "/conversation?sid=" + encodeURIComponent(sid)); }
  function esc(s){ return String(s==null?"":s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
  function pct(a,b){ return b>0?Math.round(a/b*100):0; }

  const STAGES=[{key:"loaded",name:"Visited page",copper:false},{key:"opened",name:"Opened chat",copper:false},{key:"asked",name:"Asked a question",copper:false},{key:"intent",name:"Reached intent",copper:true},{key:"converted",name:"Converted",copper:true}];

  function renderCards(f, convRate){
    const top=f.loaded||0;
    const cards=[
      {key:"loaded", label:"Visitors",num:f.loaded,pct:""},
      {key:"opened", label:"Opened",num:f.opened,pct:pct(f.opened,top)+"% of visitors"},
      {key:"asked", label:"Asked",num:f.asked,pct:pct(f.asked,top)+"% of visitors"},
      {key:"intent", label:"Intent",num:f.intent,pct:pct(f.intent,top)+"% of visitors"}
    ];
    let html=cards.map(c=>`<div class="cd-card ${stage===c.key?'cd-active':''}" data-stage="${c.key}"><div class="cd-label">${c.label}</div><div class="cd-num">${c.num}</div><div class="cd-pct">${c.pct}</div></div>`).join("");
    html+=`<div class="cd-card cd-hero ${stage==='converted'?'cd-active':''}" data-stage="converted"><div class="cd-label">Converted</div><div class="cd-num">${f.converted}</div><div class="cd-pct">${convRate}% of those who asked</div></div>`;
    $id("cd-cards").innerHTML=html;
  }
  function renderFunnel(f){
    /*const max=Math.max(f.loaded,1);
    $id("cd-funnel").innerHTML=STAGES.map(s=>{
      const v=f[s.key]||0; const w=Math.max((v/max)*100, v>0?3:0);
      return `<div class="cd-funnel-row"><div class="cd-name">${s.name}</div><div class="cd-track"><div class="cd-fill ${s.copper?'cd-copper':''}" style="width:${w}%"></div></div><div class="cd-val"><b>${v}</b> &nbsp;·&nbsp; ${pct(v,f.loaded)}%</div></div>`;
    }).join("");*/
  }
  function badgeClass(ev){return ev==="loaded"?"cd-b-loaded":ev==="open"?"cd-b-open":ev==="message"?"cd-b-message":ev==="connect_shown"?"cd-b-intent":ev==="converted"?"cd-b-converted":"cd-b-loaded";}
  function badgeLabel(ev){return ev==="loaded"?"visited":ev==="open"?"opened":ev==="message"?"asked":ev==="connect_shown"?"intent":ev==="converted"?"converted":ev;}
  function renderFeed(recent){
    const feed=$id("cd-feed");
    if(!recent||!recent.length){feed.innerHTML='<div class="cd-empty">No activity in this window yet.</div>';return;}
    feed.innerHTML=recent.map(r=>{const via=r.via?` (${r.via})`:"";return `<div class="cd-item"><span class="cd-badge ${badgeClass(r.event)}">${badgeLabel(r.event)}${via}</span><span class="cd-who">visitor ${esc(r.sid)}</span><span class="cd-ago">${esc(r.ago)}</span></div>`;}).join("");
  }

  let trendChart, convChart;
  function formatBucketLabel(raw, granularity){
    if(granularity === "hour"){
      const d = new Date(String(raw).replace(" ","T"));
      return isNaN(d) ? raw : d.toLocaleTimeString([], {hour:"numeric"});
    }
    const d = new Date(raw + "T00:00:00");
    return isNaN(d) ? raw : d.toLocaleDateString([], {month:"short", day:"numeric"});
  }
  function renderTrendChart(daily, granularity){
    if(typeof Chart === "undefined" || !$id("cd-trendChart")) return;
    const hint = $id("cd-trendHint");
    if(hint) hint.textContent = granularity === "hour" ? "— distinct visitors per hour, per stage (today)" : "— distinct visitors per day, per stage";
    const labels = (daily||[]).map(d=>formatBucketLabel(d.date, granularity));
    const mk = (key,color,label)=>({label,data:(daily||[]).map(d=>d[key]||0),borderColor:color,backgroundColor:color+"22",tension:.35,fill:true,pointRadius:2,borderWidth:2});
    const data = { labels, datasets:[
      mk("loaded","#0E443B","Visited"),
      mk("opened","#1C6E60","Opened"),
      mk("asked","#1E40AF","Asked"),
      mk("intent","#B06A48","Intent"),
      mk("converted","#166534","Converted"),
    ]};
    if(trendChart){ trendChart.data = data; trendChart.update(); return; }
    trendChart = new Chart($id("cd-trendChart").getContext("2d"), {
      type:"line", data,
      options:{ responsive:true, maintainAspectRatio:false, interaction:{mode:"index",intersect:false},
        plugins:{ legend:{ position:"bottom", labels:{ boxWidth:10, font:{ size:11 } } } },
        scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } }, x:{ grid:{ display:false } } } }
    });
  }
  function renderConvChart(f, convRate){
    if(typeof Chart === "undefined" || !$id("cd-convChart")) return;
    const converted = f.converted||0, asked = f.asked||0;
    const remaining = Math.max(asked-converted,0);
    const data = { labels:["Converted","Asked, not converted"], datasets:[{ data:[converted, remaining], backgroundColor:["#166534","#E4EAE7"], borderWidth:0 }] };
    $id("cd-convRateNum").textContent = convRate + "%";
    if(convChart){ convChart.data = data; convChart.update(); return; }
    convChart = new Chart($id("cd-convChart").getContext("2d"), {
      type:"doughnut", data,
      options:{ responsive:true, maintainAspectRatio:false, cutout:"70%",
        plugins:{ legend:{ position:"bottom", labels:{ boxWidth:10, font:{ size:11 } } } } }
    });
  }

  function updateStageFilterUI(){
    const box = $id("cd-stageFilter");
    if(stage){ box.classList.add("show"); $id("cd-stageFilterLabel").textContent = STAGE_LABELS[stage] || stage; }
    else{ box.classList.remove("show"); }
  }

  async function load(){
    try{
      const res=await fetch(qStats(),{headers:{Accept:"application/json"}});
      if(!res.ok) throw 0;
      const d=await res.json();
      renderCards(d.funnel,d.conversion_rate);
      renderFunnel(d.funnel);
      renderFeed(d.recent);
      renderTrendChart(d.daily, d.granularity);
      renderConvChart(d.funnel, d.conversion_rate);
      $id("cd-updated").textContent="live · updated just now";
      loadConversations();
    }catch(e){ $id("cd-updated").textContent="can't reach server (check login / key)"; }
  }
  async function loadConversations(){
    try{
      const res=await fetch(qConvs(),{headers:{Accept:"application/json"}});
      if(!res.ok) throw 0;
      const d=await res.json();
      const list=$id("cd-convlist");
      if(!d.items||!d.items.length){list.innerHTML='<div class="cd-empty">No stored conversations in this window yet.</div>';return;}
      list.innerHTML=d.items.map(it=>{
        const conv=it.converted?'<span class="cd-badge cd-b-converted">converted</span>':'';
        return `<div class="cd-cvrow"><div class="cd-cvrow-main"><div class="cd-cvrow-top"><b>visitor ${esc(it.sid)}</b> ${conv} <span class="cd-count">${it.count} msgs</span></div><div class="cd-cvrow-prev">${esc(it.preview)}</div></div><div class="cd-cvrow-side"><span class="cd-ago">${esc(it.ago)}</span><button class="cd-view" data-sid="${esc(it.full_sid)}" data-short="${esc(it.sid)}">View chat</button></div></div>`;
      }).join("");
    }catch(e){}
  }
  async function openChat(fullSid,shortSid){
    const overlay=$id("cd-cvOverlay"), body=$id("cd-cvBody");
    $id("cd-cvSub").textContent="visitor "+shortSid;
    body.innerHTML='<div class="cd-loading">Loading…</div>';
    overlay.style.display="flex";
    try{
      const res=await fetch(qConv(fullSid),{headers:{Accept:"application/json"}});
      const d=await res.json();
      if(!d.messages||!d.messages.length){body.innerHTML='<div class="cd-loading">No messages stored for this visitor.</div>';return;}
      body.innerHTML=d.messages.map(m=>{const cls=m.role==="user"?"cd-user":"cd-bot";return `<div class="cd-msg ${cls}"><div class="cd-bubble">${esc(m.content).replace(/\n/g,"<br>")}</div></div>`;}).join("");
      body.scrollTop=0;
    }catch(e){ body.innerHTML='<div class="cd-loading">Could not load this conversation.</div>'; }
  }

  // Range buttons (today / 7d / 30d / custom)
  $id("cd-ranges").addEventListener("click",e=>{
    const b=e.target.closest("button");
    if(!b)return;
    document.querySelectorAll("#cd-ranges button").forEach(x=>x.classList.toggle("active",x===b));
    range=b.dataset.range;

    if(range === "custom"){
      $id("cd-customRange").classList.remove("hidden");
      // Don't reload until the user picks dates and clicks Apply.
      return;
    }
    $id("cd-customRange").classList.add("hidden");
    load();
  });
  $id("cd-applyCustom").addEventListener("click",()=>{
    const from = $id("cd-fromDate").value, to = $id("cd-toDate").value;
    if(!from || !to){ alert("Please pick both a start and end date."); return; }
    customFrom = from; customTo = to;
    load();
  });

  // Clickable funnel cards -> filter the whole dashboard by that stage.
  $id("cd-cards").addEventListener("click", e=>{
    const c = e.target.closest(".cd-card[data-stage]");
    if(!c) return;
    const s = c.dataset.stage;
    stage = (stage === s) ? "" : s;
    updateStageFilterUI();
    load();
  });
  $id("cd-stageFilterClear").addEventListener("click", ()=>{
    stage = "";
    updateStageFilterUI();
    load();
  });

  $id("cd-convlist").addEventListener("click",e=>{const b=e.target.closest(".cd-view");if(!b)return;openChat(b.dataset.sid,b.dataset.short);});
  $id("cd-cvClose").addEventListener("click",()=>$id("cd-cvOverlay").style.display="none");
  $id("cd-cvOverlay").addEventListener("click",e=>{if(e.target.id==="cd-cvOverlay")e.currentTarget.style.display="none";});

  let cdOn=true; setInterval(()=>{cdOn=!cdOn;$id("cd-dot").style.opacity=cdOn?"1":"0.3";},800);
  load(); setInterval(load,30000);
})();
</script>
@endsection