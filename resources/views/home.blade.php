@extends('casa')

@section('contentbody')
  <h5 class="col-md-12">
    <div class="col-md-5 text-left">
        @if($alerta == 1) 
          <b style="color:green"><span class="icon-check"></span> Ya cerraste caja hoy. Espera hasta mañana para poder registrar Recibos.</b>
        @else 
          <b style="color:red"><span class="icon-check"></span> Recuerda cerrar caja Hoy. </b>
        @endif
    </div>
    <div class="col-md-7 text-right">
      <div class="col-md-12">
        <div class="col-md-7">
          @if($secuencia == 0) 
            <b style="color:blue"><span class="icon-check"></span> Iniciar Nº Recibos en: <font class="_sequence">NO TIENES</font></b>
          @else 
            <b style="color:blue"><span class="icon-check"></span> Iniciar Nº Recibos en: <font class="_sequence">{{ $secuencia }}</font></b>
          @endif
        </div>
        <form>
          <div class="col-md-4">
            <input class="form-control input-sm" type="text" id="txt_sequence" placeholder="Nº">
          </div>
          <div class="col-md-1">
            <a href="#" class="btn btn-sm btn-primary" id="btn_sequence">OK</a>
          </div>
        </form>
      </div>
    </div>
  </h5>
  <div class="col-md-12">
    <img src="{{ asset('img/iden.png') }}" class="img img-responsive center-block" alt="">
    <hr>
  </div>
  <div class="row">
    <h4 class="col-md-4 col-sm-4 text-center"><b>FICSA</b></h4>
    <h4 class="col-md-4 col-sm-4 text-center"><b>Telf. 074 25-25-25</b></h4>
    <h4 class="col-md-4 col-sm-4 text-center"><b>Lambayeque</b></h4>
  </div>
@endsection