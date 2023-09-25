<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'review',
        'rating'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    protected static function booted()
    {
        // ogni volta che una review viene modificata/cancellata viene cancellato ogni dato nella cache 
        // relativo al libro che ha ricevuto la review

        // il metodo cache() è semplicemente un'alternativa alla facade Cache
        static::updated(fn (Review $review) => cache()->forget('book:' . $review->book_id));
        static::deleted(fn (Review $review) => cache()->forget('book:' . $review->book_id));
        static::created(fn (Review $review) => cache()->forget('book:' . $review->book_id));

        // non funziona se fai l'update tramite query SQL raw 
        // o con query che non caricano il modello Review
        // es. non funzionamento:
        // \App\Models\Review::where('id', 1)->update(['rating' => 5])
    }
}
