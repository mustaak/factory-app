<?php

namespace App\Filament\Resources\Vendors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = Filament::getCurrentPanel()?->getId() === 'admin';

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Account Email')
                    ->searchable(),

                TextColumn::make('business_name')
                    ->label('Business Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Business Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('country_code')
                    ->label('Phone Code')
                    ->toggleable(),

                TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('state')
                    ->label('State / Province')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('country')
                    ->label('Country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('postal_code')
                    ->label('Postal Code')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('address_line_1')
                    ->label('Address')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('address_line_2')
                    ->label('Address Line 2')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tax_id')
                    ->label('Tax ID')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('verification_status')
                    ->label('Verification')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),

                SelectColumn::make('status')
                    ->label('Vendor Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ])
                    ->selectablePlaceholder(false)
                    ->disabled(! $isAdmin)
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->visible($isAdmin),
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make()
                    ->visible($isAdmin),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible($isAdmin),

                    ForceDeleteBulkAction::make()
                        ->visible($isAdmin),

                    RestoreBulkAction::make()
                        ->visible($isAdmin),
                ]),
            ]);
    }
}
