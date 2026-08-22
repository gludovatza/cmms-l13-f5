<x-filament-panels::page>
    <div class="flex items-center justify-between">
        <x-filament::icon-button icon="heroicon-o-chevron-left" label="{{ __('page_labels.previous_week') }}" wire:click="previousWeek" color="gray" />
        <strong>
            {{ $weekStart->format('Y-m-d') }}
            -
            {{ $weekStart->copy()->endOfWeek()->format('Y-m-d') }}
        </strong>
        <x-filament::icon-button icon="heroicon-o-chevron-right" label="{{ __('page_labels.next_week') }}" wire:click="nextWeek" color="gray" />
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr>
                    <th class="p-2 text-left">
                        {{ __('module_names.worksheets.plural_label') }}
                    </th>

                    @foreach ($this->getWeekDays() as $day)
                        <th class="p-2 text-center">
                            <div>
                                {{ $day->translatedFormat('D') }}
                            </div>

                            <div class="text-sm font-normal">
                                {{ $day->format('Y-m-d') }}
                            </div>
                        </th>
                    @endforeach

                    <th class="p-2 text-center">
                        {{ __('page_labels.total') }}
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach ($worksheets as $worksheet)
                    <tr>
                        <td class="p-2">
                            {{ $worksheet->description }}
                        </td>

                        @foreach ($this->getWeekDays() as $day)
                            <td class="p-2">
                                <input type="number"
                                    min="0"
                                    max="8"
                                    step="0.25"
                                    wire:model.blur="hours.{{ $worksheet->id }}.{{ $day->format('Y-m-d') }}"
                                    wire:key="time-entry-{{ $worksheet->id }}-{{ $day->format('Y-m-d') }}"
                                    @disabled(! $this->isEditable($worksheet->id, $day))
                                    class="w-20 rounded-lg border-gray-300">
                                    @if ( isset( $statuses[$worksheet->id][ $day->format('Y-m-d') ] ) )
                                        <div class="mt-1 text-xs">
                                            {{ $statuses[$worksheet->id][ $day->format('Y-m-d') ]->getLabel() }}
                                        </div>
                                        @if ($statuses[$worksheet->id][ $day->format('Y-m-d') ] === \App\Enums\TimeEntryStatus::Rejected)
                                            <div class="mt-1 flex items-center gap-1 text-xs">
                                                <x-filament::icon
                                                    icon="heroicon-o-information-circle"
                                                    class="h-4 w-4 cursor-help"
                                                    x-tooltip.raw="{{
                                                        $rejectionReasons[$worksheet->id][$day->format('Y-m-d')]
                                                    }}"
                                                />
                                            </div>
                                        @endif
                                    @endif

                            </td>
                        @endforeach

                        <td class="p-2 text-center font-semibold">
                            {{ $this->getWorksheetTotal($worksheet->id) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="p-2 text-left">
                        {{ __('page_labels.daily_total') }}
                    </th>

                    @foreach ($this->getWeekDays() as $day)
                        <th class="p-2 text-center">
                            {{ number_format($this->getDayTotal($day), 2) }}
                        </th>
                    @endforeach

                    <th class="p-2 text-center">
                        {{ number_format(collect($this->hours)->flatten()->sum(fn($value) => (float) $value), 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

</x-filament-panels::page>
