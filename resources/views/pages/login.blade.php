@extends('layoutfront')
@section('title', 'Connexion')
@section('content')
                <div id="content" class="col-md-9 col-sm-8 col-xs-12">
      <div class="row">
        <div class="col-md-6 col-xs-12">
          <div class="well">
            <h2 class="heading">New Customer</h2>
            <p><strong>Register</strong></p>
            <p>By creating an account you will be able to shop faster, be up to date on an order's status, and keep track of the orders you have previously made.</p>
            <a href="index5502.html?route=account/register" class="btn btn-primary">Continue</a></div>
        </div>
        <div class="col-md-6 col-xs-12">
          <div class="well">
            <h2 class="heading">Returning Customer</h2>
            <p><strong>I am a returning customer</strong></p>
            <form action="https://inspiretemplates.com/Grocery/index.php?route=account/login" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label class="control-label" for="input-email">E-Mail Address</label>
                <input type="text" name="email" value="" placeholder="E-Mail Address" id="input-email" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-password">Password</label>
                <input type="password" name="password" value="" placeholder="Password" id="input-password" class="form-control" /><br>
                <a href="indexacda.html?route=account/forgotten">Forgotten Password</a></div>
              <input type="submit" value="Login" class="btn btn-primary" />
                          </form>
          </div>
        </div>
      </div>
      </div>
    <aside id="column-right" class="col-md-3 col-sm-4 col-xs-12 hidden-xs">
    <div class="list-group">
    <a href="indexe223.html?route=account/login" class="list-group-item">Login</a> <a href="index5502.html?route=account/register" class="list-group-item">Register</a> <a href="indexacda.html?route=account/forgotten" class="list-group-item">Forgotten Password</a>
    <a href="indexe223.html?route=account/account" class="list-group-item">My Account</a>
    <a href="indexc6b3.html?route=account/address" class="list-group-item">Address Book</a> <a href="index4628.html?route=account/wishlist" class="list-group-item">Wish List</a> <a href="indexee92.html?route=account/order" class="list-group-item">Order History</a> <a href="index1042.html?route=account/download" class="list-group-item">Downloads</a><a href="index6a11.html?route=account/recurring" class="list-group-item">Recurring payments</a> <a href="index5a23.html?route=account/reward" class="list-group-item">Reward Points</a> <a href="index32dd.html?route=account/return" class="list-group-item">Returns</a> <a href="indexb2aa.html?route=account/transaction" class="list-group-item">Transactions</a> <a href="indexb4e9.html?route=account/newsletter" class="list-group-item">Newsletter</a>
  </div>

  </aside>
</div>
</div>
@endsection
