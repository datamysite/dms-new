{{-- DataMySite AI chat widget ("Ava"). @include this right before </body> in your layout. --}}
{{-- Look: editorial bento + 3D liquid glass, in the site's brand colours (green / coral / navy). Styles are scoped under #dms-root
     and read the site's own colour variables (with the same hex values as fallback); icons are inline Lucide SVGs (ISC licence),
     so the widget needs no CSS framework, icon library or extra download. The conversation logic is in the script below. --}}
@verbatim
<style>
  /* ============================================================
     Everything is scoped under #dms-root so it won't collide
     with your site's existing CSS when embedded.
     Rebrand the look by editing the variables right below.
     ============================================================ */
  #dms-root{
    --dms-green:#0b3d2e;      /* deep brand green (V2: --green) */
    --dms-green-d:#082e23;
    --dms-coral:#e04a43;      /* brand coral (V2: --coral, old pages: --accent-color) */
    --dms-coral-d:#c63a34;
    --dms-navy:#1b2f45;       /* headings / text */
    --dms-muted:#5d6b78;      /* secondary text */
    --dms-line:rgba(11,61,46,.12);
    --dms-glass:rgba(255,255,255,.62);
    --dms-glass-hi:rgba(255,255,255,.9);
    --dms-good:#0E9F6E;
    --dms-ease:cubic-bezier(.16,1,.3,1);
    --dms-font:var(--font-body, var(--default-font, "Nunito Sans", system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif));
    --dms-display:var(--font-heading, var(--heading-font, "League Spartan", "Nunito Sans", system-ui, sans-serif));
  }
  @supports (color:var(--x)){
    #dms-root{
      --dms-green:var(--green, #0b3d2e);
      --dms-green-d:var(--green-d, #082e23);
      --dms-coral:var(--coral, var(--accent-color, #e04a43));
      --dms-navy:var(--heading-c, var(--heading-color, #1b2f45));
    }
  }
  #dms-root *{box-sizing:border-box;}
  #dms-root .dms-i{display:block;flex:none;}
  #dms-root button,#dms-root a,#dms-root textarea,#dms-root input{font-family:var(--dms-font);}

  /* micro label: GREETING / TOPICS / GET IN TOUCH */
  #dms-root .dms-label{display:inline-flex;align-items:center;gap:8px;font-family:var(--dms-display);font-weight:700;font-size:10px;
    letter-spacing:.24em;text-transform:uppercase;color:var(--dms-navy);opacity:.62;line-height:1;}
  #dms-root .dms-label::before{content:"";width:16px;height:2px;border-radius:2px;background:var(--dms-coral);opacity:.9;}

  /* ============================ LAUNCHER ============================ */
  /* a solid pill (no backdrop blur): this layer is on screen on every page, so it must stay cheap to draw */
  #dms-launcher{
    position:fixed;right:22px;bottom:22px;z-index:2147483000;
    height:58px;padding:0 24px 0 9px;border:1px solid rgba(255,255,255,.14);border-top-color:rgba(255,255,255,.38);border-left-color:rgba(255,255,255,.26);
    cursor:pointer;border-radius:999px;color:#fff;
    display:flex;align-items:center;gap:13px;
    background:linear-gradient(180deg,#14503f 0%,var(--dms-green) 52%,var(--dms-green-d) 100%);
    box-shadow:inset 0 1px 1px rgba(255,255,255,.3),inset 0 -3px 6px rgba(0,0,0,.28),0 16px 32px -10px rgba(8,46,35,.6),0 5px 12px rgba(8,46,35,.28);
    transition:transform .45s var(--dms-ease),box-shadow .45s var(--dms-ease);
    animation:dms-launch-in .55s var(--dms-ease) backwards;
  }
  #dms-launcher:hover{transform:translateY(-4px);
    box-shadow:inset 0 1px 1px rgba(255,255,255,.34),inset 0 -3px 6px rgba(0,0,0,.28),0 22px 38px -10px rgba(8,46,35,.62),0 8px 16px rgba(8,46,35,.3);}
  #dms-launcher:active{transform:translateY(-1px) scale(.97);}
  #dms-launcher:focus-visible,#dms-root .dms-ic:focus-visible,#dms-root .dms-card:focus-visible,#dms-root .dms-send:focus-visible,#dms-root .dms-reset:focus-visible{
    outline:2px solid var(--dms-coral);outline-offset:3px;}
  #dms-launcher .dms-l-badge{position:relative;width:40px;height:40px;flex:none;border-radius:50%;display:grid;place-items:center;color:#fff;
    background:radial-gradient(circle at 32% 24%,#ff8f89 0,var(--dms-coral) 46%,var(--dms-coral-d) 100%);
    box-shadow:inset 0 1px 1px rgba(255,255,255,.65),inset 0 -3px 5px rgba(120,20,16,.35),0 6px 12px -3px rgba(224,74,67,.65);}
  #dms-launcher .dms-l-badge .dms-i{transition:transform .5s var(--dms-ease);}
  #dms-launcher:hover .dms-l-badge .dms-i{transform:rotate(12deg) scale(1.08);}
  /* ambient pulse behind the badge */
  #dms-launcher .dms-l-ring{position:absolute;inset:0;border-radius:50%;background:var(--dms-coral);opacity:.35;z-index:-1;animation:dms-pulse 2.8s ease-out infinite;}
  #dms-launcher .pulse{position:absolute;right:-1px;top:-1px;width:12px;height:12px;border-radius:50%;background:#22C55E;border:2px solid var(--dms-green);}
  #dms-launch-label{font-family:var(--dms-display);font-weight:700;font-size:12px;letter-spacing:.22em;text-transform:uppercase;white-space:nowrap;line-height:1;}
  @keyframes dms-pulse{0%{transform:scale(1);opacity:.4;}70%,100%{transform:scale(1.9);opacity:0;}}
  @keyframes dms-launch-in{from{opacity:0;transform:translate3d(0,14px,0) scale(.92);}}

  /* proactive teaser bubble (disabled by default in CONFIG) */
  #dms-teaser{position:fixed;right:22px;bottom:94px;z-index:2147482999;width:300px;max-width:calc(100vw - 32px);
    background:linear-gradient(160deg,rgba(255,255,255,.96),rgba(255,255,255,.78));border:1px solid rgba(255,255,255,.9);border-radius:20px;
    box-shadow:inset 0 1px 1px #fff,0 18px 44px -12px rgba(8,46,35,.4);
    padding:15px 15px 13px;font-family:var(--dms-font);animation:dms-rise .45s var(--dms-ease) backwards;}
  #dms-teaser .dms-t-msg{font-size:14px;line-height:1.45;color:var(--dms-navy);padding-right:18px;}
  #dms-teaser .dms-t-x{position:absolute;top:8px;right:10px;background:none;border:none;color:#8a96a3;cursor:pointer;font-size:18px;line-height:1;padding:2px;}
  #dms-teaser .dms-t-x:hover{color:var(--dms-navy);}
  #dms-teaser .dms-t-chips{display:flex;flex-wrap:wrap;gap:7px;margin-top:11px;}
  #dms-teaser .dms-t-chip{border:1px solid var(--dms-line);background:#fff;color:var(--dms-green);font-weight:700;font-size:12.5px;padding:8px 12px;border-radius:999px;
    cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;transition:transform .25s var(--dms-ease),border-color .2s,background .2s;}
  #dms-teaser .dms-t-chip:hover{border-color:var(--dms-coral);background:#fff6f5;transform:translateY(-1px);}

  /* ============================== PANEL ============================== */
  /* liquid glass: translucent layers + backdrop blur + specular top/left rim (blur only exists while the panel is open) */
  #dms-panel{
    position:fixed;right:22px;bottom:22px;z-index:2147483001;
    width:420px;max-width:calc(100vw - 28px);height:680px;max-height:calc(100vh - 44px);
    border-radius:30px;overflow:hidden;display:none;flex-direction:column;isolation:isolate;
    background:linear-gradient(155deg,rgba(255,255,255,.93),rgba(241,247,244,.88) 55%,rgba(255,246,244,.86));
    -webkit-backdrop-filter:blur(30px) saturate(1.7);backdrop-filter:blur(30px) saturate(1.7);
    border:1px solid rgba(255,255,255,.55);border-top-color:rgba(255,255,255,.95);border-left-color:rgba(255,255,255,.9);
    box-shadow:inset 0 1px 1px rgba(255,255,255,.95),inset 1px 0 1px rgba(255,255,255,.6),0 40px 90px -24px rgba(8,46,35,.55),0 12px 30px rgba(8,46,35,.16);
    font-family:var(--dms-font);color:var(--dms-navy);transform-origin:100% 100%;will-change:transform,opacity;
  }
  /* soft coloured light inside the glass (static gradients, no filters) */
  #dms-panel::before{content:"";position:absolute;inset:0;z-index:-1;pointer-events:none;border-radius:inherit;
    background:radial-gradient(60% 38% at 0% 0%,rgba(224,74,67,.16),transparent 70%),radial-gradient(70% 45% at 100% 100%,rgba(11,61,46,.09),transparent 70%);}
  #dms-panel.open{display:flex;animation:dms-panel-in .5s var(--dms-ease);}
  #dms-panel.open.closing{animation:dms-panel-out .22s ease-in forwards;pointer-events:none;}
  @keyframes dms-panel-in{from{opacity:0;transform:translate3d(0,22px,0) scale(.92);}}
  @keyframes dms-panel-out{to{opacity:0;transform:translate3d(0,14px,0) scale(.95);}}
  @keyframes dms-rise{from{opacity:0;transform:translate3d(0,14px,0) scale(.97);}}

  /* header */
  #dms-panel .dms-head{display:flex;align-items:center;gap:13px;padding:16px 14px 14px 18px;position:relative;
    background:linear-gradient(180deg,rgba(255,255,255,.75),rgba(255,255,255,.3));border-bottom:1px solid var(--dms-line);}
  /* 3D orb avatar */
  #dms-panel .dms-avatar{position:relative;width:44px;height:44px;flex:none;border-radius:50%;display:grid;place-items:center;color:#fff;
    font-family:var(--dms-display);font-weight:800;font-size:17px;text-shadow:0 1px 2px rgba(0,0,0,.35);
    background:radial-gradient(circle at 30% 24%,rgba(255,255,255,.95) 0 5%,rgba(255,255,255,0) 6.5%),radial-gradient(circle at 34% 30%,#3f8f78 0%,var(--dms-green) 56%,#04201a 100%);
    box-shadow:inset -5px -7px 11px rgba(224,74,67,.6),inset 3px 3px 7px rgba(255,255,255,.34),0 10px 18px -6px rgba(8,46,35,.65);}
  #dms-panel .dms-avatar::after{content:"";position:absolute;right:0;bottom:1px;width:11px;height:11px;border-radius:50%;background:#22C55E;border:2px solid #fff;box-shadow:0 0 0 0 rgba(34,197,94,.5);animation:dms-online 2.4s ease-out infinite;}
  @keyframes dms-online{0%{box-shadow:0 0 0 0 rgba(34,197,94,.5);}100%{box-shadow:0 0 0 8px rgba(34,197,94,0);}}
  #dms-panel .who{display:flex;flex-direction:column;gap:5px;flex:1;min-width:0;line-height:1.15;}
  #dms-panel .who .name-row{display:flex;align-items:center;gap:8px;min-width:0;}
  #dms-panel .who .name{font-family:var(--dms-display);font-weight:700;font-size:16px;letter-spacing:.01em;color:var(--dms-navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    #dms-panel .dms-online{font-family:var(--dms-display);font-weight:700;font-size:9px;letter-spacing:.18em;color:#0a7a55;background:rgba(14,159,110,.12);
    border:1px solid rgba(14,159,110,.25);padding:3px 7px 3px 6px;border-radius:999px;display:inline-flex;align-items:center;gap:5px;line-height:1;flex:none;}
  #dms-panel .dms-online::before{content:"";width:5px;height:5px;border-radius:50%;background:#16a870;}
  #dms-panel .who .status{font-size:12px;color:var(--dms-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
  #dms-panel .dms-actions{display:flex;align-items:center;gap:2px;flex:none;}
  #dms-root .dms-ic{width:34px;height:34px;border:none;border-radius:50%;background:transparent;color:var(--dms-navy);cursor:pointer;display:grid;place-items:center;
    text-decoration:none;opacity:.78;transition:background .25s,opacity .25s,transform .35s var(--dms-ease);}
  #dms-root .dms-ic:hover{background:rgba(11,61,46,.08);opacity:1;transform:translateY(-1px);}
  #dms-root .dms-ic:active{transform:scale(.92);}

  /* conversation */
  #dms-panel .dms-scroll{flex:1;overflow-y:auto;overflow-x:hidden;padding:18px 18px 8px;overscroll-behavior:contain;}
  #dms-panel .dms-scroll::-webkit-scrollbar{width:6px;}
  #dms-panel .dms-scroll::-webkit-scrollbar-thumb{background:rgba(11,61,46,.18);border-radius:8px;}

  #dms-panel .dms-greet{position:relative;padding:15px 18px 16px;margin-bottom:16px;border-radius:22px;
    background:linear-gradient(160deg,rgba(255,255,255,.92),rgba(255,255,255,.58));
    border:1px solid rgba(255,255,255,.7);border-top-color:#fff;border-left-color:rgba(255,255,255,.95);
    box-shadow:inset 0 1px 1px #fff,0 14px 30px -18px rgba(8,46,35,.45),0 2px 6px rgba(8,46,35,.05);
    animation:dms-rise .6s var(--dms-ease) .08s backwards;}
  #dms-panel .dms-greet p{margin:11px 0 0;font-size:14.5px;line-height:1.55;color:var(--dms-navy);}

  /* TOPICS: swipeable 3D glass bento cards */
  #dms-panel .dms-chips{margin:0 -18px 4px;}
  #dms-panel .dms-chips > .dms-label{margin:0 18px;animation:dms-rise .5s var(--dms-ease) .16s backwards;}
  #dms-panel .dms-topics{display:flex;gap:11px;overflow-x:auto;overflow-y:hidden;scroll-snap-type:x mandatory;scroll-padding:0 18px;padding:12px 18px 14px;
    scrollbar-width:none;-webkit-overflow-scrolling:touch;perspective:900px;overscroll-behavior-x:contain;}
  #dms-panel .dms-topics::-webkit-scrollbar{display:none;}
  /* click-and-drag scrolling for mouse users (touch swipes natively): grab cursor; while dragging, snapping and card hover are off */
  #dms-panel .dms-topics{cursor:grab;}
  #dms-panel .dms-topics.is-drag{cursor:grabbing;scroll-snap-type:none;user-select:none;-webkit-user-select:none;}
  #dms-panel .dms-topics.is-drag .dms-card{pointer-events:none;}
  #dms-panel .dms-card{-webkit-user-drag:none;user-select:none;-webkit-user-select:none;}
  #dms-panel .dms-card:last-child{scroll-snap-align:end;}   /* the last card can be scrolled fully into view */
  #dms-panel .dms-card{position:relative;flex:0 0 130px;height:128px;scroll-snap-align:start;padding:15px 14px 14px;border-radius:22px;cursor:pointer;
    display:flex;flex-direction:column;justify-content:space-between;align-items:flex-start;text-align:left;color:var(--dms-navy);
    background:linear-gradient(165deg,rgba(255,255,255,.96),rgba(255,255,255,.52));
    border:1px solid rgba(255,255,255,.65);border-top-color:#fff;border-left-color:rgba(255,255,255,.95);
    box-shadow:inset 0 1px 1px #fff,inset 0 -8px 14px -10px rgba(11,61,46,.18),0 14px 26px -14px rgba(8,46,35,.5),0 2px 4px rgba(8,46,35,.06);
    transition:transform .45s var(--dms-ease),box-shadow .45s var(--dms-ease),border-color .3s;
    animation:dms-rise .6s var(--dms-ease) backwards;animation-delay:calc(.22s + var(--i,0) * 40ms);}
  /* specular top edge */
  #dms-panel .dms-card::before{content:"";position:absolute;left:10px;right:10px;top:0;height:46%;border-radius:22px 22px 60% 60% / 22px 22px 40% 40%;pointer-events:none;
    background:linear-gradient(180deg,rgba(255,255,255,.95),rgba(255,255,255,0));opacity:.7;}
  #dms-panel .dms-card:hover{transform:translateY(-6px) rotateX(5deg);border-color:rgba(224,74,67,.55);border-top-color:rgba(224,74,67,.7);
    box-shadow:inset 0 1px 1px #fff,0 0 0 1px rgba(224,74,67,.18),0 24px 34px -16px rgba(8,46,35,.55),0 0 24px -6px rgba(224,74,67,.35);}
  #dms-panel .dms-card:active{transform:translateY(-2px) scale(.97);transition-duration:.12s;}
  #dms-panel .dms-card-ic{position:relative;width:38px;height:38px;border-radius:13px;display:grid;place-items:center;color:#fff;
    background:linear-gradient(160deg,#f0716b,var(--dms-coral) 55%,var(--dms-coral-d));
    box-shadow:inset 0 1px 1px rgba(255,255,255,.6),inset 0 -3px 5px rgba(120,20,16,.3),0 8px 14px -6px rgba(224,74,67,.7);transition:transform .45s var(--dms-ease);}
  #dms-panel .dms-card:hover .dms-card-ic{transform:translateY(-2px) scale(1.06);}
  #dms-panel .dms-card-tx{position:relative;font-family:var(--dms-display);font-weight:700;font-size:11.5px;line-height:1.3;letter-spacing:.09em;text-transform:uppercase;
    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}

  /* messages */
  #dms-panel .dms-msg{display:flex;margin-bottom:12px;max-width:100%;animation:dms-msg-in .45s var(--dms-ease) backwards;}
  #dms-panel .dms-msg.bot{transform-origin:0 100%;}
  #dms-panel .dms-msg.user{transform-origin:100% 100%;}
  @keyframes dms-msg-in{from{opacity:0;transform:translate3d(0,12px,0) scale(.96);}}
  #dms-panel .dms-msg .bubble{padding:12px 15px;border-radius:20px;font-size:14.5px;line-height:1.55;max-width:84%;word-wrap:break-word;}
  #dms-panel .dms-msg.bot{justify-content:flex-start;}
  #dms-panel .dms-msg.bot .bubble{background:linear-gradient(160deg,rgba(255,255,255,.95),rgba(255,255,255,.68));color:var(--dms-navy);
    border:1px solid rgba(255,255,255,.8);border-bottom-left-radius:6px;box-shadow:inset 0 1px 1px #fff,0 8px 18px -12px rgba(8,46,35,.4);}
  #dms-panel .dms-msg.user{justify-content:flex-end;}
  #dms-panel .dms-msg.user .bubble{background:linear-gradient(160deg,#16604b,var(--dms-green) 60%,var(--dms-green-d));color:#fff;border-bottom-right-radius:6px;
    box-shadow:inset 0 1px 1px rgba(255,255,255,.28),0 10px 20px -10px rgba(8,46,35,.7);}
  #dms-panel .dms-msg .bubble a{color:inherit;text-decoration:underline;}
  #dms-panel .dms-msg.bot .bubble a{color:var(--dms-coral-d);}

  /* typing: three-dot wave */
  #dms-panel .dms-typing{display:flex;gap:5px;padding:14px 16px;width:fit-content;margin-bottom:12px;border-radius:20px;border-bottom-left-radius:6px;
    background:linear-gradient(160deg,rgba(255,255,255,.95),rgba(255,255,255,.68));border:1px solid rgba(255,255,255,.8);box-shadow:inset 0 1px 1px #fff,0 8px 18px -12px rgba(8,46,35,.4);
    animation:dms-msg-in .4s var(--dms-ease) backwards;}
  #dms-panel .dms-typing span{width:7px;height:7px;border-radius:50%;background:var(--dms-coral);animation:dms-wave 1.15s ease-in-out infinite;}
  #dms-panel .dms-typing span:nth-child(2){animation-delay:.14s;}
  #dms-panel .dms-typing span:nth-child(3){animation-delay:.28s;}
  @keyframes dms-wave{0%,60%,100%{transform:translateY(0);opacity:.45;}30%{transform:translateY(-6px);opacity:1;}}

  /* WhatsApp / lead cards (shown by the conversation) */
  #dms-panel .dms-leadcard{border:1px solid rgba(255,255,255,.8);border-top-color:#fff;border-radius:22px;padding:17px;margin:2px 0 14px;
    background:linear-gradient(160deg,rgba(255,255,255,.95),rgba(255,255,255,.66));box-shadow:inset 0 1px 1px #fff,0 16px 30px -18px rgba(8,46,35,.5);
    animation:dms-msg-in .5s var(--dms-ease) backwards;}
  #dms-panel .dms-leadcard h4{margin:0 0 4px;font-family:var(--dms-display);font-weight:700;font-size:16px;color:var(--dms-navy);}
  #dms-panel .dms-leadcard p{margin:0 0 13px;font-size:12.5px;color:var(--dms-muted);}
  #dms-panel .dms-field{margin-bottom:9px;}
  #dms-panel .dms-field input{width:100%;padding:12px 14px;border:1px solid var(--dms-line);border-radius:14px;font-size:14px;outline:none;background:rgba(255,255,255,.85);color:var(--dms-navy);
    transition:border-color .2s,box-shadow .2s;}
  #dms-panel .dms-field input:focus{border-color:var(--dms-coral);box-shadow:0 0 0 3px rgba(224,74,67,.16);}
  #dms-panel .dms-leadcard button{width:100%;border:none;border-radius:14px;padding:13px;cursor:pointer;color:#fff;font-weight:800;font-size:14px;letter-spacing:.02em;
    background:linear-gradient(160deg,#f0716b,var(--dms-coral) 55%,var(--dms-coral-d));box-shadow:inset 0 1px 1px rgba(255,255,255,.5),0 10px 18px -8px rgba(224,74,67,.7);
    transition:transform .3s var(--dms-ease);}
  #dms-panel .dms-leadcard button:hover{transform:translateY(-2px);}
  #dms-panel .dms-leadcard button:active{transform:scale(.97);}
  #dms-panel .dms-leadcard button:disabled{opacity:.55;cursor:default;transform:none;}
  #dms-panel .dms-leadcard .err{color:#DC2626;font-size:12px;margin-top:7px;display:none;}
  #dms-panel .dms-leadcard .dms-wa-btn{display:flex;align-items:center;justify-content:center;gap:10px;text-decoration:none;color:#fff;font-weight:800;font-size:14.5px;
    padding:13px;border-radius:999px;background:linear-gradient(180deg,#14503f,var(--dms-green) 55%,var(--dms-green-d));
    box-shadow:inset 0 1px 1px rgba(255,255,255,.3),0 12px 20px -10px rgba(8,46,35,.7);transition:transform .3s var(--dms-ease);}
  #dms-panel .dms-leadcard .dms-wa-btn:hover{transform:translateY(-2px);}
  #dms-panel .dms-leadcard .dms-wa-btn svg{width:20px;height:20px;flex:none;}
  #dms-panel .dms-leadcard .dms-call-btn{display:flex;align-items:center;justify-content:center;gap:10px;margin-top:9px;text-decoration:none;color:var(--dms-navy);font-weight:800;font-size:14.5px;
    padding:12px;border-radius:999px;background:rgba(255,255,255,.8);border:1.5px solid rgba(27,47,69,.7);transition:background .25s,transform .3s var(--dms-ease);}
  #dms-panel .dms-leadcard .dms-call-btn:hover{background:#fff;transform:translateY(-2px);}
  #dms-panel .dms-leadcard.sent{text-align:center;}
  #dms-panel .dms-leadcard.sent .check{width:44px;height:44px;border-radius:50%;background:var(--dms-good);display:flex;align-items:center;justify-content:center;margin:4px auto 9px;
    box-shadow:inset 0 1px 1px rgba(255,255,255,.5),0 10px 18px -8px rgba(14,159,110,.7);}
  #dms-panel .dms-leadcard.sent .check svg{width:22px;height:22px;color:#fff;}

  /* GET IN TOUCH strip */
  #dms-panel .dms-strip{margin:4px 14px 8px;padding:11px 12px 12px;border-radius:22px;
    background:linear-gradient(135deg,rgba(224,74,67,.12),rgba(11,61,46,.09));border:1px solid rgba(255,255,255,.75);border-top-color:#fff;
    box-shadow:inset 0 1px 1px rgba(255,255,255,.9);}
  #dms-panel.open .dms-strip{animation:dms-rise .6s var(--dms-ease) .44s backwards;}
  #dms-panel .dms-strip-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:9px;}
  #dms-panel .dms-reset{width:28px;height:28px;border:none;border-radius:50%;cursor:pointer;display:grid;place-items:center;color:var(--dms-navy);background:rgba(255,255,255,.6);
    opacity:.75;transition:transform .5s var(--dms-ease),opacity .2s,background .2s;}
  #dms-panel .dms-reset:hover{opacity:1;background:#fff;transform:rotate(-120deg);}
  #dms-panel .dms-quickbar{display:flex;gap:8px;}
  #dms-panel .dms-quickbar a{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;font-weight:800;font-size:13px;padding:11px 10px;
    border-radius:999px;min-height:44px;transition:transform .35s var(--dms-ease),box-shadow .35s var(--dms-ease);}
  #dms-panel .dms-quickbar .dms-qb-wa{color:#fff;background:linear-gradient(180deg,#14503f,var(--dms-green) 55%,var(--dms-green-d));
    box-shadow:inset 0 1px 1px rgba(255,255,255,.32),0 10px 16px -9px rgba(8,46,35,.75);}
  #dms-panel .dms-quickbar .dms-qb-call{color:var(--dms-navy);background:linear-gradient(180deg,#fff,rgba(255,255,255,.75));border:1px solid rgba(255,255,255,.9);
    box-shadow:inset 0 1px 1px #fff,0 10px 16px -10px rgba(8,46,35,.5);}
  #dms-panel .dms-quickbar a:hover{transform:translateY(-2px);}
  #dms-panel .dms-quickbar a:active{transform:scale(.96);}
  #dms-panel .dms-quickbar svg{width:17px;height:17px;flex:none;}

  /* input */
  #dms-panel .dms-foot{padding:4px 14px max(12px,env(safe-area-inset-bottom));}
  #dms-panel .dms-inputrow{display:flex;align-items:flex-end;gap:8px;padding:6px 6px 6px 18px;border-radius:999px;
    background:linear-gradient(180deg,rgba(255,255,255,.92),rgba(255,255,255,.62));border:1px solid rgba(255,255,255,.85);border-top-color:#fff;
    box-shadow:inset 0 1px 1px #fff,inset 0 -2px 6px rgba(11,61,46,.05),0 10px 22px -14px rgba(8,46,35,.5);transition:box-shadow .3s,border-color .3s;}
  #dms-panel .dms-inputrow:focus-within{border-color:rgba(224,74,67,.55);box-shadow:inset 0 1px 1px #fff,0 0 0 3px rgba(224,74,67,.14),0 10px 22px -14px rgba(8,46,35,.5);}
  #dms-panel .dms-inputrow textarea{flex:1;border:none;background:transparent;resize:none;outline:none;font-size:14.5px;max-height:96px;line-height:1.4;padding:10px 0;color:var(--dms-navy);}
  #dms-panel .dms-inputrow textarea::placeholder{color:#8794a1;}
  #dms-panel .dms-send{flex:none;width:42px;height:42px;border:none;border-radius:50%;cursor:pointer;color:#fff;display:grid;place-items:center;
    background:radial-gradient(circle at 32% 24%,#ff8f89 0,var(--dms-coral) 48%,var(--dms-coral-d) 100%);
    box-shadow:inset 0 1px 1px rgba(255,255,255,.65),inset 0 -3px 5px rgba(120,20,16,.32),0 8px 14px -5px rgba(224,74,67,.7);
    transition:transform .35s var(--dms-ease),box-shadow .35s var(--dms-ease);}
  #dms-panel .dms-send .dms-i{transition:transform .35s var(--dms-ease);}
  #dms-panel .dms-send:hover:not(:disabled) .dms-i{transform:translateY(-3px);}
  #dms-panel .dms-send:active:not(:disabled){transform:scale(.92);}
  #dms-panel .dms-send:disabled{opacity:.45;cursor:default;}
  #dms-panel .dms-credit{text-align:center;font-size:10.5px;letter-spacing:.06em;color:#7f8c98;margin-top:9px;}
  #dms-panel .dms-credit b{color:var(--dms-navy);font-weight:700;opacity:.8;}

  /* phones: a bottom sheet */
  @media (max-width:480px){
    #dms-launcher{right:14px;bottom:14px;height:54px;}
    #dms-panel{right:0;bottom:0;width:100%;max-width:100%;height:min(92vh,720px);max-height:calc(100dvh - 8px);border-radius:28px 28px 0 0;}
    #dms-panel .dms-card{flex-basis:126px;height:126px;}
    #dms-teaser{right:14px;}
  }
  /* narrow phones: keep the full name visible next to the ONLINE badge */
  @media (max-width:420px){
    #dms-panel .dms-head{gap:11px;padding:15px 10px 13px 14px;}
    #dms-panel .dms-avatar{width:40px;height:40px;font-size:15px;}
    #dms-panel .who .name{font-size:14px;}
    #dms-panel .who .name-row{gap:6px;}
    #dms-panel .dms-online{font-size:8px;letter-spacing:.1em;padding:3px 5px 3px 4px;}
    #dms-root .dms-ic{width:30px;height:30px;}
  }
  /* no backdrop blur support or reduced transparency: near-opaque glass */
  @supports not ((backdrop-filter:blur(1px)) or (-webkit-backdrop-filter:blur(1px))){ #dms-panel{background:linear-gradient(155deg,#fbfdfc,#eef5f2 60%,#fff3f1);} }
  @media (prefers-reduced-transparency:reduce){ #dms-panel{-webkit-backdrop-filter:none;backdrop-filter:none;background:linear-gradient(155deg,#fbfdfc,#eef5f2 60%,#fff3f1);} }
  @media (prefers-reduced-motion:reduce){
    #dms-root *,#dms-root *::before,#dms-root *::after{animation:none !important;transition:none !important;}
  }
</style>

<div id="dms-root">
  <button id="dms-launcher" type="button" aria-label="Open chat">
    <span class="dms-l-badge" aria-hidden="true"><span class="dms-l-ring"></span><svg class="dms-i" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/></svg><span class="pulse"></span></span>
    <span id="dms-launch-label">ASK AVA AI</span>
  </button>

  <div id="dms-panel" role="dialog" aria-label="datamysite chat">
    <div class="dms-head">
      <div class="dms-avatar" id="dms-avatar" aria-hidden="true">D</div>
      <div class="who">
        <span class="name-row"><span class="name" id="dms-botname">Ava · datamysite</span><span class="dms-online">ONLINE</span></span>
        <span class="status">Usually replies instantly</span>
      </div>
      <div class="dms-actions">
        <a class="dms-ic" id="dms-h-wa" href="#" aria-label="WhatsApp our team" title="WhatsApp"><svg class="dms-i" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/></svg></a>
        <a class="dms-ic" id="dms-h-call" href="#" aria-label="Call our team" title="Call"><svg class="dms-i" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg></a>
        <button class="dms-ic dms-x" id="dms-close" type="button" aria-label="Close" title="Close"><svg class="dms-i" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
      </div>
    </div>

    <div class="dms-scroll" id="dms-scroll"></div>

    <div class="dms-strip" id="dms-strip">
      <div class="dms-strip-top">
        <span class="dms-label">GET IN TOUCH</span>
        <button class="dms-reset" id="dms-reset" type="button" aria-label="Show the topics again" title="Show topics again"><svg class="dms-i" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></button>
      </div>
      <div class="dms-quickbar" id="dms-quickbar"></div>
    </div>

    <div class="dms-foot">
      <div class="dms-inputrow">
        <textarea id="dms-input" rows="1" placeholder="Type your message…"></textarea>
        <button class="dms-send" id="dms-send" type="button" aria-label="Send"><svg class="dms-i" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg></button>
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

  // Full service catalogue (from the DMS profile deck).
  services: [
    "Celebrity endorsements — Bollywood, Tollywood and renowned GCC personalities",
    "Influencer marketing — 10,000+ influencers across the GCC",
    "PR — 50 media houses across UAE, KSA and India",
    "Events & sponsorships — corporate, social, sports and entertainment",
    "Broadcasting & streaming — OTT platforms (283M est. audience) and 5 UAE radio stations",
    "Airport advertising — 14 airports, 591 spots (Dubai, Sharjah, South Africa, Goa)",
    "Inflight media — Emirates, Etihad and Air Arabia (pre-roll ads, front-page magazine)",
    "Transit media — Dubai Metro (53 stations, 2,668 spots), buses, 700+ taxis, advertising boats",
    "Digital screens — 1,600+ elevator screens, 60+ premium hotels, 789 petrol-station screens",
    "Top Golf Dubai & mall activations — Mall of the Emirates pop-ups and immersive brand experiences",
    "Billboards — static (9.28M daily footfall locations) and 21 digital billboards across Dubai",
    "CGI advertising — viral 3D campaigns on Dubai landmarks",
    "SEO — local SEO, free SEO audit and tech support",
    "Web development — full-stack, e-commerce and website maintenance",
    "Lead generation — custom cost-per-lead campaigns across the UAE",
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
  // "whatsapp" = show Call + WhatsApp buttons (no form).
  // "form"     = capture name/email/phone into HubSpot via the backend.
  connectMode: "whatsapp",
  whatsappNumber: "971525287259",     // country code + number, digits only (no + or spaces)
  whatsappGreeting: "Hi DataMySite team, I'd like to talk about my marketing.",
  supportPhone: "+971 52 528 7259",   // shown on the "Call us" button; tap-to-dial
  connectAfterMessages: 2,        // if the bot hasn't offered contact after this many visitor messages, show it anyway (0 = off)
  showConnectEarly: true,             // also offer Call/WhatsApp after a couple of exchanges, not only at high intent

  // ── PROACTIVE OPEN ───────────────────────────────────────────
  autoOpenDelayMs: 0,       // legacy full-panel open — superseded by the teaser below (set >0 only if teaser.enabled=false)
  autoOpenOnce: true,

  // ── PROACTIVE TEASER (small bubble + one-tap chips) ─────────
  teaser: {
    enabled: false,           // off: no chat preview bubble on page load
    delayMs: 6000,            // shown this long after the visitor first scrolls/touches/moves
    requireEngagement: true,  // wait for a real engagement signal first (filters bots, catches readers)
    hooks: {                  // page-path fragment → opening line (first match wins; "" = default)
      "influencer":          "Planning an influencer push? 👋 Ask me reach & pricing.",
      "celebrity":           "Thinking celebrity campaign? 👋 Ask me who fits your brand.",
      "outdoor-advertising": "Looking at billboards? 👋 Ask me locations & rates.",
      "transit":             "Metro, taxi or airport ads? 👋 I can help you choose.",
      "seo":                 "Want to rank higher on Google? 👋 Ask me how.",
      "lead-generation":     "Need more leads? 👋 That's literally our job.",
      "":                    "👋 Quick one — what are you trying to grow right now?"
    },
    chips: ["🚀 More leads & sales","💰 What does it cost?","💬 WhatsApp us"]
  },

  // ── DEPLOYMENT ───────────────────────────────────────────────
  // Leave apiBase "" to run the in-Claude demo (direct, no key needed).
  // For your LIVE datamysite site, set apiBase to your backend's base
  // URL — e.g. "https://datamysite.com/api". The widget then calls YOUR
  // server, which keeps the API key secret and emails each lead to you.
  /* this file is @verbatim, so Blade cannot fill in the address: use the site address every layout prints in <meta name="home_url">
     (staging posts to staging, production to production, a sub-folder install keeps its folder) */
  apiBase: (function(){ var m=document.querySelector('meta[name="home_url"]'); return ((m&&m.content)?m.content:location.origin).replace(/\/+$/,"")+"/api"; })(),
  leadWebhookUrl: ""   // optional: ALSO post leads to a Zapier/other hook
};

/* ---------- Build the system prompt from CONFIG ---------- */
function buildSystemPrompt(){
  return `You are ${CONFIG.botName}, ${CONFIG.brandName}'s AI assistant — and you operate with the mindset of a Chief Revenue Officer. You combine a sales consultant's instinct, a marketer's storytelling, a customer-success manager's empathy and a strategist's clarity, but your compass is a CRO's: every conversation is revenue in motion, and revenue happens when a visitor talks to our team. Your single measurable goal: get them onto WhatsApp or a phone call with us. A human conversation converts far better than a chat widget ever can — your job is to start that human conversation, warmly and quickly.

HOW A CRO SELLS (be this, always):
- Sell like a person, not a script. Short, natural, confident sentences. React to what they actually said.
- Lead with the outcome, not the service. They don't want "DOOH"; they want footfall, enquiries, sales. Name their outcome, then the fastest route to it.
- With 16 services across influencer, celebrity, OOH, airport, transit, digital screens, PR, events, SEO, web, lead generation and China market entry, almost any brand goal has a DMS answer — use that breadth to always have a relevant next step, and the team call as the place to pick the right mix.
- Create momentum, not pressure. Give one genuinely useful insight, then bridge to the call/WhatsApp as the obvious next step ("in 5 minutes on WhatsApp our team can give you a plan and a number for that").
- Handle hesitation like a pro: acknowledge, reframe with a specific proof point from our results, offer the low-commitment step (a quick WhatsApp message costs nothing).
- Never let a conversation drift. If it stalls, summarise what they want in one line and offer the connect step.

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
Your #1 measurable goal is getting the visitor talking to the team on WhatsApp or by phone. Do NOT wait for explicit buying intent. Offer the connect step as soon as ANY of these happen:
- you know which service or outcome they care about (even roughly),
- they ask about pricing, results, timelines, or how to start,
- you've answered their second question,
- they seem unsure what they need (the team call IS the answer: a free consultation).
When offering, be warm and concrete — e.g. tie it to their goal ("the fastest way to get a plan for X is 5 minutes with our team") — then:
- Output the token <<<CONNECT>>> on its own line. ${CONFIG.connectMode === "whatsapp" ? "This shows WhatsApp/call buttons so they can talk to the team directly — do NOT type out a number or link yourself, always use the token." : "This opens a short contact form — never ask for name/email/phone as plain text, always use the token."}
- Immediately AFTER the token, on the very next line, output ONE line of JSON capturing what you've learned (the visitor never sees it). Use exactly these keys, with "" for anything you don't yet know:
  {"service":"","budget":"","timeline":"","painPoint":"","summary":""}
  where service = the DMS service they most want, budget = any budget they mentioned, timeline = when they want to start, painPoint = the core problem to solve, and summary = one or two sentences a teammate can read to instantly understand this person. Write nothing after this JSON line.
- If they decline or keep chatting, keep helping genuinely — then find a natural moment to offer again with a different, specific reason. Never pressure, never repeat the same line twice.
- ${CONFIG.connectMode === "whatsapp" ? "Once the buttons appear, let them know the team can take it from there on WhatsApp or a quick call." : "After they submit, confirm warmly and state the next step (the team will reach out"+(CONFIG.calendarLink ? ", or they can pick a time at "+CONFIG.calendarLink : "")+")."}

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

/* ---------- icons: Lucide (ISC licence), inlined so the widget needs no icon library. icon(name,size) returns the <svg> markup;
   every piece of HTML built below uses it, so dynamically added content always has its icons (nothing to re-hydrate). ---------- */
const ICONS = {
    sparkles: "<path d=\"M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z\"/><path d=\"M20 2v4\"/><path d=\"M22 4h-4\"/><circle cx=\"4\" cy=\"20\" r=\"2\"/>",
    "message-circle": "<path d=\"M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719\"/>",
    phone: "<path d=\"M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384\"/>",
    x: "<path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/>",
    "arrow-up": "<path d=\"m5 12 7-7 7 7\"/><path d=\"M12 19V5\"/>",
    "rotate-ccw": "<path d=\"M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8\"/><path d=\"M3 3v5h5\"/>",
    "trending-up": "<path d=\"M16 7h6v6\"/><path d=\"m22 7-8.5 8.5-5-5L2 17\"/>",
    megaphone: "<path d=\"M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z\"/><path d=\"M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14\"/><path d=\"M8 6v8\"/>",
    wallet: "<path d=\"M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1\"/><path d=\"M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4\"/>",
    trophy: "<path d=\"M10 14.66V17a1 1 0 0 1-1 1 2 2 0 0 0-2 2v2\"/><path d=\"M14 14.66V17a1 1 0 0 0 1 1 2 2 0 0 1 2 2v2\"/><path d=\"M17.916 10H19.5A2.5 2.5 0 0 0 22 7.5V5a1 1 0 0 0-1-1h-3\"/><path d=\"M4 22h16\"/><path d=\"M6 9a6 6 0 0 0 12 0V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z\"/><path d=\"M6.084 10H4.5A2.5 2.5 0 0 1 2 7.5V5a1 1 0 0 1 1-1h3\"/>"
};
function icon(name,size){ size=size||18; return '<svg class="dms-i" width="'+size+'" height="'+size+'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'+(ICONS[name]||"")+'</svg>'; }
const TOPIC_ICONS = ["trending-up","megaphone","wallet","trophy","message-circle"];   // one per INTENT_CHIPS entry, same order
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
  const body = { sid:SID, event:event, meta:meta||null,messages:history };
  if(event!=="loaded" && event!=="open" && history.length){ body.conversation = history; }
  try{
    fetch(CONFIG.apiBase.replace(/\/+$/,"")+"/track",{
      method:"POST", headers:{"Content-Type":"application/json"}, keepalive:true,
      body:JSON.stringify(body)
    }).catch(()=>{});
  }catch(e){}
}

const GREETING = `Hi 👋 I'm ${CONFIG.botName} from ${CONFIG.brandName}. What are you trying to grow right now? Tap one below — or WhatsApp / call our team directly using the buttons underneath.`;
const INTENT_CHIPS = ["🚀 I want more leads & sales","📣 Brand visibility / advertising","💰 What does it cost?","🏆 Show me client results",{label:"💬 WhatsApp our team", action:"whatsapp"}];

/* the greeting is a frosted card (UI only: it is not part of the conversation history, same as before) */
function renderGreeting(){
  const el=document.createElement("div"); el.className="dms-greet";
  el.innerHTML='<span class="dms-label">GREETING</span><p>'+mdLite(GREETING)+'</p>'; scroll.appendChild(el); toBottom();
}
let closeTimer=0;
function openPanel(auto){
  clearTimeout(closeTimer); panel.classList.remove("closing");
  panel.classList.add("open"); launcher.style.display="none";
  if(!tracked.open){ tracked.open=true; track("open",{mode:auto?"auto":"manual"}); }
  if(!greeted){ greeted=true; renderGreeting(); addChips(INTENT_CHIPS); scroll.scrollTop=0; }   // the intro starts at the top
  setTimeout(()=>input.focus(),120);
}
function closePanel(){
  if(!panel.classList.contains("open") || panel.classList.contains("closing")) return;
  panel.classList.add("closing"); launcher.style.display="flex";          // the panel animates out (220 ms), then is hidden
  closeTimer=setTimeout(()=>{ panel.classList.remove("open","closing"); },230);
}
launcher.addEventListener("click",()=>openPanel(false));
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
/* mouse users: click and drag the cards sideways (touch screens swipe natively). A drag never counts as a click on a card. */
function makeDraggable(el){
  let down=false, moved=false, startX=0, startLeft=0;
  el.addEventListener('pointerdown',e=>{ if(e.pointerType==='touch'||e.button!==0) return; down=true; moved=false; startX=e.clientX; startLeft=el.scrollLeft; });
  el.addEventListener('pointermove',e=>{
    if(!down) return;
    const dx=e.clientX-startX;
    if(!moved && Math.abs(dx)>5){ moved=true; el.classList.add('is-drag'); try{ el.setPointerCapture(e.pointerId); }catch(_){} }
    if(moved){ el.scrollLeft=startLeft-dx; e.preventDefault(); }
  });
  const end=()=>{ if(!down) return; down=false; if(moved){ el.classList.remove('is-drag'); setTimeout(()=>{ moved=false; },0); } };
  el.addEventListener('pointerup',end); el.addEventListener('pointercancel',end);
  el.addEventListener('click',e=>{ if(moved){ e.preventDefault(); e.stopPropagation(); } },true);
}

/* TOPICS: the quick replies as swipeable glass bento cards. Clicking one does exactly what the old chips did:
   send(<the original label>) (or the WhatsApp card). Only the DISPLAYED label drops the leading emoji. */
function addChips(list){
  const wrap=document.createElement("div"); wrap.className="dms-chips";
  wrap.innerHTML='<span class="dms-label">TOPICS</span>';
  const row=document.createElement("div"); row.className="dms-topics";
  list.forEach((t,i)=>{
    const isObj = typeof t === "object" && t !== null;
    const label = isObj ? t.label : t;
    const c=document.createElement("button"); c.type="button"; c.className="dms-chip dms-card"; c.style.setProperty("--i",i);
    c.innerHTML='<span class="dms-card-ic">'+icon(TOPIC_ICONS[i]||"sparkles",19)+'</span><span class="dms-card-tx">'+escapeHtml(label.replace(/^[^\p{L}\p{N}]+/u,""))+'</span>';
    c.addEventListener("click",()=>{
      wrap.remove();
      if(isObj && t.action === "whatsapp"){ addUser(label); showWhatsApp(); }
      else { send(label); }
    });
    row.appendChild(c);
  });
  wrap.appendChild(row); scroll.appendChild(wrap); toBottom();
  makeDraggable(row);
}
function showTyping(){
  const t=document.createElement("div"); t.className="dms-typing"; t.id="dms-typing";
  t.innerHTML="<span></span><span></span><span></span>"; scroll.appendChild(t); toBottom();
}
function hideTyping(){ const t=$("dms-typing"); if(t) t.remove(); }
function toBottom(){ scroll.scrollTop=scroll.scrollHeight; }

/* ---------- WhatsApp / call handoff ---------- */
let connectShown = false;
function showWhatsApp(){
  connectShown = true;
  const q = pendingQualification || {};
  let msg = CONFIG.whatsappGreeting || "Hi, I'd like to talk.";
  const ctx = [];
  if(q.service) ctx.push("Interested in: " + q.service);
  if(q.summary) ctx.push(q.summary);
  if(ctx.length) msg += "\n\n" + ctx.join("\n");
  const num = String(CONFIG.whatsappNumber).replace(/\D/g,"");
  const url = "https://wa.me/" + num + "?text=" + encodeURIComponent(msg);
  const phone = (CONFIG.supportPhone||"").trim();

  const card=document.createElement("div"); card.className="dms-leadcard";
  card.innerHTML=`
    <h4>Talk to our team now</h4>
    <p>WhatsApp us or call — whichever is easier for you.</p>
    <a class="dms-wa-btn" href="${url}" target="_blank" rel="noopener">
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.148-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
      Chat on WhatsApp
    </a>
    ${phone ? `<a class="dms-call-btn" href="tel:${phone.replace(/[^\d+]/g,"")}">
      ${icon("phone",18)}
      Call ${escapeHtml(phone)}
    </a>` : ``}`;
  scroll.appendChild(card); toBottom();
  const wab=card.querySelector(".dms-wa-btn"); if(wab) wab.addEventListener("click",()=>track("converted",{via:"whatsapp"}));
  const cab=card.querySelector(".dms-call-btn"); if(cab) cab.addEventListener("click",()=>track("converted",{via:"call"}));
  track("connect_shown",{mode:"whatsapp"});
  pendingQualification=null;
}

/* ---------- lead capture card ---------- */
function showLeadForm(){
  connectShown = true;
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
  /* conversion event for Google Tag Manager (separate name so it can be mapped on its own) */
  try{ window.dataLayer=window.dataLayer||[]; window.dataLayer.push({event:"chatLeadEvent"}); }catch(e){}
  if(CONFIG.apiBase){
    fetch(CONFIG.apiBase.replace(/\/+$/,"")+"/lead",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(lead)})
      .then(function(r){ if(!r.ok && window.console) console.warn("DMS chat: lead was not delivered (HTTP "+r.status+")"); })
      .catch(function(){ if(window.console) console.warn("DMS chat: lead request failed"); });
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
    } else if(!connectShown && (CONFIG.connectAfterMessages|0) > 0){
      // Safety net: if the bot hasn't offered contact yet, offer it after N visitor messages.
      const userMsgs = history.filter(m=>m.role==="user").length;
      if(userMsgs >= (CONFIG.connectAfterMessages|0)){
        addBot("By the way — if it's easier, you can talk to our team directly right now:");
        if(CONFIG.connectMode === "whatsapp" && CONFIG.whatsappNumber){ showWhatsApp(); }
        else { showLeadForm(); }
      }
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

/* ---------- proactive engagement: teaser bubble (legacy full open kept as fallback) ---------- */
function pickTeaserHook(){
  const hooks = (CONFIG.teaser && CONFIG.teaser.hooks) || {};
  const path = (location.pathname||"").toLowerCase();
  for(const key in hooks){ if(key && path.indexOf(key) !== -1) return hooks[key]; }
  return hooks[""] || "👋 Can I help you find the right service?";
}
function showTeaser(){
  if(panel.classList.contains("open") || greeted) return;
  let done0=false;
  try{ done0 = sessionStorage.getItem("dms_teaser_done")==="1"; }catch(e){}
  if(done0) return;
  const t=document.createElement("div"); t.id="dms-teaser";
  const chips=((CONFIG.teaser&&CONFIG.teaser.chips)||[]).slice(0,3);
  const num=String(CONFIG.whatsappNumber||"").replace(/\D/g,"");
  const waMsg=encodeURIComponent(CONFIG.whatsappGreeting||"Hi, I'd like to talk.");
  t.innerHTML = `<button class="dms-t-x" aria-label="Dismiss">&times;</button>
    <div class="dms-t-msg">${escapeHtml(pickTeaserHook())}</div>
    <div class="dms-t-chips">${chips.map(function(c){
      if(/whatsapp/i.test(c) && num){
        return '<a class="dms-t-chip" data-wa="1" target="_blank" rel="noopener" href="https://wa.me/'+num+'?text='+waMsg+'">'+escapeHtml(c)+'</a>';
      }
      return '<button class="dms-t-chip" data-q="'+escapeHtml(c)+'">'+escapeHtml(c)+'</button>';
    }).join("")}</div>`;
  document.getElementById("dms-root").appendChild(t);
  track("teaser",{action:"shown"});
  function done(){ try{ sessionStorage.setItem("dms_teaser_done","1"); }catch(e){} if(t.parentNode) t.remove(); }
  t.querySelector(".dms-t-x").addEventListener("click",function(){ done(); track("teaser",{action:"dismissed"}); });
  t.querySelectorAll("button.dms-t-chip[data-q]").forEach(function(b){
    b.addEventListener("click",function(){
      const q=b.getAttribute("data-q"); done();
      panel.classList.add("open"); launcher.style.display="none";
      if(!tracked.open){ tracked.open=true; track("open",{mode:"teaser"}); }
      if(!greeted){ greeted=true; renderGreeting(); }
      setTimeout(function(){ send(q); }, 60);
    });
  });
  const wa=t.querySelector('[data-wa="1"]');
  if(wa) wa.addEventListener("click",function(){ done(); track("converted",{via:"whatsapp",source:"teaser"}); });
}
(function scheduleProactive(){
  const tz=CONFIG.teaser||{};
  if(tz.enabled){
    let fired=false;
    function arm(){ if(fired) return; fired=true; setTimeout(showTeaser, Math.max(0, tz.delayMs|0)); }
    if(tz.requireEngagement){
      ["scroll","pointerdown","mousemove","touchstart","keydown"].forEach(function(ev){
        window.addEventListener(ev, arm, {once:true, passive:true});
      });
      setTimeout(arm, 30000); // fallback for readers who never scroll
    } else { arm(); }
  } else if((CONFIG.autoOpenDelayMs|0) > 0){
    let alreadyAuto=false;
    try{ alreadyAuto = CONFIG.autoOpenOnce && localStorage.getItem("dms_auto_opened")==="1"; }catch(e){}
    if(!alreadyAuto){
      setTimeout(function(){
        if(panel.classList.contains("open") || greeted) return;
        openPanel(true);
        try{ if(CONFIG.autoOpenOnce) localStorage.setItem("dms_auto_opened","1"); }catch(e){}
      }, CONFIG.autoOpenDelayMs);
    }
  }
})();

/* ---------- count this visit once (top of funnel) ---------- */
try{ if(!sessionStorage.getItem("dms_ev_loaded")){ sessionStorage.setItem("dms_ev_loaded","1"); track("loaded"); } }
catch(e){ track("loaded"); }

/* ---------- quick contact bar: one-tap WhatsApp / Call, always visible ---------- */
(function initQuickbar(){
  const bar = $("dms-quickbar"); if(!bar) return;
  const num = String(CONFIG.whatsappNumber||"").replace(/\D/g,"");
  const phone = (CONFIG.supportPhone||"").trim();
  const waMsg = encodeURIComponent(CONFIG.whatsappGreeting || "Hi, I'd like to talk.");
  let html = "";
  if(num) html += `<a class="dms-qb-wa" href="https://wa.me/${num}?text=${waMsg}" target="_blank" rel="noopener">
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.148-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
    WhatsApp us</a>`;
  if(phone) html += `<a class="dms-qb-call" href="tel:${phone.replace(/[^\d+]/g,"")}">
    ${icon("phone",17)}
    Call us</a>`;
  if(!html){ const strip=$("dms-strip"); if(strip) strip.style.display="none"; return; }
  bar.innerHTML = html;
  const wa=bar.querySelector(".dms-qb-wa"); if(wa) wa.addEventListener("click",()=>track("converted",{via:"whatsapp",source:"quickbar"}));
  const ca=bar.querySelector(".dms-qb-call"); if(ca) ca.addEventListener("click",()=>track("converted",{via:"call",source:"quickbar"}));
})();
/* ---------- header buttons (WhatsApp / Call) and "show topics again" ---------- */
(function initHeaderActions(){
  const num = String(CONFIG.whatsappNumber||"").replace(/\D/g,"");
  const phone = (CONFIG.supportPhone||"").trim();
  const waMsg = encodeURIComponent(CONFIG.whatsappGreeting || "Hi, I'd like to talk.");
  const wa=$("dms-h-wa"), call=$("dms-h-call");
  if(wa){
    if(num){ wa.href="https://wa.me/"+num+"?text="+waMsg; wa.target="_blank"; wa.rel="noopener"; wa.addEventListener("click",()=>track("converted",{via:"whatsapp",source:"header"})); }
    else wa.style.display="none";
  }
  if(call){
    if(phone){ call.href="tel:"+phone.replace(/[^\d+]/g,""); call.addEventListener("click",()=>track("converted",{via:"call",source:"header"})); }
    else call.style.display="none";
  }
  const reset=$("dms-reset");
  if(reset) reset.addEventListener("click",()=>{
    const have=scroll.querySelector(".dms-chips");
    if(have){ have.scrollIntoView({block:"nearest"}); return; }      // already showing
    if(busy) return;
    addChips(INTENT_CHIPS);
  });
})();
})();
</script>
@endverbatim
