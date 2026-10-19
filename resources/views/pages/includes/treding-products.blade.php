
  <div class="container pro-bg">
    <h2 class="home-head pull-left tab-head">Produits tendance</h2>
    <ul class="nav nav-tabs pro-tab pull-right">
      <li class="active"><a href="#featurep" data-toggle="tab">Featured</a></li>
      <li><a href="#bestp" data-toggle="tab">BestSeller</a></li>
      <li><a href="#latestp" data-toggle="tab">Latest</a></li>

    </ul>
    <hr>
	
    <div class="tab-content pro-nepr">
      <div class="tab-pane active" id="featurep">
        <div class="pro-nepr">
          <div class="row thummargin">
            <div id="feature" class="owl-theme owl-carousel">
              @php
              afficheArticle(2);
              afficheArticle(3);  
			  afficheArticle(2);
              afficheArticle(3);  
			  afficheArticle(2);
              afficheArticle(3);
              @endphp
              
            </div>
          </div>


        </div>
      </div>
      <script type="text/javascript">
        $(document).ready(function () {
          $("#feature").owlCarousel({
            itemsCustom: [
              [0, 2],
              [375, 2],
              [600, 3],
              [768, 3],
              [992, 4],
              [1200, 5]
            ],
            // autoPlay: 1000,
            navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
            navigation: true,
            pagination: false
          });
        });
      </script>
	  
      <div class="tab-pane" id="bestp">
        <div class="row thummargin">
          <div id="best" class="owl-theme owl-carousel">
            @php
              afficheArticle(2);
              afficheArticle(3);  
			  afficheArticle(2);
             
              @endphp
			  
		 </div>
        </div>
      </div>

      <script type="text/javascript">
        $(document).ready(function () {
          $("#best").owlCarousel({
            itemsCustom: [
              [0, 2],
              [375, 2],
              [600, 3],
              [768, 3],
              [992, 4],
              [1200, 5]
            ],
            // autoPlay: 1000,
            navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
            navigation: true,
            pagination: false
          });
        });
      </script>

      <div class="tab-pane" id="latestp">
        <div class="row thummargin">
          <div id="latest" class="owl-theme owl-carousel">
            
		 </div>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function () {
      $("#latest").owlCarousel({
        itemsCustom: [
          [0, 2],
          [375, 2],
          [600, 3],
          [768, 3],
          [992, 4],
          [1200, 5]
        ],
        // autoPlay: 1000,
        navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
        navigation: true,
        pagination: false
      });
    });
  </script>
  
