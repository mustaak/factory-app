<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = Filament::getCurrentPanel()?->getId() === 'admin';

        return $table
            ->columns([
                ImageColumn::make('primaryImage.image_path')
                ->label('Image')
                ->disk('public')
                ->square()
                ->size(60)
                ->defaultImageUrl(url('/images/placeholder.png')),
                
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('category.name')
                ->label('Category')
                ->searchable()
                ->sortable()
                ->placeholder('No category'),

                TextColumn::make('vendor.business_name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->visible($isAdmin),

                TextColumn::make('brand')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Variants')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('approval_status')
                    ->label('Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->placeholder('Not reviewed')
                    ->visible($isAdmin),

                TextColumn::make('reviewed_at')
                    ->label('Reviewed At')
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable()
                    ->visible($isAdmin)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                SelectFilter::make('category')
                ->label('Category')
                ->relationship(
                    'category',
                    'name',
                    modifyQueryUsing: fn ($query) => $query
                        ->where('is_active', true)
                        ->orderBy('name'),
                )
                ->searchable()
                ->preload(),

                SelectFilter::make('approval_status')
                    ->label('Approval Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()
                        ->visible($isAdmin),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
