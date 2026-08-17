<?php

namespace App\Filament\Pages;

use App\Enums\TimeEntryStatus;
use App\Models\TimeEntry;
use App\Models\Worksheet;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class WeeklyTimesheet extends Page
{
    protected string $view = 'filament.pages.weekly-timesheet';

    public Carbon $weekStart;
    public array $hours = [];
    public Collection $worksheets;

    public static function canAccess(): bool
    {
        return auth()->user()?->can(
            'manage own time entries'
        ) ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    public function getTitle(): string | Htmlable
    {
        return __('module_names.pages.weekly-timesheet');
    }

    public static function getNavigationGroup(): string
    {
        return __('module_names.navigation_groups.maintenance');
    }

    public static function getNavigationLabel(): string
    {
        return __('module_names.pages.weekly-timesheet');
    }

    public function mount(): void
    {
        $this->weekStart = now()->startOfWeek();
        $this->loadWeek();
    }

    public function loadWeek(): void
    {
        $this->loadWorksheets();
        $this->loadHours();
    }

    // segédmetódusok
    public function getWeekDays(): array
    {
        return collect(range(0, 6))
            ->map(
                fn (int $day): Carbon =>
                    $this->weekStart->copy()->addDays($day)
            )
            ->all();
    }

    protected function loadWorksheets(): void
    {
        $user = auth()->user();

        $this->worksheets = Worksheet::query()
            ->when(
                ! $user->hasRole('admin'),
                fn ($query) =>
                    $query->where('repairer_id', $user->id)
            )
            ->orderBy('created_at')
            ->get();
    }

    protected function loadHours(): void
    {
        $user = auth()->user();

        $entries = TimeEntry::query()
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [
                $this->weekStart->copy()->startOfDay(),
                $this->weekStart->copy()->endOfWeek()->endOfDay(),
            ])
            ->get();

        $this->hours = [];

        foreach ($entries as $entry) {
            $this->hours[
                $entry->worksheet_id
            ][
                $entry->work_date->format('Y-m-d')
            ] = $entry->hours;
        }
    }

    public function getWorksheetTotal(int $worksheetId): float
    {
        return collect($this->hours[$worksheetId] ?? [])
            ->filter(fn ($value) => is_numeric($value))
            ->sum(fn ($value) => (float) $value);
    }

    public function getDayTotal(Carbon $day): float
    {
        $date = $day->format('Y-m-d');

        return collect($this->hours)
            ->sum(
                fn (array $days): float =>
                    (float) ($days[$date] ?? 0)
            );
    }

    public function saveDraft(): void
    {
        $user = auth()->user();

        foreach ($this->hours as $worksheetId => $days) {
            foreach ($days as $date => $hours) {

                $hours = (float) ($hours ?: 0);

                $entry = TimeEntry::query()
                    ->where('user_id', $user->id)
                    ->where('worksheet_id', $worksheetId)
                    ->whereDate('work_date', $date)
                    ->first();

                if ($entry === null && $hours > 0) {
                    TimeEntry::create([
                        'user_id' => $user->id,
                        'worksheet_id' => $worksheetId,
                        'work_date' => $date,
                        'hours' => $hours,
                        'status' => TimeEntryStatus::Draft,
                    ]);

                    continue;
                }

                if ($entry !== null && $hours > 0) {
                    $entry->update([
                        'hours' => $hours,
                    ]);

                    continue;
                }

                if ($entry !== null && $hours <= 0) {
                    $entry->delete();
                }
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveDraft')
                ->label(__('actions.save'))
                ->color('gray')
                ->icon(Heroicon::OutlinedCheck)
                ->visible(fn (): bool => auth()->user()->hasRole('repairer'))
                ->action(fn () => $this->saveDraft()),
        ];
    }

    public function previousWeek(): void
    {
        $this->weekStart->subWeek();

        $this->loadWeek();
    }

    public function nextWeek(): void
    {
        $this->weekStart->addWeek();

        $this->loadWeek();
    }

    public function currentWeek(): void
    {
        $this->weekStart = now()->startOfWeek();

        $this->loadWeek();
    }
}
