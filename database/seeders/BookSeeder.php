<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

            $queries = [
                'subject:"Science Fiction"', 
                'subject:"Romance"', 
                'subject:"Drama"'
            ];
            foreach ($queries as $query) {
            $response = Http::Withoutverifying()->get("https://www.googleapis.com/books/v1/volumes?q={$query}&maxResults=5&langRestrict=pt");

            if ($response->successful() && isset($response['items'])) {
                
                foreach ($response->json('items') as $item) {
                    $info = $item['volumeInfo'];

                    $autorName = !empty($info['authors']) ? $info['authors'][0] : 'Autor Desconhecido';

                    Book::create([
                        'user_id' => $users->random()->id, 

                        'title' => $info['title'] ?? 'Título Desconhecido',
                        'author' => $autorName,
                        'description' => $info['description'] ?? 'Sem descrição disponível.',
                        
                    ]);
                }
            }
        }
    }
}