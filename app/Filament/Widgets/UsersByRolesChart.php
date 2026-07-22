<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersByRolesChart extends ChartWidget
{
    public function getHeading(): string
    {
        return __('module_names.widgets.usersbyroles');
    }

    protected function getData(): array
    {
        $roles = Role::withCount('users')->get();

        $labels = $roles->pluck('name')->toArray();

        $data = $roles->pluck('users_count')->toArray();

        $colors = $roles
            ->map(fn ($role) => $this->colorFromString($role->name))
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('module_names.widgets.usersbyroles'),
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => __('module_names.users.plural_label') . ' (' .
                        User::all()->count() . ')',
                ]
            ]
        ];
    }

    private function colorFromString(string $value): string
    {
        $hash = md5($value);

        $r = hexdec(substr($hash, 0, 2));
        $g = hexdec(substr($hash, 2, 2));
        $b = hexdec(substr($hash, 4, 2));

        return "rgb($r, $g, $b)";
    }

    public static function canView(): bool
    {
        // return auth()->user()->can('read roles') && auth()->user()->can('read users');
        return auth()->user()?->can('view roles statistics') ?? false;
    }
}
