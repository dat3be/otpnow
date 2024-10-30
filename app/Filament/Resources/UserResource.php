<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $pluralLabel = 'Users';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Tên người dùng')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'), // Chỉ cho phép Admin chỉnh sửa

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'), // Chỉ cho phép Admin chỉnh sửa

                TextInput::make('password')
                    ->label('Mật khẩu')
                    ->password()
                    ->required(fn ($record) => $record === null) // Chỉ bắt buộc khi tạo mới
                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->visible(fn ($record) => auth()->user()->role === 'admin' || $record->role === 'user'), // Hiển thị cho cả Admin và User

                Select::make('role')
                    ->label('Vai trò')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                    ])
                    ->default('user')
                    ->required()
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'), // Chỉ Admin mới có thể thay đổi vai trò

                TextInput::make('telegram_userid')
                    ->label('Telegram User ID')
                    ->nullable()
                    ->unique()
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'),

                TextInput::make('telegram_username')
                    ->label('Telegram Username')
                    ->nullable()
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'),

                TextInput::make('balance')
                    ->label('Số dư tài khoản')
                    ->numeric()
                    ->default(0)
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'),

                TextInput::make('aff_code')
                    ->label('Mã affiliate')
                    ->disabled() // Không cho phép chỉnh sửa
                    ->default(fn () => strtoupper(substr(bin2hex(random_bytes(3)), 0, 6))) // Sinh tự động khi tạo mới
                    ->unique(ignoreRecord: true), // Bỏ qua kiểm tra unique với bản ghi hiện tại

                TextInput::make('phone_num')
                    ->label('Số điện thoại')
                    ->nullable()
                    ->unique(ignoreRecord: true) // Kiểm tra unique chỉ khi tạo mới hoặc thay đổi
                    ->disabled(fn ($record) => $record !== null), // Không cho phép chỉnh sửa sau khi tạo mới

                TextInput::make('aff_balance')
                    ->label('Số dư affiliate')
                    ->numeric()
                    ->default(0)
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'),

                TextInput::make('ref_by')
                    ->label('ID của người giới thiệu')
                    ->nullable()
                    ->disabled(fn ($record) => auth()->user()->role !== 'admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên người dùng')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Vai trò')
                    ->sortable(),

                TextColumn::make('telegram_userid')
                    ->label('Telegram User ID'),

                TextColumn::make('telegram_username')
                    ->label('Telegram Username'),

                TextColumn::make('balance')
                    ->label('Số dư tài khoản')
                    ->formatStateUsing(fn (string $state): string => number_format($state, 2) . ' VND'),

                TextColumn::make('aff_code')
                    ->label('Mã affiliate'),

                TextColumn::make('aff_balance')
                    ->label('Số dư affiliate')
                    ->formatStateUsing(fn (string $state): string => number_format($state, 2) . ' VND'),

                TextColumn::make('ref_by')
                    ->label('ID của người giới thiệu'),

                TextColumn::make('phone_num')
                    ->label('Số điện thoại'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'), // Chỉ Admin mới thấy nút Edit
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'), // Chỉ Admin mới thấy nút Delete
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'), // Chỉ Admin mới có thể xóa hàng loạt
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
