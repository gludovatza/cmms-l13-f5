<?php

namespace App\Filament\Pages;

use App\Enums\TimeEntryStatus;
use App\Models\TimeEntry;
use App\Models\Worksheet;
use App\Services\TimeEntryApprovalService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class WeeklyTimesheet extends Page
{
    protected string $view = 'filament.pages.weekly-timesheet';

    public Carbon $weekStart;
    public array $hours = [];
    public array $statuses = [];
    public array $rejectionReasons = [];
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
        $this->statuses = [];
        $this->rejectionReasons = [];

        foreach ($entries as $entry) {
            $date = $entry->work_date->format('Y-m-d');

            $this->hours[$entry->worksheet_id][$date] = $entry->hours;

            $this->statuses[$entry->worksheet_id][$date] = $entry->status;

            $this->rejectionReasons[$entry->worksheet_id][$date]
                = $entry->rejection_reason;
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
                    if (! in_array(
                        $entry->status,
                        [
                            TimeEntryStatus::Draft,
                            TimeEntryStatus::Rejected,
                        ],
                        true,
                    )) {
                        continue;
                    }

                    Gate::authorize('update', $entry);

                    $entry->update([
                        'hours' => $hours,
                        'status' => TimeEntryStatus::Draft,
                    ]);

                    continue;
                }

                if ($entry !== null && $hours <= 0) {
                    if (! in_array(
                        $entry->status,
                        [
                            TimeEntryStatus::Draft,
                            TimeEntryStatus::Rejected,
                        ],
                        true,
                    )) {
                        continue;
                    }

                    Gate::authorize('delete', $entry);

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

            Action::make('submitWeek')
                ->label(__('actions.submit'))
                ->color('primary')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->requiresConfirmation()
                ->visible( fn (): bool =>
                    auth()->user()->can('submit time entries') && $this->hasSubmittableEntries()
                )
                // ->action(fn (TimeEntryApprovalService $service) => $this->submitWeek($service)),
                ->action(function (TimeEntryApprovalService $service)
                {
                    $user = auth()->user();
                    $entries = TimeEntry::query()
                        ->where('user_id', $user->id)
                        ->whereBetween('work_date', [
                            $this->weekStart->copy()->startOfDay(),
                            $this->weekStart->copy()->endOfWeek()->endOfDay()
                        ])
                        ->whereIn('status', [
                            TimeEntryStatus::Draft,
                            TimeEntryStatus::Rejected
                        ])
                        ->get();

                    foreach ($entries as $entry) {
                        $service->submit( $entry, $user );
                    }

                    $this->loadWeek();
                }),
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

    public function isEditable(int $worksheetId, Carbon $day): bool
    {
        $date = $day->format('Y-m-d');

        $status = $this->statuses[$worksheetId][$date] ?? null;

        return $status === null
            || in_array(
                $status,
                [
                    TimeEntryStatus::Draft,
                    TimeEntryStatus::Rejected,
                ],
                true,
            );
    }

    // public function submitWeek(TimeEntryApprovalService $service): void
    // {
    //     $user = auth()->user();
    //     $entries = TimeEntry::query()
    //         ->where('user_id', $user->id)
    //         ->whereBetween('work_date', [
    //             $this->weekStart->copy()->startOfDay(),
    //             $this->weekStart->copy()->endOfWeek()->endOfDay()
    //         ])
    //         ->whereIn('status', [
    //             TimeEntryStatus::Draft,
    //             TimeEntryStatus::Rejected
    //         ])
    //         ->get();

    //     foreach ($entries as $entry) {
    //         $service->submit( $entry, auth()->user() );
    //     }

    //     $this->loadWeek();
    // }

    public function hasSubmittableEntries(): bool
    {
        return TimeEntry::query()
            ->where('user_id', auth()->id())
            ->whereBetween('work_date', [
                $this->weekStart->copy()->startOfDay(),
                $this->weekStart->copy()->endOfWeek()->endOfDay()
            ])
            ->whereIn('status', [
                TimeEntryStatus::Draft,
                TimeEntryStatus::Rejected
            ])
            ->exists();
    }
}
