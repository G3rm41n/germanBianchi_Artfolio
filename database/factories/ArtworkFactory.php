<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artwork>
 */
class ArtworkFactory extends Factory
{
    protected $model = Artwork::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price_hint' => 'Orientativo: $50 USD',
            'external_url' => 'https://www.youtube.com/watch?v=' . fake()->lexify('???????????'),
            'provider' => 'youtube',
            'category' => 'video',
            'render_type' => 'iframe',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119',
            'embed_html' => '<iframe src="https://www.youtube.com/embed/test"></iframe>',
            'status' => 'published',
            'is_featured' => false,
        ];
    }
}
