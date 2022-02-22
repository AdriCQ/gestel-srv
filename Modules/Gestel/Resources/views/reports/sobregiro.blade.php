@extends('gestel::reports.base')

@section('body')
<h2>Reporte de Sobregiro de {{ $mes }}/{{ $year }}</h2>

<h4 style="margin-top: 4rem">Resumen</h4>
<table class="table" style="width: 30rem">
  <thead>
    <tr>
      <td>Teléfonos Sobregirados</td>
      <td>{{ $counterTels }}</td>

    </tr>
  </thead>

  <tbody>
    <tr>
      <td>Sobregiro Total</td>
      <td>${{ number_format($totalSobregiro,2) }}</td>
    </tr>
  </tbody>
</table>

<h4 style="margin-top: 4rem">Detalles</h4>
<table class="table">
  <thead>
    <tr>
      <td><b>Telf</b></td>
      <td><b>Presupuesto</b></td>
      <td><b>Sobregiro</b></td>
      <td><b>Cargo</b></td>
      <td><b>Unidad</b></td>
      <td><b>Órgano</b></td>
    </tr>
  </thead>

  <tbody>
    @foreach ($tels as $tel)
      <tr>
        <td>{{ $tel['telf'] }}</td>
        <td>${{ number_format($tel['presupuesto'],2) }}</td>
        <td>${{ number_format($tel['dif'],2) }}</td>
        <td>{{ $tel['cargo']['nombre'] }}</td>
        <td>{{ $tel['cargo']['departamento']['nombre'] }}</td>
        <td>{{ $tel['cargo']['departamento']['entidad']['nombre'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

@endsection()