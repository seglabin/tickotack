<div class="container">
    <div class="cat-img">
        <div class="pro-bg pro-nepr">
            <h2>Top Categories</h2>
            <hr>

            <div class="row cat-mr">
                <div class="beyque-p">
                    <div id="featured_category">
                        <div class="category_module p-0">
                            <div class="beyqu-center-column-btn">
                                @foreach ($categtop as $c)

                                    <div class="block-cat-wr col-xs-12 col-md-3 pl-5 pr-5">
                                        <a href="{{ route('category', ['slug' => $c->id]) }}">
                                            <img src="{{ asset('storage/images/Categories/' . $c->photo) }}"
                                                class="img-responsive center-block" alt="">
                                        </a>
                                        <h4 class="text-center cat-name-height">
                                            <a href="{{ route('category', ['slug' => $c->id]) }}" class="">
                                                {{ $c->libelle }} ({{ $c->nbprod }})
                                            </a>
                                        </h4>
                                    </div>

                                @endforeach

                            </div> {{-- /.beyqu-center-column-btn --}}
                        </div> {{-- /.category_module --}}
                    </div> {{-- /#featured_category --}}
                </div>
            </div>

        </div>
    </div>
</div>