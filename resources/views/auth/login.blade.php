@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-2">
            <div class="panel panel-default" style="background-color:  #E6FFFF">
                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="estado" id="estado" value="true">
                    <div class="panel-heading">
                        <h4 class="text-right">Identifícate</h4>
                    </div>
                    @if($errors->has('password') || $errors->has('email'))
                        <div class="row">
                            <h6 id="merror" role="alert" class="alert alert-danger col-md-offset-2 col-md-8 text-center">
                            Lo sentimos, vuelve a intentar. Quizás el Administrados te INHABILITÓ. Contáctate con él.
                            </h6>
                        </div>
                    @endif
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">Usuario</label>
                            <div class="col-md-8">
                                <input type="text" name="email" id="email" class="form-control" placeholder="Ingresa Tu Usuario" value="{{ old('email') }}" onfocus>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="password" class="col-md-4 control-label">Contraseña</label>
                            <div class="col-md-8">
                                <input type="password" name="password" id="password" class="form-control" aria-describedby="mostrarc" placeholder="Ingresa Tu Clave">
                                <small id="mostrarc" class="form-text text-muted">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <br>
                                            <input onclick="mostrarc()" type="checkbox" class="mc form-check-input">
                                            Mostrar Caracteres
                                        </label>
                                    </div>
                                </small>
                            </div>
                        </div>  
                        <hr>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="col-md-12">  
                                    <button type="submit" class="btn btn-primary btn-sm center-block" style="cursor: pointer;">
                                    <i class="icon-user"></i>&nbsp;&nbsp; INGRESAR &nbsp;&nbsp;<i class="icon-user"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="col-md-12">
                <h3><img class="img img-thumbnail img-responsive center-block" src="{{ asset('img/iden.png') }}" alt="logo"></h3>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <h4 class="col-md-4 col-sm-4 text-center"><b>FICSA</b></h4>
        <h4 class="col-md-4 col-sm-4 text-center"><b>Telf. 074 25-25-25</b></h4>
        <h4 class="col-md-4 col-sm-4 text-center"><b>Lambayeque</b></h4>
    </div>
</div>

<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-2.1.3.min.js"></script>
<script>
    function mostrarc() {
        if($('#password').attr('type') == 'text'){
            $('#password').removeAttr('type', 'text');
            $('#password').attr('type', 'password');
        } else {
            $('#password').removeAttr('type', 'password');
            $('#password').attr('type', 'text');
        }               
    }

</script>
@endsection
