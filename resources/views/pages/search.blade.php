@extends('layoutfront')
@section('title', 'Recherche')
@section('content')
                <div id="content" class="col-sm-12">
      <h1 class="heading">Search</h1>
      <label class="control-label" for="input-search">Search Criteria</label>
      <div class="row">
        <div class="col-sm-4">
          <input type="text" name="search" value="" placeholder="Keywords" id="input-search" class="form-control" />
        </div>
        <div class="col-sm-3">
          <select name="category_id" class="form-control">
            <option value="0">All Categories</option>
                                    <option value="59">Baby Care</option>
                                                            <option value="71">Cleaning &amp; Household</option>
                                                            <option value="60">Daily Needs</option>
                                                            <option value="70">Dairy &amp; Bakery</option>
                                                            <option value="68">Fruits</option>
                                                            <option value="69">Beverages</option>
                                                            <option value="20">Oil &amp; Spices</option>
                                                <option value="26">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PC</option>
                                                <option value="61">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;beuty &amp; Spa</option>
                                                <option value="63">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Smart Phone</option>
                                                <option value="62">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Smart watch</option>
                                                            <option value="27">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mac</option>
                                                <option value="66">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Decor</option>
                                                <option value="67">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Decor</option>
                                                <option value="64">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Printers</option>
                                                <option value="65">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trackballs</option>
                                                                        <option value="18">Personal Care</option>
                                                <option value="46">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Macs</option>
                                                            <option value="45">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Windows</option>
                                                                        <option value="25">Rice &amp; Grains</option>
                                                <option value="29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mice and Trackballs</option>
                                                            <option value="28">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monitors</option>
                                                <option value="35">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;test 1</option>
                                                <option value="36">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;test 2</option>
                                                            <option value="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Printers</option>
                                                <option value="31">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Scanners</option>
                                                <option value="32">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Web Cameras</option>
                                                                        <option value="57">Snacks &amp; Food</option>
                                                            <option value="17">Staple Food</option>
                                                            <option value="24">Fruit Juice</option>
                                              </select>
        </div>
        <div class="col-sm-3">
          <label class="checkbox-inline">
                        <input type="checkbox" name="sub_category" value="1" />
                        Search in subcategories</label>
        </div>
      </div>
      <p><br>
        <label class="checkbox-inline">
                    <input type="checkbox" name="description" value="1" id="description" />
                    Search in product descriptions</label>
      </p><br>
      <input type="button" value="Search" id="button-search" class="btn btn-primary" />
      <br><br><br><h2 class="heading">Search</h2>
            <p>Your shopping cart is empty!</p>
            </div>
    </div>
</div>
<script type="text/javascript"><!--
$('#button-search').bind('click', function() {
	url = 'index64b3.html?route=product/search';

	var search = $('#content input[name=\'search\']').prop('value');

	if (search) {
		url += '&search=' + encodeURIComponent(search);
	}

	var category_id = $('#content select[name=\'category_id\']').prop('value');

	if (category_id > 0) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}

	var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');

	if (sub_category) {
		url += '&sub_category=true';
	}

	var filter_description = $('#content input[name=\'description\']:checked').prop('value');

	if (filter_description) {
		url += '&description=true';
	}

	location = url;
});

$('#content input[name=\'search\']').bind('keydown', function(e) {
	if (e.keyCode == 13) {
		$('#button-search').trigger('click');
	}
});

$('select[name=\'category_id\']').on('change', function() {
	if (this.value == '0') {
		$('input[name=\'sub_category\']').prop('disabled', true);
	} else {
		$('input[name=\'sub_category\']').prop('disabled', false);
	}
});

$('select[name=\'category_id\']').trigger('change');
--></script>
@endsection
