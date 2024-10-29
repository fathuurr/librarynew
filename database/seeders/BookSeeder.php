<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'stock' => 5],
            ['title' => '1984', 'author' => 'George Orwell', 'stock' => 10],
            ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'stock' => 7],
            ['title' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'stock' => 3],
            ['title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'stock' => 8],
            ['title' => 'Moby-Dick', 'author' => 'Herman Melville', 'stock' => 6],
            ['title' => 'War and Peace', 'author' => 'Leo Tolstoy', 'stock' => 4],
            ['title' => 'The Odyssey', 'author' => 'Homer', 'stock' => 12],
            ['title' => 'Ulysses', 'author' => 'James Joyce', 'stock' => 9],
            ['title' => 'The Divine Comedy', 'author' => 'Dante Alighieri', 'stock' => 6],
            ['title' => 'Crime and Punishment', 'author' => 'Fyodor Dostoevsky', 'stock' => 5],
            ['title' => 'Jane Eyre', 'author' => 'Charlotte Bronte', 'stock' => 8],
            ['title' => 'The Hobbit', 'author' => 'J.R.R. Tolkien', 'stock' => 15],
            ['title' => 'The Brothers Karamazov', 'author' => 'Fyodor Dostoevsky', 'stock' => 4],
            ['title' => 'Don Quixote', 'author' => 'Miguel de Cervantes', 'stock' => 6],
            ['title' => 'Wuthering Heights', 'author' => 'Emily Bronte', 'stock' => 3],
            ['title' => 'Anna Karenina', 'author' => 'Leo Tolstoy', 'stock' => 7],
            ['title' => 'Brave New World', 'author' => 'Aldous Huxley', 'stock' => 8],
            ['title' => 'The Iliad', 'author' => 'Homer', 'stock' => 5],
            ['title' => 'One Hundred Years of Solitude', 'author' => 'Gabriel Garcia Marquez', 'stock' => 10]
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
