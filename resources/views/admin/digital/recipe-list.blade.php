@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Recipes</h4>
            </div>
            <div class="btn-option-info">
                <a class="btn-ye" href="{{ route('admin.recipe.create') }}">Add New Recipe</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="di-overview-info">
            <div class="row">
                <div class="col-md-3">
                    <div class="di-card style-one">
                        <div class="di-card-inner">
                            <div class="di-card-content">
                                <h2>{{$draft_recipe}}</h2>
                                <p>Draft Recipe</p>
                                <a href="{{ route('admin.recipe.list,status','DRAFT') }}" class="more-btn">more</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="di-card style-one">
                        <div class="di-card-inner">
                            <div class="di-card-content">
                                <h2>{{$publish_recipe}}</h2>
                                <p>Published Recipe</p>
                                <a href="{{ route('admin.recipe.list,status','PUBLISH') }}" class="more-btn">more</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="di-card style-one">
                        <div class="di-card-inner">
                            <div class="di-card-content">
                                <h2>{{$premium_recipe}}</h2>
                                <p>Premium Recipe</p>
                                <a href="{{ route('admin.recipe.list,status','PREMIUM') }}" class="more-btn">more</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="di-card style-one">
                        <div class="di-card-inner">
                            <div class="di-card-content">
                                <h2>{{$free_recipe}}</h2>
                                <p>Free Access Recipe</p>
                                <a href="{{ route('admin.recipe.list,status','FREE') }}" class="more-btn">more</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('common.msg')
        <div class="recipe-cat-section">
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

            <div class="">
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
                                <a href="{{route('admin.recipe.detail',$recipe)}}" class="view-btn-outline">View Detail</a>
                                <a href="{{route('admin.recipe.delete',$recipe->id)}}" class="edit-btn-outline">Delete</a>
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
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/recipe.css') }}">
@endpush