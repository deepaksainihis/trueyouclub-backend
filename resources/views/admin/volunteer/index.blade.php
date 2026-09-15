@extends('layouts.admin')
@section('title','Volunteers')

@section('styles')
@stop

@section('content')

@livewire('admin.volunteer.index')


@stop

@section('scripts')
    window.addEventListener('close-modal',event =>{
        $(#InviteModal).modal('hide');
    });

@stop