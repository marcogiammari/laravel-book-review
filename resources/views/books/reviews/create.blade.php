@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2xl">Add Review for {{ $book->title }}</h1>

    <form action="{{ route('books.reviews.store', $book) }}" method="post">
        @csrf

        <label for="review">Review</label>
        <textarea class="input" name="review" id="review" required></textarea>

        <div class="mb-4 text-red-500 text-sm">
            @error('review')
                {{ $message }}
            @enderror
        </div>

        <label for="rating">Rating</label>
        <select class="input" name="rating" id="rating" required>
            <option value="">Select a Rating</option>
            @for ($i = 1; $i <= 5; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>

        <div class="mb-4 text-red-500 text-sm">
            @error('rating')
                {{ $message }}
            @enderror
        </div>

        <button type="submit" class="btn">Add Review</button>
    </form>
@endsection
