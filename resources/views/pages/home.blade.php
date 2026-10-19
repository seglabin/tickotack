@extends('layoutfront')
@section('title', 'Accueil')
@section('content')

@include('pages.includes.suivi-colis-modal')

<div id="content" class="col-sm-12">

  <!-- Slider --> 
  @include('pages.includes.slider')
  <!-- Top catégorie -->
  @include('pages.includes.top-categories')

  <!-- All product -->
  @include('pages.includes.all-products')

  <script type="text/javascript">
    $(document).ready(function() {
      $(".tab-content .tab-pane #cattab").owlCarousel({
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
  <!-- Top  -->
 

  <!-- Treding products -->
  @include('pages.includes.treding-products')

  <!-- Deal of day products -->
  {{--@include('pages.includes.deal-day')--}}


  <!-- Les marques -->
  @include('pages.includes.lesmarques')


  <!-- Last blog -->
  @include('pages.includes.last-blog')



</div>

</div>
</div>
@endsection