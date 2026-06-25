<?php

namespace Tests\Feature;

use App\Filament\Resources\ScreeningQuestions\Pages\ListScreeningQuestions;
use App\Filament\Resources\ScreeningQuestions\ScreeningQuestionResource;
use App\Models\ScreeningQuestion;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminScreeningQuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_question_list_prioritizes_editable_question_text(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $question = ScreeningQuestion::create([
            'number' => 1,
            'scale' => 'stress',
            'text' => 'Saya merasa sulit untuk beristirahat.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(ListScreeningQuestions::class)
            ->assertOk()
            ->assertTableColumnVisible('number')
            ->assertTableColumnVisible('text')
            ->assertTableColumnVisible('scale')
            ->assertTableColumnVisible('is_active')
            ->assertTableColumnStateSet('text', $question->text, $question);

        $questionColumn = $component->instance()->getTable()->getColumn('text');
        $questionAction = $questionColumn->getAction();

        $this->assertSame(FontWeight::Bold, $questionColumn->getWeight());
        $this->assertTrue($questionColumn->isSearchable());
        $this->assertInstanceOf(EditAction::class, $questionAction);
        $this->assertSame('editQuestion', $questionAction->getName());
        $this->assertSame(['index'], array_keys(ScreeningQuestionResource::getPages()));

        $recordActions = $component->instance()->getTable()->getRecordActions();

        $this->assertCount(1, $recordActions);
        $this->assertInstanceOf(EditAction::class, $recordActions[0]);
    }

    public function test_admin_can_render_the_question_list_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $question = ScreeningQuestion::create([
            'number' => 3,
            'scale' => 'depression',
            'text' => 'Saya tidak dapat merasakan perasaan positif sama sekali.',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/screening-questions')
            ->assertOk()
            ->assertSee('Pertanyaan DASS-21')
            ->assertSee($question->text)
            ->assertSee('editQuestion');
    }

    public function test_admin_creates_and_edits_questions_from_list_modals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $component = Livewire::actingAs($admin)
            ->test(ListScreeningQuestions::class);

        $this->assertNull($component->instance()->getAction('create')->getUrl());

        $component->callAction('create', data: [
            'number' => 22,
            'scale' => 'stress',
            'text' => 'Saya merasa sulit untuk menenangkan diri.',
            'sort_order' => 22,
            'is_active' => true,
        ]);

        $question = ScreeningQuestion::query()->where('number', 22)->firstOrFail();

        $component
            ->assertTableActionDoesNotHaveUrl('edit', $question)
            ->callTableAction('edit', $question, data: [
                'number' => 22,
                'scale' => 'anxiety',
                'text' => 'Saya merasa sulit untuk tetap tenang.',
                'sort_order' => 21,
                'is_active' => false,
            ]);

        $this->assertDatabaseHas('screening_questions', [
            'id' => $question->id,
            'number' => 22,
            'scale' => 'anxiety',
            'text' => 'Saya merasa sulit untuk tetap tenang.',
            'sort_order' => 21,
            'is_active' => false,
        ]);
    }
}
