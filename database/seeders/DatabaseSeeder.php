<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Book::factory(33)->create()->each(function ($book) {

            // per ogni libro crea da 5 a 30 recensioni
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
                // sovrascrive i dati chiamando il metodo custom  
                ->good()
                // crea un'associazione con il libro settando la colonna "book_id"
                ->for($book)
                ->create();
        });

        Book::factory(33)->create()->each(function ($book) {

            // per ogni libro crea da 5 a 30 recensioni
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
                // sovrascrive i dati chiamando il metodo custom  
                ->average()
                // crea un'associazione con il libro settando la colonna "book_id"
                ->for($book)
                ->create();
        });

        Book::factory(34)->create()->each(function ($book) {

            // per ogni libro crea da 5 a 30 recensioni
            $numReviews = random_int(5, 30);

            Review::factory()->count($numReviews)
                // sovrascrive i dati chiamando il metodo custom 
                ->bad()
                // crea un'associazione con il libro settando la colonna "book_id"
                ->for($book)
                ->create();
        });
    }
}
