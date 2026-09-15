@extends('auth.admin.layout')
@section('title',trans('global.reset_password'))

@section('content')

    @livewire('auth.admin.reset-password',['token'=>$token,'email'=>$email])

@stop
