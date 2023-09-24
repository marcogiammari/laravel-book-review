<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index(Request $request)
    {
        // cancella la cache -> utile per test
        // Cache::flush();

        $title = $request->input('title');
        $filter = $request->input('filter', '');

        // Query the books table and filter by title if provided (when title -> arrow function)
        $books = Book::when(
            $title,
            fn ($query, $title) => $query->title($title)
        );

        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6_months' => $books->highestRatedLast6Months(),
            'popular_last_6_months' => $books->popularLast6Months(),
            default => $books->withAvg('reviews', 'rating')->withCount('reviews')->latest()
        };

        // $books = $books->get();

        $cacheKey = 'books:' . $filter . ':' . $title;
        // Verifica che ci sia una chiave 'books' nella cache
        // altrimenti chiama la funzione closure che restituisce $books (e logga un messaggio)
        $books = Cache::remember($cacheKey, 3600, function () use ($books, $cacheKey) {
            // dump('Not cached');
            // dump($cacheKey);
            return $books->get();
        });

        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        // qui cachiamo soltanto le review -> per cachare tutto il libro dovremmo evitare la dependency injection e usare solo l'id
        $cacheKey = 'book:' . $book->id;

        $book = Cache::remember($cacheKey, 3600, fn () => $book->load([
            // esempio di eager loading con metodo load() 
            // al contrario di $book->reviews in show.blade che è un esempio di lazy loading 
            'reviews' => fn ($query) => $query->latest()
        ]));

        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
