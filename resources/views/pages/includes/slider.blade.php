

<div class="container">
    <div class="row">

        <div class="col-md-9 col-sm-12 slide-padding"> 
           
            <div class="homeslide">
                <div id="slideshow0" class="owl-carousel owl-theme">
                    @foreach ($sliders as $s)
                    @php 
                    //dd(asset('storage/images/Articles/' . $s->photo1));
                    
                    @endphp
                    <div class="text-center">                        
                        <a href="#">
                                <img src="{{ asset('storage/images/Articles/' . $s->photo1) }}" 
                                    class="img-responsive center-block" alt="slider" >                                    
                            </a>
                        </div>

                    @endforeach
                </div>
            </div>
        </div>

        {{-- FIX ICI : on met les bannières DANS la colonne droite --}}
        <div class="col-md-3 sliderightbanner hidden-sm hidden-xs">
            <div class="rightbanner">
                <div class="banner-effect">
                    <a href="#">
                        <img src="{{ asset('templateFront/image/cache/catalog/rightsidebanner/right-2-263x215.jpg') }}" alt="banner"
                            class="img-responsive" />
                    </a>
                </div>
                <div class="banner-effect">
                    <a href="#">
                        <img src="{{ asset('templateFront/image/cache/catalog/rightsidebanner/right-1-263x215.jpg') }}" alt="banner"
                            class="img-responsive" />
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () { 
       
        $("#slideshow0").owlCarousel({
            itemsCustom: [[0, 1]],
            autoPlay: 2500,
            animateIn: 'fadeIn',
            animateOut: 'fadeOut',
            loop: true,
            navigationText: [
                '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                '<i class="fa fa-angle-right" aria-hidden="true"></i>'
            ],
            navigation: true,
            pagination: false
        });
    });
</script>

<style>
    #slideshow0 .owl-item img {
        width: 100%;
        height: clamp(220px, 32vw, 430px);
        object-fit: contain;
        background: #fff;
    }

    .product-thumb .image {
        height: 230px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .product-thumb .image > a:first-child {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-thumb .image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .cat-img .block-cat-wr img {
        width: 100%;
        height: 150px;
        object-fit: contain;
    }
</style>