@extends('layoutback')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 rounded-lg mt-5 mb-5">
            <div class="card-header bg-primary text-white text-center py-4">
                <h3 class="font-weight-light my-2 text-white">Authentification</h3>
            </div>
            <div class="card-body p-4">
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form onsubmit="valider('login','POST');" method="POST" action="{{ url('/login') }}">
                    @csrf
<!--                     
                    <div class="form-group mb-3">
                        <label class="mb-1" for="inputEmailAddress">Adresse Email</label>
                        <input class="form-control" id="inputEmailAddress" type="email" name="email" value="{{ old('email') }}" placeholder="Entrez votre adresse email" required autofocus />
                    </div>
                    
                     -->
                    <div class="form-group mb-3">
                        <label class="mb-1" for="inputUsername">Nom d'utilisateur</label>
                        <input class="form-control" id="inputUsername" type="text" name="username" value="{{ old('username') }}" placeholder="Entrez votre nom d'utilisateur" required />
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="mb-1" for="inputPassword">Mot de passe</label>
                        <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Entrez votre mot de passe" required />
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                        <button type="submit" onclick="valider('login','POST');" class="btn btn-success btn-lg">Se connecter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
<script>

</script>
