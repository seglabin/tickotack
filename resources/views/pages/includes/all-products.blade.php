<div class="container pro-bg">
    <div class="category-tab pro-bg pro-nepr">
        <h2 class="home-head pull-left tab-head">Tous les products</h2>
        <ul class="nav nav-tabs pro-tab pull-right">

            @foreach ($categvisible as $i => $cv)
                <li class="{{ $i == 0 ? 'active' : '' }}"><a href="#tabcateg{{$cv->id}}"
                        data-toggle="tab">{{$cv->libelle}}</a>
                </li>
            @endforeach

        </ul>
        <hr>

        <div class="tab-content pro-nepr">
            @foreach ($categvisible as $i => $cv)
                <div class="tab-pane {{ $i == 0 ? 'active' : '' }}" id="tabcateg{{$cv->id}}">
                    <div class="pro-nepr">
                        <div class="row thummargin">
                            <div id="slidcat{{$cv->id}}" class="owl-theme owl-carousel">
                                @php

                                    $produits = collect(DB::select(" SELECT * FROM articles WHERE idcategorie = '" . $cv->id . "' ORDER BY libelle "));

                                    foreach ($produits as $key => $p) {
                                        afficheArticle($p->id);
                                    }

                                  @endphp

                            </div>
                        </div>

                    </div>
                </div>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#slidcat{{$cv->id}}").owlCarousel({
                            itemsCustom: [
                                [0, 2],
                                [375, 2],
                                [600, 3],
                                [768, 3],
                                [992, 4],
                                [1200, 5]
                            ],
                            //autoPlay: 1000,
                            navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                            navigation: true,
                            pagination: true
                        });
                    });
                </script>
            @endforeach

        </div>
    </div>
</div>