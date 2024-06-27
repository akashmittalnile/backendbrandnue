@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Publish Recipes</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.recipe.list') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="searchbar">
            <form class="searchform" method="get" action="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            
                            <select class="form-control" name="c">
                                <option value="">Select Category</option>
                                @forelse($categories as $category)
                                    <option value="{{$category->id}}" @if($category->id==request()->c) selected @endif>{{$category->name}}</option>
                                @empty
                                @endforelse
                            </select>
                            
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" name="s" class="form-control" placeholder="Search By Recipe Name, Keyword" value="{{request()->s}}"/>
                            <button type="submit" class="searchsubmit"><i class="las la-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="di-product-card">
                    <div class="di-product-header">
                        <h2>Publish Recipes</h2>
                    </div>
                    <div class="di-product-body">
                        <div class="row">
                            @forelse($recipes as $recipe)
                            <div class="col-md-4">
                                <div class="di-cat-item">
                                    <div class="di-cat-content-body">
                                        <div class="di-cat-media">
                                            @if(isset($recipe->recipeImage->url) && !empty($recipe->recipeImage->url))
                                                @php
                                                    $parsed = parse_url($recipe->recipeImage->url);
                                                    if(isset($parsed['scheme']) && in_array($parsed['scheme'],['https','http'])){
                                                        echo '<img src="'.$recipe->recipeImage->url.'" />';
                                                    }else{
                                                        echo '<img src="'.asset($recipe->recipeImage->url).'" />';
                                                    }
                                                @endphp
                                            @else
                                                <img src="{{ asset('admin/images/logo.svg') }}" />
                                            @endif
                                        </div>
                                        <div class="di-cat-content">
                                            <h2>{{$recipe->meal_title}}</h2>
                                        </div>
                                    </div>
                                    <div class="di-cat-content-footer">
                                        <a href="{{ route('admin.recipe.edit',$recipe) }}" class="edit-btn-outline">Edit</a>
                                        <a href="{{ route('admin.recipe.detail',$recipe) }}" class="view-btn-outline">View Detail</a>
                                    </div>
                                </div>
                            </div>
                            @empty
                                <div class="col-md-12">
                                    <div class="text-center text-success">No Data Available</div>
                                </div>
                            @endforelse
                            <div class="col-md-12">
                                {{$recipes->appends(Request::except('page'))->links()}}
                            </div>                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/recipe.css') }}">
@endpush