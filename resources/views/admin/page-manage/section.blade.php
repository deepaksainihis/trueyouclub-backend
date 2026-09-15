@extends('layouts.admin')
@section('title','Page Sections')

@section('styles')
@stop

@section('content')

    @livewire('admin.page-manage.section',['slug'=>request()->slug])

@stop

@section('scripts')
@stop