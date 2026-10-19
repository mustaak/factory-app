<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\ProductImage;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdmin = Filament::getCurrentPanel()?->getId() === 'admin';

        return $schema->components([
            Section::make('Product Information')
                ->schema([
                    Select::make('vendor_id')
                        ->label('Vendor')
                        ->relationship('vendor', 'business_name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->visible($isAdmin),
                        
                    Select::make('category_id')
                    ->label('Category')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query
                            ->where('is_active', true)
                            ->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $set('slug', Str::slug((string) $state));
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('brand')
                        ->maxLength(255),

                    Textarea::make('short_description')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->rows(6)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Product Variants')
                ->description('Har size/color combination ke liye alag SKU aur price add karein.')
                ->schema([
                    Repeater::make('variants')
                        ->relationship()
                        ->schema([
                            TextInput::make('sku')
                                ->label('SKU')
                                ->required()
                                ->maxLength(100)
                                ->distinct(),

                            TextInput::make('size')
                                ->maxLength(50)
                                ->placeholder('S, M, L, XL'),

                            TextInput::make('color')
                                ->maxLength(50)
                                ->placeholder('Black, Blue'),

                            TextInput::make('price')
                                ->numeric()
                                ->minValue(0)
                                ->required()
                                ->prefix('$'),

                            TextInput::make('compare_at_price')
                                ->numeric()
                                ->minValue(0)
                                ->prefix('$'),

                            TextInput::make('stock_quantity')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),

                            FileUpload::make('image_path')
                            ->label('Variant Image')
                            ->image()
                            ->disk('public')
                            ->directory('products/variants')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imagePreviewHeight('80'),

                            Toggle::make('is_active')
                                ->default(true),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->itemLabel(fn (array $state): string =>
                            filled($state['sku'] ?? null)
                                ? $state['sku']
                                : trim(
                                    ($state['size'] ?? 'New variant') .
                                    ' ' .
                                    ($state['color'] ?? '')
                                )
                        )
                        ->collapsible()
                        ->collapsed()
                        ->reorderable()
                        ->addActionLabel('Add variant')
                        ->columnSpanFull(),
                ]),

            Section::make('Product Images')
                ->description('Upload multiple images and select one main image.')
                ->schema([
                    Repeater::make('images')
                        ->relationship()
                        ->schema([
                            FileUpload::make('image_path')
                            ->label('Product Image')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->required(),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->integer()
                                ->default(0)
                                ->required(),

                            Toggle::make('is_primary')
                                ->label('Main image')
                                ->default(false),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->collapsible()
                        ->collapsed()
                        ->reorderable()
                        ->addActionLabel('Add image')
                        ->columnSpanFull(),
                ]),

            Section::make('Publishing & Review')
                ->visible($isAdmin)
                ->schema([
                    Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                        ])
                        ->default('inactive')
                        ->required(),

                    Select::make('approval_status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending')
                        ->live()
                        ->required(),

                    Textarea::make('rejection_reason')
                        ->visible(fn ($get): bool =>
                            $get('approval_status') === 'rejected'
                        )
                        ->required(fn ($get): bool =>
                            $get('approval_status') === 'rejected'
                        )
                        ->columnSpanFull(),

                    Placeholder::make('reviewer_info')
                        ->label('Reviewed By')
                        ->content(fn ($record) =>
                            $record?->reviewer?->name ?? 'Will be set when reviewed'
                        ),

                    Placeholder::make('reviewed_at_info')
                        ->label('Reviewed At')
                        ->content(fn ($record) =>
                            $record?->reviewed_at?->format('d M Y, h:i A')
                                ?? 'Will be set when reviewed'
                        ),
                ])
                ->columns(2),
        ]);
    }
}
