@extends('layoutfront')

 <?php
  $repertCat = "storage/images/Categories/";
  $repertProd = "storage/images/Articles/";
 ?>

@section('title', $product->id ?? 'Produit')

@section('content')
<div class="container">

  {{-- Breadcrumb --}}
  <ul class="breadcrumb">
    <li><a href="{{ route('home') }}"><i class="fa fa-home"></i></a></li>
    @if($product->idcategorie!= null)
      <li>
        <a href="{{ route('category', ['slug' => $product->idcategorie]) }}">
          {{ $product->libcategorie ?? 'Category' }}
        </a>
      </li>
    @endif
    <li>{{ $product->libelle ?? 'Product' }}</li>
  </ul>

  <div class="row">
    <div id="content" class="col-sm-12">

      <div class="row">

        {{-- LEFT : Images --}}
        <div class="col-sm-5">
          <ul class="thumbnails">
            <li>
              <a class="thumbnail" href="{{ asset($repertProd.$product->photo1) }}">
                <img id="product-main-image"
                     src="{{ asset($repertProd.$product->photo1) }}"
                     class="img-responsive"
                     alt="{{ $product->libelle }}">
              </a>
            </li>
<!--  -> -->
            <li class="image-additional">
              <div class="row">
                
                @for($k = 2; $k <= 5; $k++ )   
                        
                   @if(file_exists($repertProd.$product->{'photo'.$k}))             
                  <div class="col-xs-3">
                    <a class="thumbnail product-thumb"
                       href="javascript:void(0)"
                       data-img="{{ asset($repertProd.$product->{'photo'.$k}) }}">
                      <img src="{{ asset($repertProd.$product->{'photo'.$k}) }}" class="img-responsive">
                    </a>
                  </div>
                  @endif
                @endfor
              </div>
            </li>
          </ul>
        </div>

        {{-- RIGHT : Infos --}}
        <div class="col-sm-7">
          <h1>{{ $product->libelle }}</h1>

          <ul class="list-unstyled">
            <li>Brand: <a href="{{ route('brands') }}">{{ $product->libmarque }}</a></li>
            <li>Product Code: {{ $product->reference }}</li>
            <li>Reward Points: {{-- $product['reward_points'] --}}</li>
            <li>Availability: <span class="text-success">{{-- $product['availability'] --}}</span></li>
          </ul>

          <ul class="list-unstyled">
            <li><h2>${{ number_format($product->prix, 2) }}</h2></li>
            <li>Price in reward points: {{-- $product['reward_price'] --}}</li>
          </ul>

          <div class="price-tax">
            <small> Prix tiers
              {{--@foreach($product['price_tiers'] as $qty => $price)
                {{ $qty }} or more: ${{ number_format($price, 2) }}<br>
              @endforeach--}}
            </small>
          </div>

          <div >
            <hr>

            <h3>Available Options</h3>

            <div class="form-group required"> Options??
              <label class="control-label">Select</label>
              <select class="form-control">
                <option>--- Please Select ---</option>
               {{-- 
               @foreach($product['options'] as $opt)
               <option>{{ $opt }}</option>
               @endforeach
               --}} 
              </select>
            </div>

            <hr>

            <div class="form-group">
              <label class="control-label">Qty</label>
              <div class="input-group" style="max-width:140px;">
                <span class="input-group-btn">
                  <button class="btn btn-default" id="qty-minus">-</button>
                </span>
                <input type="text" value="1" id="input-quantity" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default" id="qty-plus">+</button>
                </span>
              </div>
            </div>

            <div class="form-group">          
              <button class="btn btn-primary"  id="button-cart" >Add to Cart</button>
              <button class="btn btn-default"><i class="fa fa-heart"></i></button>
              <button class="btn btn-default"><i class="fa fa-bar-chart"></i></button>
            </div>

            <div class="rating">
              <i class="fa fa-star-o"></i>
              <i class="fa fa-star-o"></i>
              <i class="fa fa-star-o"></i>
              <i class="fa fa-star-o"></i>
              <i class="fa fa-star-o"></i>
              <a href="#tab-review" data-toggle="tab">0 reviews</a>
            </div>

          </div>
        </div>
      </div>

      {{-- Tabs --}}
      <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-description" data-toggle="tab">Description</a></li>
        <li><a href="#tab-specification" data-toggle="tab">Specification</a></li>
        <li><a href="#tab-review" data-toggle="tab">Reviews (0)</a></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="tab-description">
          {!! $product->description !!}
        </div>

        <div class="tab-pane" id="tab-specification"> Spécification??
          <table class="table table-bordered">
            {{--@foreach($product['specs'] as $spec)
              <tr>
                <td>{{ $spec['Nom'] }}</td>
                <td>{{ $spec['Valeur'] }}</td>
              </tr>
            @endforeach--}}
          </table>
        </div>

        <div class="tab-pane" id="tab-review">
          <p>No reviews yet.</p>
        </div>
      </div>

      {{-- Related products --}}
      <h3 class="title-related">Related Products</h3>
      <div class="row">
        @foreach($relatedProducts as $rp)
          <div class="product-layout col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="product-thumb">
              <div class="image">
                <a href="{{ route('product', ['slug' => $rp->id]) }}">
                  <img src="{{ asset($repertProd.$rp->photo1) }}" class="img-responsive">
                </a>
              </div>
              <div class="caption text-center">
                <h4>{{ $rp->libelle }}</h4>
                <p class="price">{{ number_format($rp->prix, 2) }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

  $('#qty-plus').click(function(){
    var v = parseInt($('#input-quantity').val()) || 1;
    $('#input-quantity').val(v + 1);
  });

  $('#qty-minus').click(function(){
    var v = parseInt($('#input-quantity').val()) || 1;
    $('#input-quantity').val(Math.max(1, v - 1));
  });

  $('.product-thumb').click(function(){
    $('#product-main-image').attr('src', $(this).data('img'));
  });

  $('#button-cart').click(function(){
    //alert('Produit ajouté au panier (maquette)');
    var q = $('#input-quantity').val();
    ajoutpanier('{{$product->id}}',q);
  });

  // Zoom image (si plugin présent)
  if ($.fn.elevateZoom) {
    $('#product-main-image').elevateZoom({
      zoomType: 'inner',
      cursor: 'crosshair'
    });
  }

});
</script>
@endpush
