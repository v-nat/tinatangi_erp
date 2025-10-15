@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryRecipes') active @endsection
@section('headings') Recipes @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recipe Management</li>
        </ol>
    </nav>

    <div class="container" id="recipe-editor" data-product-id="{{ $product->id }}">
        <div class="card">
            <div class="card-header">
                <h4>Recipe Management</h4>
            </div>
            <div class="card-body">
                {{-- The product name will be inserted here by jQuery --}}
                <h5 class="card-title">Recipe for: <strong id="product-name">Loading...</strong></h5>
                <hr>

                <h6>Current Ingredients</h6>
                {{-- The current ingredients list will be inserted here by jQuery --}}
                <ul class="list-group mb-4" id="current-ingredients-list">
                    <p id="no-recipe-message">Loading recipe...</p>
                </ul>

                <hr>

                <h6>Update Ingredients</h6>
                <form action="{{ route('recipes.update', $product) }}" method="POST">
                    @csrf
                    {{-- The dynamic ingredient form rows will be inserted here by jQuery --}}
                    <div id="ingredient-list"></div>

                    <button type="button" id="add-ingredient-btn" class="btn btn-secondary mt-2">Add Ingredient</button>
                    <hr>
                    <button type="submit" class="btn btn-success">Save Recipe</button>
                    <a href="#" class="btn btn-light">Back to Products</a>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/recipeManagement.js') }}"></script>
@endsection
