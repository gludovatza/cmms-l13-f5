<x-filament-panels::page>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <x-filament::icon-button icon="heroicon-o-chevron-left" label="{{ __('page_labels.previous_week') }}" wire:click="previousWeek" color="gray" />
            <strong>
                {{ $weekStart->format('Y-m-d') }}
                -
                {{ $weekStart->copy()->endOfWeek()->format('Y-m-d') }}
            </strong>
            <x-filament::icon-button icon="heroicon-o-chevron-right" label="{{ __('page_labels.next_week') }}" wire:click="nextWeek" color="gray" />
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('page_labels.total') }}</p>
            <p class="text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format(collect($this->hours)->flatten()->sum(fn($value) => (float) $value), 2) }} h</p>
        </div>
    </div>
    @if (empty($worksheets))
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('page_labels.nothing_to_display') }}</p>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="p-2 text-left">
                                {{ __('module_names.worksheets.plural_label') }}
                            </th>

                            @foreach ($this->getWeekDays() as $day)
                                <th class="px-2 py-2 text-center font-medium {{ $day->isToday() ? 'text-primary-600 dark:text-primary-400' : '' }}">
                                    {{ $day->translatedFormat('D') }}
                                    <br />
                                    <span class="text-xs">
                                        {{ $day->format('Y-m-d') }}
                                    </span>
                                </th>
                            @endforeach

                            <th class="px-3 py-2 text-right font-medium">
                                {{ __('page_labels.total') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @foreach ($worksheets as $worksheet)
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-950 dark:text-white">
                                    {{ $worksheet->description }}
                                </td>

                                @foreach ($this->getWeekDays() as $day)
                                    @php
                                        $status = $statuses[$worksheet->id][$day->format('Y-m-d')] ?? null;
                                        $date = $day->format('Y-m-d');
                                    @endphp
                                    <td class="px-2 py-2 text-center">
                                        <input type="number"
                                            min="0"
                                            max="8"
                                            step="0.25"
                                            wire:model.blur="hours.{{ $worksheet->id }}.{{ $date }}"
                                            wire:key="time-entry-{{ $worksheet->id }}-{{ $date }}"
                                            @disabled(! $this->isEditable($worksheet->id, $day))
                                            @class([
                                                    'w-16 rounded-lg border-gray-300 text-center text-sm dark:border-white/10 dark:bg-white/5 dark:text-white',
                                                    'border-danger-400 ring-1 ring-danger-400' => isset( $status ) && $status === \App\Enums\TimeEntryStatus::Rejected,
                                                ])>
                                            @if ( isset( $status ) )

                                                @if ($status === \App\Enums\TimeEntryStatus::Rejected)
                                                    <div class="mt-1 text-xs text-danger-600 dark:text-danger-400" title="{{ $rejectionReasons[$worksheet->id][$date] }}">
                                                        {{ $status->getLabel() }}
                                                    </div>
                                                    <div class="mt-1 flex items-center gap-1 text-xs">
                                                        <x-filament::icon
                                                            icon="heroicon-o-information-circle"
                                                            class="h-4 w-4 cursor-help"
                                                            x-tooltip.raw="{{
                                                                $rejectionReasons[$worksheet->id][$date]
                                                            }}"
                                                        />
                                                    </div>
                                                @else
                                                    <span @class([
                                                        'inline-flex items-center gap-1 font-medium',
                                                        'text-warning-600 dark:text-warning-400' => $status === App\Enums\TimeEntryStatus::Submitted,
                                                        'text-success-600 dark:text-success-400' => $status === App\Enums\TimeEntryStatus::Approved,
                                                    ])>
                                                        @if ($status === App\Enums\TimeEntryStatus::Approved)
                                                            <x-filament::icon icon="heroicon-m-check-circle" class="h-4 w-4" />
                                                        @endif
                                                    </span>
                                                    <p class="mt-1 text-xs text-gray-400">{{ $status->getLabel() }}</p>
                                                @endif
                                            @endif

                                    </td>
                                @endforeach

                                <td class="px-3 py-2 text-right font-semibold text-gray-950 dark:text-white">
                                    {{ $this->getWorksheetTotal($worksheet->id) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200 text-gray-500 dark:border-white/10 dark:text-gray-400">
                            <th class="px-3 py-2 font-medium">
                                {{ __('page_labels.daily_total') }}
                            </th>

                            @foreach ($this->getWeekDays() as $day)
                                <th class="px-2 py-2 text-center font-semibold text-gray-950 dark:text-white">
                                    {{ number_format($this->getDayTotal($day), 2) }}
                                </th>
                            @endforeach

                            <th class="px-3 py-2 text-right font-semibold text-gray-950 dark:text-white">
                                {{ number_format(collect($this->hours)->flatten()->sum(fn($value) => (float) $value), 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                {{ __('page_labels.instructions') }}
            </p>
        </x-filament::section>
    @endif

</x-filament-panels::page>
