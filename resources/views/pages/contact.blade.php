@extends('layoutfront')
@section('title', 'Contact')
@section('content')
                <div id="content" class="col-sm-12">
      <h1 class="heading">Contact Us</h1>
      <h3 class="class="heading"">Our Location</h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
                        <div class="col-sm-3"><strong>Grocery Shopping Hub</strong><br />
              <address>
              Address 1
              </address>
                          </div>
            <div class="col-sm-3"><strong>Telephone</strong><br>
              123456789<br />
              <br />
                          </div>
            <div class="col-sm-3">
                                        </div>
          </div>
        </div>
      </div>
            <form action="https://inspiretemplates.com/Grocery/index.php?route=information/contact" method="post" enctype="multipart/form-data" class="form-horizontal">
        <fieldset>
          <legend>Contact Us</legend>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name">Your Name</label>
            <div class="col-sm-10">
              <input type="text" name="name" value="" id="input-name" class="form-control" />
                          </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-email">E-Mail Address</label>
            <div class="col-sm-10">
              <input type="text" name="email" value="" id="input-email" class="form-control" />
                          </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-enquiry">Enquiry</label>
            <div class="col-sm-10">
              <textarea name="enquiry" rows="10" id="input-enquiry" class="form-control"></textarea>
                          </div>
          </div>
          
        </fieldset>
        <div class="buttons">
          <div class="pull-right">
            <input class="btn btn-primary" type="submit" value="Submit" />
          </div>
        </div>
      </form>
      </div>
    </div>
</div>
@endsection
