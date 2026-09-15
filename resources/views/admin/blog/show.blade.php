@extends('layouts.admin')
@section('title','Blogs')

@section('styles')
@stop

@section('content')

    @livewire('admin.blog.show',['blog' => $blog])

@stop

@section('scripts')
@stop