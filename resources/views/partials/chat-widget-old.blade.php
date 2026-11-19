{{-- DataMySite AI chat widget. @include this right before </body> in your layout. --}}
@verbatim
<style>
  /* ============================================================
     Everything is scoped under #dms-root so it won't collide
     with your site's existing CSS when embedded.
     Rebrand the look by editing the variables in :root below.
     ============================================================ */
  :root{
    --dms-ink:#0E443B;        /* DMS deep green — panel header */
    --dms-surface:#FFFFFF;    /* chat background */
    --dms-soft:#F2F5F3;       /* bot bubble + chips */
    --dms-line:#E4EAE7;       /* hairlines */
    --dms-accent:#B06A48;     /* DMS copper — primary action / user bubble */
    --dms-accent-2:#9A5638;   /* deeper copper — gradient partner */
    --dms-muted:#6B7280;      /* secondary text */
    --dms-good:#0E9F6E;       /* success */
    --dms-radius:18px;
    --dms-font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  /* ============================ WIDGET ============================ */
  #dms-root *{box-sizing:border-box;}
  #dms-launcher{
    position:fixed;right:22px;bottom:22px;z-index:2147483000;
    height:60px;min-width:60px;padding:0 20px;border:none;cursor:pointer;
    border-radius:999px;color:#fff;font-family:var(--dms-font);font-weight:600;font-size:15px;
    display:flex;align-items:center;gap:10px;
    background:linear-gradient(135deg,var(--dms-accent),var(--dms-accent-2));
    box-shadow:0 12px 30px rgba(67,56,202,.35);transition:transform .15s ease, box-shadow .15s ease;
  }
  #dms-launcher:hover{transform:translateY(-2px);box-shadow:0 16px 38px rgba(67,56,202,.45);}
  #dms-launcher svg{width:22px;height:22px;flex:none;}
  #dms-launcher .pulse{position:absolute;right:-2px;top:-2px;width:14px;height:14px;border-radius:50%;
    background:#22C55E;border:2px solid #fff;}

  #dms-panel{
    position:fixed;right:22px;bottom:22px;z-index:2147483001;
    width:392px;max-width:calc(100vw - 28px);height:640px;max-height:calc(100vh - 44px);
    background:var(--dms-surface);border-radius:20px;overflow:hidden;display:none;flex-direction:column;
    box-shadow:0 24px 70px rgba(16,24,40,.28);border:1px solid var(--dms-line);
    font-family:var(--dms-font);
  }
  #dms-panel.open{display:flex;animation:dms-in .22s cubic-bezier(.2,.8,.2,1);}
  @keyframes dms-in{from{opacity:0;transform:translateY(14px) scale(.98);}to{opacity:1;transform:none;}}

  .dms-head{background:var(--dms-ink);color:#fff;padding:16px 16px 16px 18px;display:flex;align-items:center;gap:12px;}
  .dms-avatar{width:38px;height:38px;border-radius:12px;flex:none;
    background:linear-gradient(135deg,var(--dms-accent),var(--dms-accent-2));
    display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;}
  .dms-head .who{display:flex;flex-direction:column;line-height:1.25;flex:1;min-width:0;}
  .dms-head .who .name{font-weight:700;font-size:15px;}
  .dms-head .who .status{font-size:12px;color:#A7AEC2;display:flex;align-items:center;gap:6px;}
  .dms-head .who .status .dot{width:7px;height:7px;border-radius:50%;background:#22C55E;}
  .dms-x{background:transparent;border:none;color:#A7AEC2;cursor:pointer;padding:6px;border-radius:8px;}
  .dms-x:hover{background:rgba(255,255,255,.08);color:#fff;}
  .dms-x svg{width:18px;height:18px;display:block;}

  .dms-scroll{flex:1;overflow-y:auto;padding:18px 16px 8px;background:var(--dms-surface);}
  .dms-scroll::-webkit-scrollbar{width:8px;}
  .dms-scroll::-webkit-scrollbar-thumb{background:#D9DCE6;border-radius:8px;}

  .dms-msg{display:flex;margin-bottom:12px;max-width:100%;}
  .dms-msg .bubble{padding:11px 14px;border-radius:16px;font-size:14.5px;line-height:1.5;max-width:80%;
    word-wrap:break-word;}
  .dms-msg.bot{justify-content:flex-start;}
  .dms-msg.bot .bubble{background:var(--dms-soft);color:#15181F;border-bottom-left-radius:5px;}
  .dms-msg.user{justify-content:flex-end;}
  .dms-msg.user .bubble{background:var(--dms-accent);color:#fff;border-bottom-right-radius:5px;}
  .dms-msg .bubble a{color:inherit;text-decoration:underline;}
  .dms-msg.bot .bubble a{color:var(--dms-accent);}

  .dms-chips{display:flex;flex-wrap:wrap;gap:8px;margin:2px 0 14px;}
  .dms-chip{border:1px solid var(--dms-line);background:#fff;color:var(--dms-accent);font-weight:600;
    font-size:13px;padding:8px 13px;border-radius:999px;cursor:pointer;font-family:var(--dms-font);
    transition:background .12s,border-color .12s;}
  .dms-chip:hover{background:var(--dms-soft);border-color:var(--dms-accent);}

  .dms-typing{display:flex;gap:4px;padding:12px 14px;background:var(--dms-soft);border-radius:16px;
    border-bottom-left-radius:5px;width:fit-content;margin-bottom:12px;}
  .dms-typing span{width:7px;height:7px;border-radius:50%;background:#9AA1B2;animation:dms-bounce 1.2s infinite;}
  .dms-typing span:nth-child(2){animation-delay:.18s;}
  .dms-typing span:nth-child(3){animation-delay:.36s;}
  @keyframes dms-bounce{0%,60%,100%{transform:translateY(0);opacity:.5;}30%{transform:translateY(-5px);opacity:1;}}

  /* Inline lead-capture card — the signature conversion moment */
  .dms-leadcard{border:1px solid var(--dms-line);border-radius:16px;padding:15px;margin:2px 0 14px;
    background:linear-gradient(180deg,#FBFBFF,#fff);box-shadow:0 4px 18px rgba(67,56,202,.08);}
  .dms-leadcard h4{margin:0 0 3px;font-size:14px;color:var(--dms-ink);}
  .dms-leadcard p{margin:0 0 11px;font-size:12.5px;color:var(--dms-muted);}
  .dms-field{margin-bottom:9px;}
  .dms-field input{width:100%;padding:10px 12px;border:1px solid var(--dms-line);border-radius:10px;
    font-size:14px;font-family:var(--dms-font);outline:none;}
  .dms-field input:focus{border-color:var(--dms-accent);box-shadow:0 0 0 3px rgba(67,56,202,.12);}
  .dms-leadcard button{width:100%;border:none;border-radius:10px;padding:11px;cursor:pointer;
    background:linear-gradient(135deg,var(--dms-accent),var(--dms-accent-2));color:#fff;font-weight:700;
    font-size:14px;font-family:var(--dms-font);}
  .dms-leadcard button:disabled{opacity:.55;cursor:default;}
  .dms-leadcard .err{color:#DC2626;font-size:12px;margin-top:6px;display:none;}
  .dms-leadcard .dms-wa-btn{display:flex;align-items:center;justify-content:center;gap:9px;
    text-decoration:none;background:#25D366;color:#fff;font-weight:700;font-size:14.5px;
    padding:12px;border-radius:10px;font-family:var(--dms-font);transition:background .12s;}
  .dms-leadcard .dms-wa-btn:hover{background:#1FB457;}
  .dms-leadcard .dms-wa-btn svg{width:20px;height:20px;flex:none;}
  .dms-leadcard.sent{text-align:center;}
  .dms-leadcard.sent .check{width:42px;height:42px;border-radius:50%;background:var(--dms-good);
    display:flex;align-items:center;justify-content:center;margin:4px auto 8px;}
  .dms-leadcard.sent .check svg{width:22px;height:22px;color:#fff;}

  .dms-foot{border-top:1px solid var(--dms-line);padding:12px 12px 12px;background:#fff;}
  .dms-inputrow{display:flex;align-items:flex-end;gap:8px;background:var(--dms-soft);border-radius:14px;padding:6px 6px 6px 14px;}
  .dms-inputrow textarea{flex:1;border:none;background:transparent;resize:none;outline:none;font-size:14.5px;
    font-family:var(--dms-font);max-height:96px;line-height:1.4;padding:7px 0;color:#15181F;}
  .dms-send{flex:none;width:38px;height:38px;border:none;border-radius:11px;cursor:pointer;
    background:var(--dms-accent);color:#fff;display:flex;align-items:center;justify-content:center;}
  .dms-send:disabled{opacity:.4;cursor:default;}
  .dms-send svg{width:18px;height:18px;}
  .dms-credit{text-align:center;font-size:11px;color:#A2A8B8;margin-top:8px;}
  .dms-credit b{color:#7C8398;font-weight:600;}
</style>

<div id="dms-root">
  <button id="dms-launcher" aria-label="Open chat">
    <span class="pulse"></span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
    <span id="dms-launch-label">Chat with us</span>
  </button>

  <div id="dms-panel" role="dialog" aria-label="datamysite chat">
    <div class="dms-head">
      <div class="dms-avatar" id="dms-avatar">D</div>
      <div class="who">
        <span class="name" id="dms-botname">Ava · datamysite</span>
        <span class="status"><span class="dot"></span>Usually replies instantly</span>
      </div>
      <button class="dms-x" id="dms-close" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="dms-scroll" id="dms-scroll"></div>

    <div class="dms-foot">
      <div class="dms-inputrow">
        <textarea id="dms-input" rows="1" placeholder="Type your message…"></textarea>
        <button class="dms-send" id="dms-send" aria-label="Send">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
        </button>
      </div>
      <div class="dms-credit">Powered by <b>Claude</b> · DataMySite</div>
    </div>
  </div>
</div>

<script>
(function(){
/* ================================================================
   ⚙️  CONFIG — THIS IS THE ONLY PART YOU EDIT TO MAKE IT "YOURS".
   Everything marked  ← REPLACE  is placeholder text. Swap in the
   real datamysite copy (or send me your deck + case studies and
   I'll fill this in precisely).
   ================================================================ */
const CONFIG = {
  botName: "Ava",                       // the assistant's name
  brandName: "DataMySite",
  avatarLetter: "D",

  // What DMS does, in one breath a visitor instantly understands.
  whatWeDo: "DataMySite (DMS) is a full-service marketing and advertising agency in Dubai — your \"extended arms\" across the UAE, KSA, India and China. " +
            "We run 360° campaigns: influencer and celebrity marketing, airport, inflight, transit and outdoor media, billboards, digital screens, PR, events and sponsorships, plus SEO, web development and lead generation.",

  // Core services in plain language.
  services: [
    "Influencer & celebrity marketing — 10,000+ influencers across the GCC and celebrities from Bollywood, Tollywood and beyond",
    "Out-of-home & transit media — airports, inflight (Emirates, Etihad, Air Arabia), Dubai Metro, buses, taxis, boats and billboards",
    "Digital screens & experiential — hotels, elevators, petrol stations, Top Golf Dubai and mall activations",
    "PR, events & sponsorships across UAE, KSA and India",
    "SEO, full-stack & e-commerce web development, and custom lead generation",
    "China market entry — Kuaishou, Weibo and WeChat advertising"
  ],

  // Who DMS is the best fit for — helps the bot qualify.
  idealCustomer: "Brands in real estate, retail, automotive, beauty, finance and ecommerce that want more visibility, qualified leads and sales across the UAE, GCC, India or China. DMS works with names like Dubai Duty Free, DAMAC, Danube, Lenskart, Jetour, AYS Developers and BNW.",

  // REAL results from DMS case studies. The bot must only use these — never invent numbers.
  proof: [
    "Lenskart (UAE): 100 influencers drove 754K+ views, 8.84% engagement and 300+ tracked orders in just 5 days — at AED 0.07 cost-per-view, roughly 4x cheaper than Meta/Google ads",
    "Lenskart (Saudi National Day): 100 Saudi influencers generated 359K+ views, 5.66% engagement and 200+ tracked orders",
    "AYS Developers x Amir Khan: a celebrity-ambassador campaign reaching an estimated 15M+ people in 6 months across airline, cinema, billboard and mall media",
    "Jetour T2: a 30-day experiential car display at Dubai Airport Terminal 2, seen by an estimated 800K–1M travellers",
    "Tranquil 'Blossom 76': 100+ wrapped Dubai taxis covered 300,000+ km and generated 4M+ monthly impressions, driving a clear spike in project enquiries",
    "BNW Developments: an outdoor-campaign audit that fixed wasted media spend and brought 650+ assets to 100% utilisation",
    "Danube: an influencer campaign with 2,600 creators, 1.1B combined followership and 94M views"
  ],

  // How to talk about pricing without quoting fixed numbers.
  pricingApproach: "Every campaign is tailored to the goal, market and scale, so DMS doesn't publish fixed prices. We start with a free consultation, then send a custom proposal. We're known for strong ROI — for example, influencer cost-per-view as low as AED 0.07.",

  // The single action we most want an interested visitor to take.
  primaryCTA: "book a free consultation with the DMS team",
  calendarLink: "",   // ← optional: add a real booking link to let visitors self-schedule

  // Human handoff.
  humanHandoff: "the DMS team",

  tone: "warm, sharp and consultative — a knowledgeable Dubai marketing expert who is helpful first, never pushy",
  language: "English",

  // ── HOW VISITORS CONNECT ─────────────────────────────────────
  // "whatsapp" = hand off to WhatsApp to talk to your team (no form).
  // "form"     = capture name/email/phone into HubSpot via the backend.
  connectMode: "whatsapp",
  whatsappNumber: "971525287259",     // ← your number: country code + number, digits only (no + or spaces)
  whatsappGreeting: "Hi DataMySite team, I'd like to talk about my marketing.",

  // ── PROACTIVE OPEN ───────────────────────────────────────────
  autoOpenDelayMs: 5000,   // auto-open & greet this many ms after load (0 = off)
  autoOpenOnce: true,       // only auto-open once per browser (set false to do it every visit)

  // ── DEPLOYMENT ───────────────────────────────────────────────
  // Leave apiBase "" to run the in-Claude demo (direct, no key needed).
  // For your LIVE datamysite site, set apiBase to your backend's base
  // URL — e.g. "https://datamysite.com/api". The widget then calls YOUR
  // server, which keeps the API key secret and emails each lead to you.
  apiBase: "https://datamysite.com/api",
  leadWebhookUrl: ""   // optional: ALSO post leads to a Zapier/other hook
};

/* ---------- Build the system prompt from CONFIG ---------- */
function buildSystemPrompt(){
  return `You are ${CONFIG.botName}, the AI assistant on ${CONFIG.brandName}'s website. You think and act like four people at once: a sales consultant, a marketer, a customer-success manager, and a business strategist. Your purpose is not to answer questions — it is to guide each visitor toward a meaningful outcome: a booked consultation or a captured, qualified lead.

================= WHAT YOU KNOW =================
You have complete knowledge of ${CONFIG.brandName}'s services, case studies, client success stories, pricing approach, industry expertise and values, summarised here:

ABOUT:
${CONFIG.whatWeDo}

SERVICES:
- ${CONFIG.services.join("\n- ")}

WHO WE'RE FOR:
${CONFIG.idealCustomer}

PROVEN RESULTS (use ONLY these — never invent clients, numbers, or guarantees):
- ${CONFIG.proof.join("\n- ")}

PRICING:
${CONFIG.pricingApproach}

PRIMARY GOAL ACTION:
${CONFIG.primaryCTA}${CONFIG.calendarLink ? " ("+CONFIG.calendarLink+")" : ""}

If you genuinely don't know something (an exact price, a niche capability, a timeline), say so honestly and offer to connect them with ${CONFIG.humanHandoff}. Never fabricate.

================= HOW YOU OPERATE =================
- ${CONFIG.tone}. Replies are short and human: usually 2–4 sentences. Mirror the visitor's own words.
- Guide, don't just answer. Every reply should move the visitor one step closer to a meaningful outcome.
- Break big goals into small, actionable steps. If you need more to help, ask ONE intelligent follow-up at a time — never interrogate.
- Lead with practical value and real insight, not pitches. Be consultative.
- Personalise every recommendation to their situation. No generic answers.
- Meet objections by understanding and reframing value — never with pressure.
- Never abandon the objective midway. Keep working toward it until it is achieved or the visitor clearly ends the chat.

================= QUALIFY NATURALLY =================
Through normal conversation (never a checklist), work out: their industry, business size, geography/market, marketing objective, rough budget range, timeline, whether they're the decision-maker, and their current challenge. Collect this naturally across the chat — do not interrogate. Once you understand enough, match them to the right service and the single most relevant case study above, and recommend a concrete next step.

================= MOVE TO THE OUTCOME =================
When real buying intent appears (they ask about getting started, pricing, a quote, results for their case, or to speak to someone):
- Recommend a clear, specific next step. ${CONFIG.connectMode === "whatsapp" ? "Invite them to continue the conversation with our team on WhatsApp." : "Guide them toward "+CONFIG.primaryCTA+"."}
- Then output the token <<<CONNECT>>> on its own line. ${CONFIG.connectMode === "whatsapp" ? "This shows a button that opens WhatsApp so they can talk to the team directly — do NOT type out a WhatsApp link or number yourself, always use the token." : "This opens a short contact form — never ask for name/email/phone as plain text, always use the token."}
- Immediately AFTER the token, on the very next line, output ONE line of JSON capturing what you've learned (the visitor never sees it). Use exactly these keys, with "" for anything you don't yet know:
  {"service":"","budget":"","timeline":"","painPoint":"","summary":""}
  where service = the DMS service they most want, budget = any budget they mentioned, timeline = when they want to start, painPoint = the core problem to solve, and summary = one or two sentences a teammate can read to instantly understand this person. Write nothing after this JSON line.
- ${CONFIG.connectMode === "whatsapp" ? "Once the button appears, let them know the team will continue with them on WhatsApp." : "After they submit, confirm warmly and state the next step (the team will reach out"+(CONFIG.calendarLink ? ", or they can pick a time at "+CONFIG.calendarLink : "")+")."}

================= REMEMBER WITHIN THE CHAT =================
Hold context for the whole conversation: their goal, industry, geography, budget hints, pain points, preferred solutions, and any objections raised. Never re-ask something they've already told you.

================= NEVER / ALWAYS =================
Never: sound robotic, drown them in corporate jargon, push aggressively, end the conversation early, or give generic answers.
Always: be consultative, ask intelligent questions, personalise recommendations, give practical value, and focus on solving their business problem.

================= REFLECT AS YOU GO (think silently) =================
After each of the visitor's messages, reason internally — never show this: What is their real goal? Is it achieved yet? What objection is in the way? What information is still missing? What response would increase engagement and trust? What single next move gets them closer to booking or sharing details? Then make that move. Adapt your approach in real time based on what is and isn't landing in this conversation.

================= WHEN YOUR WORK IS DONE =================
Your work is complete only when one of these happens: their objective is met, a consultation is booked, a qualified lead is captured, the visitor clearly ends the chat, or no further progress can reasonably be made. Until then, keep helping them move toward a successful outcome.

Respond in ${CONFIG.language}. Begin by understanding what brought them here today.`;
}

/* ================================================================
   Widget engine — generally no need to edit below this line.
   ================================================================ */
const $ = (id)=>document.getElementById(id);
const panel=$("dms-panel"), launcher=$("dms-launcher"), scroll=$("dms-scroll"),
      input=$("dms-input"), sendBtn=$("dms-send");
$("dms-botname").textContent = CONFIG.botName + " · " + CONFIG.brandName;
$("dms-avatar").textContent = CONFIG.avatarLetter;

let history = [];          // {role, content} sent to the API
let leads = [];            // captured leads (demo)
let busy = false;
let greeted = false;
let pendingQualification = null;   // structured data the bot gathered, attached to the next lead

// ---------- lightweight journey analytics ----------
let SID;
try{ SID = sessionStorage.getItem("dms_sid") || (Date.now().toString(36)+Math.random().toString(36).slice(2,8)); sessionStorage.setItem("dms_sid", SID); }
catch(e){ SID = Date.now().toString(36)+Math.random().toString(36).slice(2,8); }
const tracked = {};
function track(event, meta){
  if(!CONFIG.apiBase) return;   // only report in production (apiBase set)
  try{
    fetch(CONFIG.apiBase.replace(/\/+$/,"")+"/track",{
      method:"POST", headers:{"Content-Type":"application/json"}, keepalive:true,
      body:JSON.stringify({ sid:SID, event:event, meta:meta||null,messages:history })
    }).catch(()=>{});
  }catch(e){}
}

const GREETING = `Hi 👋 I'm ${CONFIG.botName} from ${CONFIG.brandName}. We help brands across the UAE, GCC, India and China win more visibility, leads and sales — through influencers, media, SEO and more. What are you working on?`;
const INTENT_CHIPS = ["Get more leads & sales","Influencer & celebrity campaigns","Out-of-home & airport ads","See your case studies"];

function openPanel(){
  panel.classList.add("open"); launcher.style.display="none";
  if(!tracked.open){ tracked.open=true; track("open"); }
  if(!greeted){ greeted=true; addBot(GREETING); addChips(INTENT_CHIPS); }
  setTimeout(()=>input.focus(),120);
}
function closePanel(){ panel.classList.remove("open"); launcher.style.display="flex"; }
launcher.addEventListener("click",openPanel);
$("dms-close").addEventListener("click",closePanel);

/* ---------- rendering ---------- */
function escapeHtml(s){return s.replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function mdLite(s){
  s = escapeHtml(s);
  s = s.replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>');
  s = s.replace(/(https?:\/\/[^\s<]+)/g,'<a href="$1" target="_blank" rel="noopener">$1</a>');
  s = s.replace(/\n/g,'<br>');
  return s;
}
function addBot(text){
  const el=document.createElement("div"); el.className="dms-msg bot";
  el.innerHTML='<div class="bubble">'+mdLite(text)+'</div>'; scroll.appendChild(el); toBottom();
}
function addUser(text){
  const el=document.createElement("div"); el.className="dms-msg user";
  el.innerHTML='<div class="bubble">'+mdLite(text)+'</div>'; scroll.appendChild(el); toBottom();
}
function addChips(list){
  const wrap=document.createElement("div"); wrap.className="dms-chips";
  list.forEach(t=>{
    const c=document.createElement("button"); c.className="dms-chip"; c.textContent=t;
    c.addEventListener("click",()=>{ wrap.remove(); send(t); });
    wrap.appendChild(c);
  });
  scroll.appendChild(wrap); toBottom();
}
function showTyping(){
  const t=document.createElement("div"); t.className="dms-typing"; t.id="dms-typing";
  t.innerHTML="<span></span><span></span><span></span>"; scroll.appendChild(t); toBottom();
}
function hideTyping(){ const t=$("dms-typing"); if(t) t.remove(); }
function toBottom(){ scroll.scrollTop=scroll.scrollHeight; }

/* ---------- WhatsApp handoff ---------- */
function showWhatsApp(){
  const q = pendingQualification || {};
  let msg = CONFIG.whatsappGreeting || "Hi, I'd like to talk.";
  const ctx = [];
  if(q.service) ctx.push("Interested in: " + q.service);
  if(q.summary) ctx.push(q.summary);
  if(ctx.length) msg += "\n\n" + ctx.join("\n");
  const num = String(CONFIG.whatsappNumber).replace(/\D/g,"");
  const url = "https://wa.me/" + num + "?text=" + encodeURIComponent(msg);

  const card=document.createElement("div"); card.className="dms-leadcard";
  card.innerHTML=`
    <h4>Let's talk on WhatsApp</h4>
    <p>Tap below to chat with the ${escapeHtml(CONFIG.brandName)} team directly.</p>
    <a class="dms-wa-btn" href="${url}" target="_blank" rel="noopener">
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.148-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
      Chat on WhatsApp
    </a>`;
  scroll.appendChild(card); toBottom();
  const wab=card.querySelector(".dms-wa-btn"); if(wab) wab.addEventListener("click",()=>track("converted",{via:"whatsapp"}));
  track("connect_shown",{mode:"whatsapp"});
  pendingQualification=null;
}

/* ---------- lead capture card ---------- */
function showLeadForm(){
  const card=document.createElement("div"); card.className="dms-leadcard";
  card.innerHTML=`
    <h4>Let's get you set up</h4>
    <p>Drop your details and we'll take it from here.</p>
    <div class="dms-field"><input type="text" placeholder="Your name" data-f="name"></div>
    <div class="dms-field"><input type="email" placeholder="Work email" data-f="email"></div>
    <div class="dms-field"><input type="text" placeholder="Company / website" data-f="company"></div>
    <div class="dms-field"><input type="tel" placeholder="Phone (optional)" data-f="phone"></div>
    <button data-submit>Send my details</button>
    <div class="err" data-err>Please add at least your name and a valid email.</div>`;
  scroll.appendChild(card); toBottom();
  track("connect_shown",{mode:"form"});
  const btn=card.querySelector("[data-submit]"), err=card.querySelector("[data-err]");
  btn.addEventListener("click",()=>{
    const get=f=>card.querySelector(`[data-f="${f}"]`).value.trim();
    const lead={name:get("name"),email:get("email"),company:get("company"),phone:get("phone"),at:new Date().toISOString()};
    if(pendingQualification && typeof pendingQualification==="object"){
      lead.service   = pendingQualification.service   || "";
      lead.budget    = pendingQualification.budget    || "";
      lead.timeline  = pendingQualification.timeline  || "";
      lead.painPoint = pendingQualification.painPoint || "";
      lead.summary   = pendingQualification.summary   || "";
    }
    const emailOk=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(lead.email);
    if(!lead.name||!emailOk){ err.style.display="block"; return; }
    btn.disabled=true; deliverLead(lead); pendingQualification=null; track("converted",{via:"form"});
    card.classList.add("sent");
    card.innerHTML=`<div class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div>
      <h4>Got it, thanks ${escapeHtml(lead.name.split(" ")[0])}!</h4>
      <p style="margin:0">We'll be in touch shortly.</p>`;
    toBottom();
    // feed the submission back into the conversation so the bot confirms naturally
    history.push({role:"user",content:`[Visitor submitted their contact details: name ${lead.name}, email ${lead.email}${lead.company?", company "+lead.company:""}${lead.phone?", phone "+lead.phone:""}. Confirm warmly and tell them the next step.]`});
    callClaude();
  });
}

function deliverLead(lead){
  leads.push(lead);
  renderLeadList();
  if(CONFIG.apiBase){
    fetch(CONFIG.apiBase.replace(/\/+$/,"")+"/lead",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(lead)}).catch(()=>{});
  }
  if(CONFIG.leadWebhookUrl){
    fetch(CONFIG.leadWebhookUrl,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(lead)}).catch(()=>{});
  }
}
function renderLeadList(){
  const box=$("lead-list"); if(!box) return;
  if(!leads.length){ box.innerHTML='<div class="lead-empty">No leads yet — open the chat and qualify a visitor.</div>'; return; }
  box.innerHTML=leads.map(l=>{
    const contact=`<div class="lead-row"><span><b>${escapeHtml(l.name)}</b></span>
      <span>${escapeHtml(l.email)}</span>${l.company?`<span>${escapeHtml(l.company)}</span>`:""}${l.phone?`<span>${escapeHtml(l.phone)}</span>`:""}</div>`;
    const bits=[];
    if(l.service)  bits.push(`<b>Service:</b> ${escapeHtml(l.service)}`);
    if(l.budget)   bits.push(`<b>Budget:</b> ${escapeHtml(l.budget)}`);
    if(l.timeline) bits.push(`<b>Timeline:</b> ${escapeHtml(l.timeline)}`);
    if(l.painPoint)bits.push(`<b>Pain:</b> ${escapeHtml(l.painPoint)}`);
    const meta = bits.length ? `<div style="font-size:12px;color:var(--dms-muted);padding:0 0 8px">${bits.join(" &nbsp;·&nbsp; ")}</div>` : "";
    const summary = l.summary ? `<div style="font-size:12.5px;color:#374151;padding:0 0 10px">${escapeHtml(l.summary)}</div>` : "";
    return contact + meta + summary;
  }).join("");
}

/* ---------- send + Claude call ---------- */
function send(text){
  text=(text||"").trim(); if(!text||busy) return;
  document.querySelectorAll(".dms-chips").forEach(c=>c.remove());
  addUser(text);
  history.push({role:"user",content:text});
  track("message");
  input.value=""; autosize();
  callClaude();
}

async function callClaude(){
  busy=true; sendBtn.disabled=true; showTyping();
  try{
    let reply="";
    if(CONFIG.apiBase){
      // PRODUCTION: talk to YOUR backend, which holds the secret key
      const res=await fetch(CONFIG.apiBase.replace(/\/+$/,"")+"/chat",{
        method:"POST",headers:{"Content-Type":"application/json"},
        body:JSON.stringify({system:buildSystemPrompt(),messages:history})
      });
      const data=await res.json();
      reply=(data.reply||"").trim();
    }else{
      // DEMO (inside Claude): direct call, key handled automatically
      const res=await fetch("https://api.anthropic.com/v1/messages",{
        method:"POST",headers:{"Content-Type":"application/json"},
        body:JSON.stringify({model:"claude-sonnet-4-6",max_tokens:1000,system:buildSystemPrompt(),messages:history})
      });
      const data=await res.json();
      reply=(data.content||[]).filter(b=>b.type==="text").map(b=>b.text).join("\n").trim();
    }
    if(!reply) reply="Sorry — I didn't catch that. Could you say it another way?";
    history.push({role:"assistant",content:reply});

    hideTyping();
    const tokenIdx = reply.indexOf("<<<CONNECT>>>");
    const wantsConnect = tokenIdx !== -1;
    if(wantsConnect){
      // Everything after the token is context data, not shown to the visitor.
      const after = reply.slice(tokenIdx + "<<<CONNECT>>>".length);
      const jsonMatch = after.match(/\{[\s\S]*\}/);
      if(jsonMatch){ try{ pendingQualification = JSON.parse(jsonMatch[0]); }catch(e){ pendingQualification = null; } }
      reply = reply.slice(0, tokenIdx).trim();   // visible message = text before the token
    }
    if(reply) addBot(reply);
    if(wantsConnect){
      if(CONFIG.connectMode === "whatsapp" && CONFIG.whatsappNumber){ showWhatsApp(); }
      else { showLeadForm(); }
    }
  }catch(err){
    hideTyping();
    addBot("I'm having trouble connecting right now. You can reach the team directly at "+CONFIG.humanHandoff+".");
  }finally{
    busy=false; sendBtn.disabled=false; input.focus();
  }
}

/* ---------- input behaviour ---------- */
function autosize(){ input.style.height="auto"; input.style.height=Math.min(input.scrollHeight,96)+"px"; }
input.addEventListener("input",autosize);
input.addEventListener("keydown",e=>{ if(e.key==="Enter"&&!e.shiftKey){ e.preventDefault(); send(input.value); }});
sendBtn.addEventListener("click",()=>send(input.value));

/* ---------- proactive auto-open on first visit ---------- */
if((CONFIG.autoOpenDelayMs|0) > 0){
  let alreadyAuto=false;
  try{ alreadyAuto = CONFIG.autoOpenOnce && localStorage.getItem("dms_auto_opened")==="1"; }catch(e){}
  if(!alreadyAuto){
    setTimeout(function(){
      if(panel.classList.contains("open") || greeted) return;   // visitor already engaged — don't interrupt
      openPanel();
      try{ if(CONFIG.autoOpenOnce) localStorage.setItem("dms_auto_opened","1"); }catch(e){}
    }, CONFIG.autoOpenDelayMs);
  }
}

/* ---------- count this visit once (top of funnel) ---------- */
try{ if(!sessionStorage.getItem("dms_ev_loaded")){ sessionStorage.setItem("dms_ev_loaded","1"); track("loaded"); } }
catch(e){ track("loaded"); }
})();
</script>
@endverbatim
