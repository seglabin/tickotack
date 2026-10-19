<footer>

  <div class="foot-top">
    <div class="container">
      <div>
        <div class="slide-padding">
          <div class="service">
            <div class="row">
              <ul class="col-xs-12 col-sm-3 col-md-3 list-inline">
                <li class="del1">
                  <svg width="36px" height="36px">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#gift"></use>
                  </svg>
                </li>
                <li class="se1 text-xs-left">
                  <h4>special gift cards</h4>
                  <p>give a perfect gift</p>
                </li>
              </ul>

              <ul class="col-xs-12 col-sm-3 col-md-3 sborder list-inline">
                <li class="del4">
                  <svg width="36px" height="36px">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#pay"></use>
                  </svg>
                </li>
                <li class="se1  text-xs-left">
                  <h4>secure payment</h4>
                  <p>100% secure payment</p>
                </li>
              </ul>

              <ul class="col-xs-12 col-sm-3 col-md-3 sborder list-inline">
                <li class="del2">
                  <svg width="36px" height="36px">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#support"></use>
                  </svg>
                </li>
                <li class="se1  text-xs-left">
                  <h4>24/7 support</h4>
                  <p>online support 24/7</p>
                </li>
              </ul>

              <ul class="col-xs-12 col-sm-3 col-md-3 sborder list-inline">
                <li class="del3">
                  <svg width="36px" height="36px">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#ship"></use>
                  </svg>
                </li>
                <li class="se1  text-xs-left">
                  <h4>free shipping</h4>
                  <p>on order over $101</p>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">

      <aside id="column-left1" class="col-sm-3">
        <div>
          <h5>
            <span>Contact us</span>
            <button type="button" class="btn btn-primary toggle collapsed" data-toggle="collapse" data-target="#contact"></button>
          </h5>

          <div id="contact" class="collapse footer-collapse footcontact">
            <ul class="list-unstyled f-left">
              <li><i class="fa fa-map-marker"></i>42 Dream Housee, Dreammy street, 7131 Dreamville, USA</li>
              <li><i class="fa fa-envelope"></i>company@gmail.com</li>
              <li><i class="fa fa-phone"></i>(+91) - 540-025-124553</li>
              <li><i class="fa fa-print" aria-hidden="true"></i> &nbsp; fax@company.com</li>
            </ul>
          </div>
        </div>
      </aside>

      <div class="col-sm-6">
        <div class="row">
          <div class="middle-footer">

            <div class="col-sm-4">
              <h5>
                Extras
                <button type="button" class="btn btn-primary toggle collapsed" data-toggle="collapse" data-target="#extra"></button>
              </h5>
              <div id="extra" class="collapse footer-collapse">
                <ul class="list-unstyled">
                  <li><a href="{{ route('brands') }}">Brands</a></li>
                  <li><a href="#" onclick="return false;">Gift Certificates</a></li>
                  <li><a href="#" onclick="return false;">Affiliate</a></li>
                  <li><a href="#" onclick="return false;">Specials</a></li>
                  <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
              </div>
            </div>

            <div class="col-sm-4">
              <h5>
                Information
                <button type="button" class="btn btn-primary toggle collapsed" data-toggle="collapse" data-target="#info"></button>
              </h5>
              <div id="info" class="collapse footer-collapse">
                <ul class="list-unstyled">
                  <li><a href="#" onclick="return false;">About Us</a></li>
                  <li><a href="#" onclick="return false;">Delivery Information</a></li>
                  <li><a href="#" onclick="return false;">Privacy Policy</a></li>
                  <li><a href="#" onclick="return false;">Terms &amp; Conditions</a></li>
                  <li><a href="{{ route('contact') }}">Contact Us</a></li>
                  <li><a href="{{ route('sitemap') }}">Site Map</a></li>
                </ul>
              </div>
            </div>

            <div class="col-sm-4">
              <h5>
                My Account
                <button type="button" class="btn btn-primary toggle collapsed" data-toggle="collapse" data-target="#account"></button>
              </h5>
              <div id="account" class="collapse footer-collapse">
                <ul class="list-unstyled lastb">
                  <li><a href="{{ route('login') }}">My Account</a></li>
                  <li><a href="#" onclick="return false;">Order History</a></li>
                  <li><a href="#" onclick="return false;">Wish List</a></li>
                  <li><a href="#" onclick="return false;">Newsletter</a></li>
                  <li><a href="#" onclick="return false;">Returns</a></li>
                </ul>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- Newsletter (maquette) --}}
      <aside id="column-right1" class="col-sm-3">
        <div class="inspire-newsletter inspire-newsletter" id="newsletter_1761238064" data-mode="default">
          <form id="formNewLestter" method="post" action="#" class="formNewLestter newsletter-bg" onsubmit="return false;">
            <div class="inner">
              <div class="">
                <h3>Newsletter</h3>
                <div class="description-top">
                  <p>Sign up for our newsletter</p>
                </div>
              </div>

              <div>
                <div class="fnews">
                  <div class="form-group">
                    <input type="text"
                           class="form-control input-md inputNew"
                           value="Your email address"
                           size="18"
                           name="email"
                           onblur="if(this.value=='')this.value='Your email address';"
                           onfocus="if(this.value=='Your email address')this.value='';">
                  </div>

                  <div class="button-submit">
                    <button type="submit" name="submitNewsletter" class="btn btn-block" onclick="alert('Newsletter (maquette)'); return false;">Subscribe</button>
                  </div>

                  <input type="hidden" value="1" name="action">
                  <div class="valid"></div>

                  <div class="description-bottom"></div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <script>
          // On laisse la place si le plugin newsletter existe dans le template,
          // mais on évite d'appeler une URL externe.
          try {
            if (window.jQuery && $('#newsletter_1761238064').inspireoNewsletter) {
              $("#newsletter_1761238064").inspireoNewsletter().work('Email is not valid!');
            }
          } catch (e) {}
        </script>
      </aside>

    </div>
  </div>

  <div class="container">
    <div class="foot-bottom">
      <div>
        <div class="row">

          <div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">
            <ul class="list-inline list-unstyled foot-social">
              <li><a href="#" onclick="return false;"><i class="fa fa-facebook"></i></a></li>
              <li><a href="#" onclick="return false;"><i class="fa fa-instagram"></i></a></li>
              <li><a href="#" onclick="return false;"><i class="fa fa-pinterest-p"></i></a></li>
              <li><a href="#" onclick="return false;"><i class="fa fa-google-plus"></i></a></li>
              <li><a href="#" onclick="return false;"><i class="fa fa-rss"></i></a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12">
            <ul class="list-inline list-unstyled foot-app">
              <li><a href="#" onclick="return false;"><img class="img-responsive center-block" src="{{ asset('image/catalog/app1.png') }}" alt="app"></a></li>
              <li><a href="#" onclick="return false;"><img class="img-responsive center-block" src="{{ asset('image/catalog/app2.png') }}" alt="app"></a></li>
              <li><a href="#" onclick="return false;"><img class="img-responsive center-block" src="{{ asset('image/catalog/app3.png') }}" alt="app"></a></li>
            </ul>
          </div>

          <div class="col-md-4 col-sm-3 col-xs-12 foot-pay text-right">
            <img class="img-responsive pull-right" src="{{ asset('image/catalog/payment.png') }}" alt="imgpayment">
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="foot-power">
    <div class="container">
      <div class="copy text-center">
        Powered By Lemastore &copy; {{ date('Y') }}
      </div>
    </div>
  </div>

</footer>

<a href="#" id="scroll" title="Scroll to Top" style="display:block;">
  <i class="fa fa-angle-up"></i>
</a>

<script>
  // Helpers UI “maquette” (sans backend)
  window.lemastore = window.lemastore || {};
  (function(api){
    api.money = function(v){
      try {
        var n = Math.round((parseFloat(v) || 0) * 100) / 100;
        return n.toFixed(2).replace('.', ',') + ' €';
      } catch(e) { return '0,00 €'; }
    };

    api.toast = function(msg){
      if(window.console) console.log('[lemastore]', msg);
      try {
        var el = document.createElement('div');
        el.className = 'alert alert-success';
        el.style.position = 'fixed';
        el.style.right = '20px';
        el.style.bottom = '20px';
        el.style.zIndex = 9999;
        el.style.maxWidth = '320px';
        el.innerHTML = msg;
        document.body.appendChild(el);
        setTimeout(function(){ el.parentNode && el.parentNode.removeChild(el); }, 1800);
      } catch(e) {}
    };

    api.qtyPlus = function(inputId){
      var el = document.getElementById(inputId);
      if(!el) return;
      var v = parseInt((el.value || '0').replace(/\D/g,''), 10);
      if(isNaN(v) || v < 1) v = 1;
      el.value = v + 1;
    };

    api.qtyMinus = function(inputId){
      var el = document.getElementById(inputId);
      if(!el) return;
      var v = parseInt((el.value || '0').replace(/\D/g,''), 10);
      if(isNaN(v) || v <= 1) v = 1;
      el.value = Math.max(1, v - 1);
    };

    api.swapProductImage = function(src){
      var img = document.getElementById('mainProductImage');
      if(img) img.src = src;
    };
  })(window.lemastore);
</script>

{{-- Preloader fallback --}}
<script>
(function () {
  function hide() {
    var el = document.getElementById('page-preloader');
    if (el) { el.classList.remove('visible'); el.style.display = 'none'; }
  }
  window.addEventListener('load', hide);
  setTimeout(hide, 1500);
})();
</script>
