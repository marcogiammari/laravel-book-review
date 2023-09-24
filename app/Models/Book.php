<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // local query scope: ci permette di semplificare le nostre query, per es:
    // \App\Models\Book::title('officiis')->get();
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', "%{$title}%");
    }

    /**
     * Calculates the popularity of the query results based on the number of reviews.
     *
     * @param Builder $query The query builder instance.
     * @param mixed $from The start date for filtering the reviews (optional).
     * @param mixed $to The end date for filtering the reviews (optional).
     * @return Builder The query builder instance.
     */
    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withCount([
            // esempio di arrow function in php: può avere una sola espressione e non serve usare use perché ha accesso alle variabili esterne
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating')
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder
    {
        // usiamo having e non where perché stiamo lavorando sui risultati di una query aggregata e non una query semplice
        return $query->having('reviews_count', '>=', $minReviews);
    }

    private function dateRangeFilter(Builder $query, $from, $to)
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } else {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder
    {
        return $query->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }
}