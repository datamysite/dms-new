{{-- Enquiry popups of the redesigned pages: "Need a Help?" (#newsletter-modal-u, opened by every .open-newsletter button), the SEO audit popups
     (#seo-cta-modal-u, #seo-ads-cta-modal-u) and the exit-intent popup (#ov). Included once by web/home-v2/master.blade.php. The forms post to
     enquiry.help.submit / enquiry.lite.submit and are handled by public/assets/js/enquiry.js. The title elements are <div class="modal-ttl"> on purpose
     (not headings) so the page outline stays h1 > h2. Edit the popups here, not in the master. --}}
  <div class="modal fade" id="newsletter-modal-u" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                  <span aria-hidden="true">&times;</span> 
                </button> 
              </div>
              <div class="modal-body p-0 row">
                  <div class="col-12 col-lg-5 ad p-0"> 
                    <img src="{{URL::to('/public/popup-min.png')}}" class="popup-des" alt="Need a Help?"  loading="lazy" /> 
                    <img src="{{URL::to('/public/popup-m.jpg')}}" class="popup-mob" width="100%" alt="Need a Help?" loading="lazy" /> 
                  </div>
                  <div class="details col-12 col-lg-7">
                    <form action="{{route('enquiry.help.submit')}}" id="enquiry-help-form">
                      @csrf
                      <div class="modal-ttl">Need a Help?</div>
                      <p>
                        <small class="para">
                          Looking for a Custom <strong>Marketing Plan</strong>? 
                        <br>
                          Get expert guidance tailored to your brand’s success!</small>
                      </p>
                      <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                      </div>
                      <div class="form-group">
                        <input type="tel" name="mailphone" id="phone-field3" class="form-control" required>
                        <input type="hidden" name="phone" id="fullphone-field3" required="">
                      </div>
                      <div class="form-group">
                        <input type="email" class="form-control help-email" name="email" placeholder="Email" required>
                      </div>
                      <br>
                      <div class="form-group">
                        <button type="reset" class="btn btn-sm btn-default cancel">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>&nbsp;&nbsp;
                        <input type="submit" class="btn btn-sm btn-theme" value="Proceed">
                      </div>
                      <img src="{{URL::to('/public/loader-gif-m.gif')}}" alt="Loader gif" width="150" height="31" class="help-loading" loading="lazy">
                    </form>
                  </div>
              </div>
          </div>
      </div>
  </div>


  <div class="modal fade" id="seo-cta-modal-u" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                  <span aria-hidden="true">&times;</span> 
                </button> 
              </div>
              <div class="modal-body p-0 row">
                  <div class="col-12 col-lg-5 ad p-0"> 
                    <img src="{{URL::to('/public/popup-min.png')}}" class="popup-des" alt="Need a Help?"  loading="lazy" /> 
                    <img src="{{URL::to('/public/popup-m.jpg')}}" class="popup-mob" width="100%" alt="Need a Help?" loading="lazy" /> 
                  </div>
                  <div class="details col-12 col-lg-7">
                    <form action="{{route('enquiry.help.submit')}}" id="seo-help-form">
                      @csrf
                      <div class="modal-ttl">Need a Help?</div>
                      <p>
                        <small class="para">
                          We’re here to rank your <strong>website higher</strong>! 
                          </small>
                      </p>
                      <p class="seo-prompt-para">
                        Get a FREE SEO Audit Report for your website. Just fill out the form and our experts will send you a detailed audit and help you fix the issues that stop you from ranking on your desired keywords.
                      </p>
                      <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                      </div>
                      <div class="form-group">
                        <input type="tel" name="mailphone" id="phone-field4" class="form-control" required>
                        <input type="hidden" name="phone" id="fullphone-field4" required="">
                      </div>
                      <div class="form-group">
                        <input type="email" class="form-control help-email2" name="email" placeholder="Email" required>
                      </div>
                      <div class="form-group">
                        <input type="text" class="form-control" name="website_link" placeholder="Website" required>
                      </div>

                      <div class="form-group">
                        <button type="reset" class="btn btn-sm btn-default cancel">&nbsp;&nbsp;Cancel&nbsp;&nbsp;</button>&nbsp;&nbsp;
                        <input type="submit" class="btn btn-sm btn-theme" value="Proceed">
                      </div>
                      <img src="{{URL::to('/public/loader-gif-m.gif')}}" alt="Loader gif" width="150" height="31" class="help-loading2" loading="lazy">
                    </form>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="modal fade" id="seo-ads-cta-modal-u" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-body p-0 row">
                  <div class="aside-form">
                  <div class="af-title">Zero-Cost SEO Audit</div>
                  <div class="af-sub"><small><small>Know Your Rank. Improve.</small></small></div>
                    <form action="{{route('enquiry.help.submit')}}" id="seo-ads-form" >
                        @csrf
                        <input type="text" name="name" placeholder="Name" class="form-control" required>
                        
                        <input type="tel" name="mailphone" id="phone-fieldseo" class="form-control" required>
                        <input type="hidden" name="phone" id="fullphone-fieldseo" required="">
                        <input type="email" name="email" placeholder="Email" class="form-control seo-ads-email" required>
                      
                        <input type="text" class="form-control" name="website_link" placeholder="Website" required>
                        
                        <input type="hidden" name="service" value="SEO Services">
                                                
                        <button class="btn btn-theme">Submit</button>
                        <button type="button" class="btn btn-default close">Close</button>
                        <img src="{{URL::to('/public/loader-gif.gif')}}" alt="loader image" width="80" height="17" class="help-loading2 seo-loading" loading="lazy">
                    </form>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="ov" id="ov">
    <div class="pop" role="dialog" aria-modal="true" aria-label="Get a free strategy">

      <button class="xbtn" onclick="closeExitPopup()" aria-label="Close">✕</button>

      <!-- HEADER -->
      <div class="hd">
        <div class="pill"><span class="dot"></span> Before You Leave</div>
        <div class="modal-ttl">Get a <em>Free Strategy</em><br>From Dubai's Top Ad Agency</div>
        <p>Our experts respond in 60 min — no commitment, no cost.</p>
      </div>

      <!-- FORM BODY -->
      <div class="bd">

        <form action="{{route('enquiry.lite.submit')}}" id="enquiry-form-exit">
            @csrf
            <div class="urg">
              <span class="ui">⚡</span>
              <span class="ut"><strong>Free slots</strong> available today</span>
              <span class="ucd" id="cd">29:59</span>
            </div>

            <div class="fw" id="fw">
              <div style="display:flex;flex-direction:column;gap:8px">
                <div class="row2">
                  <input type="text" name="name" placeholder="Your Name" required>
                  <div>
                      <input type="tel" name="mainphone" id="phone-field7" required="">
                      <input type="hidden" name="phone" id="fullphone-field7" required="">
                  </div>
                </div>
                <div class="row2 row3">
                  <input type="email" class="exit-email" name="email" placeholder="Your Email" required>
                </div>
                <select name="service" required aria-label="I need help with">
                  <option value="" disabled selected>I need help with…</option>
                  <option>Billboard Advertising</option>
                  <option>Transit Media (Metro, Bus, Taxi, Airport)</option>
                  <option>PR Coverage (Gulf News, Forbes, Khaleej Times)</option>
                  <option>Influencer Marketing</option>
                  <option>Digital Marketing</option>
                  <option>SEO &amp; Website</option>
                  <option>Experiential Marketing</option>
                  <option>Lead Generation</option>
                  <option>Radio Advertising</option>
                  <option>CGI / Creative Ads</option>
                  <option>Celebrity Marketing</option>
                  <option>Event Marketing &amp; Sponsorships</option>
                  <option>Global Marketing (Saudi, India, London)</option>
                  <option>Full 360° Strategy</option>
                </select>
                <button class="cta" type="submit">Claim My Free Strategy Call <span class="ar">→</span></button>
              </div>

              <div style="display:flex;flex-direction:column;gap:6px;margin-top:8px">
                <a href="{{ v2_wa_url() }}" class="wa" target="_blank" rel="noopener">
                  <span class="wad"></span> WhatsApp: {{ v2_biz('phone_display') }}
                </a>
                <p class="prv">🔒 No spam · 100% free · ⭐ 5.0 Google rated</p>
                <div class="skip"><button onclick="closeExitPopup()">No thanks, I'll figure it out myself</button></div>

                <div class="loading exit-loading"><img src="{{URL::to('/public/loader-gif.gif')}}" width="80" height="17" alt="" class="newsletter-loader" loading="lazy"></div>
              </div>
            </div>
          </form>
      </div>
    </div>
  </div>
