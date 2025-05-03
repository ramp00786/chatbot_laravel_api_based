<?php

namespace Database\Factories;

use App\Models\ChatbotQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotQuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'parent_id' => null,
            'question' => $this->faker->sentence() . '?',
            'answer' => $this->faker->boolean(70) ? $this->faker->paragraph() : null,
            'is_final' => false,
            'enable_input' => $this->faker->boolean(30),
        ];
    }

    public function withDepth(int $maxDepth = 20): static
    {
        return $this->afterCreating(function (ChatbotQuestion $question) use ($maxDepth) {
            $this->createQuestionTree($question, $maxDepth - 1);
        });
    }

    protected function createQuestionTree(ChatbotQuestion $parent, int $remainingDepth): void
    {
        if ($remainingDepth <= 0) {
            // Make the deepest question final
            $parent->update(['is_final' => true]);
            return;
        }

        $hasChildren = $this->faker->boolean(90); // 90% chance to have children

        if ($hasChildren) {
            $childrenCount = $this->faker->numberBetween(1, 5);
            
            for ($i = 0; $i < $childrenCount; $i++) {
                $isFinal = $remainingDepth === 1 || $this->faker->boolean(20);
                $enableInput = $this->faker->boolean(30);
                
                $child = ChatbotQuestion::factory()
                    ->create([
                        'parent_id' => $parent->id,
                        'is_final' => $isFinal,
                        'enable_input' => $enableInput,
                        'answer' => $isFinal || $enableInput ? $this->faker->paragraph() : null,
                    ]);

                if (!$isFinal) {
                    $this->createQuestionTree($child, $remainingDepth - 1);
                }
            }
        } else {
            // No children, make this question final
            $parent->update(['is_final' => true]);
        }
    }

    public function withInput(): static
    {
        return $this->state(fn (array $attributes) => [
            'enable_input' => true,
            'answer' => $this->faker->paragraph(),
        ]);
    }

    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_final' => true,
            'answer' => $this->faker->paragraph(),
        ]);
    }

    public function withAnswer(): static
    {
        return $this->state(fn (array $attributes) => [
            'answer' => $this->faker->paragraph(),
        ]);
    }
}