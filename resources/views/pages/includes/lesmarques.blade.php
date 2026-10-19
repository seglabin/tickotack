
  <div class="">
    <div class="container">
      <div class="col-md-12 logo-slider">
        <div id="carousel0" class="owl-carousel owl-theme">
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/1-150x100.png') }}" alt="Canon" class="center-block img-responsive" />
          </div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/2-150x100.png') }}" alt="NFL" class="center-block img-responsive" />
          </div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-2-150x100.png') }}" alt="Coca Cola"
              class="center-block img-responsive" /></div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-3-150x100.png') }}" alt="Sony" class="center-block img-responsive" />
          </div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-4-150x100.png') }}" alt="Nintendo"
              class="center-block img-responsive" /></div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-5-150x100.png') }}" alt="Harley Davidson"
              class="center-block img-responsive" /></div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-58-150x100.png') }}" alt="Disney"
              class="center-block img-responsive" /></div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-6-150x100.png') }}" alt="Dell" class="center-block img-responsive" />
          </div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-7-150x100.png') }}" alt="RedBull"
              class="center-block img-responsive" /></div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/brand-8-150x100.png') }}" alt="Burger King"
              class="center-block img-responsive" /></div>
          <div class="text-center"><img src="{{ asset('templateFront/image/cache/catalog/brand/1-150x100.png') }}" alt="Starbucks"
              class="center-block img-responsive" /></div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function () {
      $("#carousel0").owlCarousel({
        itemsCustom: [
          [0, 2],
          [412, 3],
          [600, 4],
          [768, 5],
          [992, 5],
          [1200, 6]
        ],
        autoPlay: 2500,
        loop: true,
        navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
        navigation: false,
        pagination: false
      });
    });
  </script>
  