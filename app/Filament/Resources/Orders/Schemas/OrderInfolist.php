<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order summary')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('order_number')->label('Order #')->weight('bold')->copyable(),
                        TextEntry::make('created_at')->label('Placed on')->dateTime('d M Y, h:i A'),
                        TextEntry::make('status')->badge()->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        }),
                        TextEntry::make('payment_method')->label('Payment')->formatStateUsing(fn () => 'Cash on delivery'),
                        TextEntry::make('payment_status')->badge()->color(fn (string $state): string => match ($state) {
                            'paid' => 'success',
                            'failed' => 'danger',
                            default => 'warning',
                        }),
                        TextEntry::make('total')->money('INR')->weight('bold'),
                    ]),

                Section::make('Customer')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->copyable(),
                        TextEntry::make('phone')->copyable(),
                        TextEntry::make('user.name')->label('Account')->placeholder('Guest'),
                    ]),

                Section::make('Delivery address')
                    ->schema([
                        TextEntry::make('address_line1')->label('Address'),
                        TextEntry::make('address_line2')->label('Address line 2')->placeholder('—'),
                        TextEntry::make('city'),
                        TextEntry::make('state'),
                        TextEntry::make('postal_code')->label('PIN code'),
                        TextEntry::make('country'),
                        TextEntry::make('notes')->placeholder('No notes')->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('product_name')->label('Product')->weight('bold'),
                                TextEntry::make('size')->placeholder('—'),
                                TextEntry::make('color')->placeholder('—'),
                                TextEntry::make('sku')->placeholder('—'),
                                TextEntry::make('price')->money('INR'),
                                TextEntry::make('quantity'),
                                TextEntry::make('line_total')->money('INR')->weight('bold'),
                            ])
                            ->columns(7),
                    ]),
            ]);
    }
}