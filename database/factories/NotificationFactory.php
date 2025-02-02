<?php
namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()->id, // The user who receives the notification
            'type' => $this->faker->randomElement(['like', 'comment']), // Type of notification
            'notifiable_id' => \App\Models\Post::inRandomOrder()->first()->id, // The related entity (e.g., post)
            'notifiable_type' => \App\Models\Post::class, // The class of the related entity
            'read' => $this->faker->boolean(20), // 20% chance of being marked as read
        ];
    }
}