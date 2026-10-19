<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdmin = Filament::getCurrentPanel()?->getId() === 'admin';

        return $schema->components([

            Select::make('user_id')
                ->label('Seller Account')
                ->relationship(
                    name: 'user',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn ($query) => $query->whereHas(
                        'roles',
                        fn ($roleQuery) => $roleQuery->where('name', 'seller')
                    )
                )
                ->searchable()
                ->preload()
                ->required()
                ->unique(ignoreRecord: true)
                ->disabled(! $isAdmin)
                ->dehydrated($isAdmin)
                ->helperText(
                    $isAdmin
                        ? 'Only users with the seller role are available.'
                        : 'Your linked seller account cannot be changed.'
                ),

            // Business Information
            Section::make('Business Information')
                ->description('Enter the vendor business details.')
                ->icon('heroicon-o-building-storefront')
                ->schema([
                    TextInput::make('business_name')
                        ->label('Business Name')
                        ->required()
                        ->maxLength(150),

                ])
                ->columns(1),

            // Contact Information
            Section::make('Contact Information')
                ->description('Business contact details.')
                ->icon('heroicon-o-phone')
                ->schema([
                    TextInput::make('email')
                        ->label('Business Email')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->maxLength(30)
                        ->placeholder('+91 9876543210')
                        ->nullable(),

                    TextInput::make('country_code')
                        ->label('Phone Country Code')
                        ->maxLength(10)
                        ->placeholder('+91, +1, +44')
                        ->nullable(),
                ])
                ->columns(2),

            // Business Address
            Section::make('Business Address')
                ->description('Registered or primary business address.')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    TextInput::make('address_line_1')
                        ->label('Address Line 1')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('address_line_2')
                        ->label('Address Line 2')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('city')
                        ->label('City')
                        ->maxLength(100)
                        ->nullable(),

                    TextInput::make('state')
                        ->label('State / Province')
                        ->maxLength(100)
                        ->nullable(),

                    TextInput::make('postal_code')
                        ->label('Postal / ZIP Code')
                        ->maxLength(20)
                        ->nullable(),

                    TextInput::make('country')
                        ->label('Country')
                        ->maxLength(100)
                        ->nullable(),
                ])
                ->columns(2),

            // Verification and Status — Admin only
            Section::make('Verification & Status')
                ->description('Control vendor approval and account availability.')
                ->icon('heroicon-o-shield-check')
                ->visible($isAdmin)
                ->schema([
                    Select::make('verification_status')
                        ->label('Verification Status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending')
                        ->required()
                        ->native(false),

                    Select::make('status')
                        ->label('Vendor Status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'suspended' => 'Suspended',
                        ])
                        ->default('inactive')
                        ->required()
                        ->native(false),

                    DateTimePicker::make('approved_at')
                        ->label('Approval Date')
                        ->seconds(false)
                        ->nullable()
                        ->visible(
                            fn (callable $get): bool =>
                                $get('verification_status') === 'approved'
                        ),
                ])
                ->columns(2),
        ]);
    }
}
