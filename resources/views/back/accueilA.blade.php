@extends('layoutback', [])
@section('content')
@php
//$menusUser = session('menusUser');
 //dd($menusUser);
@endphp

<section class="banner__area-four banner__bg-four" data-background="assets/templeApexa/img/banner/h5_banner_bg.jpg">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <div class="banner__content-four">
                    <h2 class="title" data-aos="fade-up" data-aos-delay="100">Agency's Vision for the <span>Next Generation</span> of Advertising</h2>
                    <p data-aos="fade-up" data-aos-delay="300">Apexa helps you to convert your data into a strategic asset and get business insights Apexa helps you to convert.</p>
                    <a href="{{ url('site') }}" class="btn" data-aos="fade-up" data-aos-delay="600">Ouvrir le site</a>
                    <div class="shape">
                        <img src="assets/templeApexa/img/banner/h5_banner_shape01.png" alt="" class="rotateme">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-9">
                <div class="banner__img-two">
                    <img src="assets/templeApexa/img/banner/h5_banner_img01.png" alt="">
                    <img src="assets/templeApexa/img/banner/h5_banner_img02.png" alt="">
                    <div class="img__shape">
                        <img src="assets/templeApexa/img/banner/h5_banner_shape02.png" alt="" class="rightToLeft">
                        <img src="assets/templeApexa/img/banner/h5_banner_shape03.png" alt="" class="alltuchtopdown">
                        <img src="assets/templeApexa/img/banner/h5_banner_shape04.png" alt="">
                        <img src="assets/templeApexa/img/banner/h5_banner_shape05.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection