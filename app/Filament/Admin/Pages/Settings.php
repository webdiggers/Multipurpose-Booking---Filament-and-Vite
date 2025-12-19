<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.admin.pages.settings';
    
    protected static ?string $navigationGroup = 'Settings';
    
    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        // Get social media links and ensure it's always an array
        $socialMediaLinks = Setting::get('social_media_links', []);
        if (!is_array($socialMediaLinks)) {
            $socialMediaLinks = [];
        }

        // Default Menu Items
        $aboutPage = \App\Models\Page::where('slug', 'about')->first();
        $defaultMenu = [
            [
                'label' => 'Home', 
                'url' => route('home', [], false), 
                'type' => 'custom', 
                'is_visible' => true
            ],
            [
                'label' => 'About us', 
                'url' => route('about', [], false), 
                'type' => $aboutPage ? 'page' : 'custom', 
                'page_id' => $aboutPage?->id, 
                'is_visible' => true
            ],
            [
                'label' => 'Contact us', 
                'url' => route('contact', [], false), 
                'type' => 'custom', 
                'is_visible' => true
            ],
            [
                'label' => 'My Bookings', 
                'url' => route('my-bookings', [], false), 
                'type' => 'custom', 
                'is_visible' => true
            ],
        ];

        $this->form->fill([
            'company_name' => Setting::get('company_name', 'Company Name') ?? 'Company Name',
            'company_email' => Setting::get('company_email', 'info@company.com') ?? 'info@company.com',
            'company_phone' => Setting::get('company_phone', '+91 XXX XXX XXXX') ?? '+91 XXX XXX XXXX',
            'company_logo' => Setting::get('company_logo'),
            'page_header_image' => Setting::get('page_header_image'),
            'social_media_links' => $socialMediaLinks,
            'header_menu' => Setting::get('header_menu', $defaultMenu),
            'footer_menu' => Setting::get('footer_menu', $defaultMenu),
            'maintenance_mode' => (bool) Setting::get('maintenance_mode', false),
            'maintenance_message' => Setting::get('maintenance_message', 'We are currently performing maintenance. Please check back soon.') ?? 'We are currently performing maintenance. Please check back soon.',
            'enable_paypal' => (bool) Setting::get('enable_paypal', false),
            'enable_phonepe' => (bool) Setting::get('enable_phonepe', false),
            'registration_method' => Setting::get('registration_method', 'email'),
            'registration_fields_shown' => Setting::get('registration_fields_shown', []),
            'registration_fields_required' => Setting::get('registration_fields_required', []),
            'light_primary_color' => Setting::get('light_primary_color', '#e9ab00'),
            'dark_primary_color' => Setting::get('dark_primary_color', '#e9ab00'),
            'about_us_content' => Setting::get('about_us_content', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\Section::make('Company Information')
                                    ->schema([
                                        Forms\Components\FileUpload::make('company_logo')
                                            ->label('Company Logo')
                                            ->image()
                                            ->directory('settings')
                                            ->visibility('public'),
                                        Forms\Components\TextInput::make('company_name')
                                            ->label('Company Name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('company_email')
                                            ->label('Company Email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('company_phone')
                                            ->label('Company Phone Number')
                                            ->tel()
                                            ->required()
                                            ->maxLength(255),
                                    ])->columns(2),

                                Forms\Components\Section::make('Social Media Links')
                                    ->schema([
                                        Forms\Components\Repeater::make('social_media_links')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Select::make('platform')
                                                    ->label('Platform')
                                                    ->options([
                                                        'facebook' => 'Facebook',
                                                        'instagram' => 'Instagram',
                                                        'linkedin' => 'LinkedIn',
                                                        'youtube' => 'YouTube',
                                                        'tiktok' => 'TikTok',
                                                        'whatsapp' => 'WhatsApp',
                                                    ])
                                                    ->required()
                                                    ->searchable(),
                                                Forms\Components\TextInput::make('url')
                                                    ->label('URL')
                                                    ->url()
                                                    ->placeholder('https://...')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('Add Social Media Link')
                                            ->collapsible()
                                            ->defaultItems(0),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Menus')
                            ->schema([
                                Forms\Components\Section::make('Header Menu')
                                    ->schema([
                                        Forms\Components\Repeater::make('header_menu')
                                            ->label('Menu Items')
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->options([
                                                        'custom' => 'Custom Link',
                                                        'page' => 'Page',
                                                    ])
                                                    ->default('custom')
                                                    ->reactive()
                                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('page_id', null)),
                                                
                                                Forms\Components\Select::make('page_id')
                                                    ->label('Page')
                                                    ->options(\App\Models\Page::where('is_active', true)->pluck('title', 'id'))
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'page')
                                                    ->reactive()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                        if ($state) {
                                                            $page = \App\Models\Page::find($state);
                                                            if ($page) {
                                                                $set('label', $page->title);
                                                                // If system page, use specific route, else use generic page route
                                                                if ($page->slug === 'about') {
                                                                    $set('url', route('about', [], false));
                                                                } elseif ($page->slug === 'contact') {
                                                                    $set('url', route('contact', [], false));
                                                                } else {
                                                                    $set('url', route('pages.show', ['slug' => $page->slug], false));
                                                                }
                                                            }
                                                        }
                                                    }),

                                                Forms\Components\TextInput::make('label')
                                                    ->required()
                                                    ->label('Label'),
                                                Forms\Components\TextInput::make('url')
                                                    ->required()
                                                    ->label('URL')
                                                    ->placeholder('/about or https://example.com')
                                                    ->readOnly(fn (Forms\Get $get) => $get('type') === 'page'),
                                                Forms\Components\Toggle::make('is_visible')
                                                    ->label('Visible')
                                                    ->default(true),
                                            ])
                                            ->columns(3)
                                            ->collapsible()
                                            ->reorderableWithButtons()
                                            ->defaultItems(0),
                                    ]),
                                Forms\Components\Section::make('Footer Menu')
                                    ->schema([
                                        Forms\Components\Repeater::make('footer_menu')
                                            ->label('Menu Items')
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->options([
                                                        'custom' => 'Custom Link',
                                                        'page' => 'Page',
                                                    ])
                                                    ->default('custom')
                                                    ->reactive()
                                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('page_id', null)),
                                                
                                                Forms\Components\Select::make('page_id')
                                                    ->label('Page')
                                                    ->options(\App\Models\Page::where('is_active', true)->pluck('title', 'id'))
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'page')
                                                    ->reactive()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                        if ($state) {
                                                            $page = \App\Models\Page::find($state);
                                                            if ($page) {
                                                                $set('label', $page->title);
                                                                // If system page, use specific route, else use generic page route
                                                                if ($page->slug === 'about') {
                                                                    $set('url', route('about', [], false));
                                                                } elseif ($page->slug === 'contact') {
                                                                    $set('url', route('contact', [], false));
                                                                } else {
                                                                    $set('url', route('pages.show', ['slug' => $page->slug], false));
                                                                }
                                                            }
                                                        }
                                                    }),

                                                Forms\Components\TextInput::make('label')
                                                    ->required()
                                                    ->label('Label'),
                                                Forms\Components\TextInput::make('url')
                                                    ->required()
                                                    ->label('URL')
                                                    ->placeholder('/terms or https://example.com')
                                                    ->readOnly(fn (Forms\Get $get) => $get('type') === 'page'),
                                                Forms\Components\Toggle::make('is_visible')
                                                    ->label('Visible')
                                                    ->default(true),
                                            ])
                                            ->columns(3)
                                            ->collapsible()
                                            ->reorderableWithButtons()
                                            ->defaultItems(0),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Appearance')
                            ->schema([
                                Forms\Components\Section::make('Color Scheme')
                                    ->description('Customize the primary color for Light and Dark modes.')
                                    ->schema([
                                        Forms\Components\ColorPicker::make('light_primary_color')
                                            ->label('Light Mode Primary Color')
                                            ->default('#e9ab00'),
                                        Forms\Components\ColorPicker::make('dark_primary_color')
                                            ->label('Dark Mode Primary Color')
                                            ->default('#e9ab00'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('System')
                            ->schema([
                                Forms\Components\Section::make('Maintenance Mode')
                                    ->schema([
                                        Forms\Components\Toggle::make('maintenance_mode')
                                            ->label('Enable Maintenance Mode')
                                            ->helperText('When enabled, non-admin users will see the maintenance page')
                                            ->reactive(),
                                        Forms\Components\Textarea::make('maintenance_message')
                                            ->label('Maintenance Message')
                                            ->rows(3)
                                            ->visible(fn ($get) => $get('maintenance_mode')),
                                    ]),

                                Forms\Components\Section::make('Payment Methods')
                                    ->schema([
                                        Forms\Components\Toggle::make('enable_paypal')
                                            ->label('Enable PayPal')
                                            ->helperText('Show PayPal as a payment option'),
                                        Forms\Components\Toggle::make('enable_phonepe')
                                            ->label('Enable PhonePe')
                                            ->helperText('Show PhonePe as a payment option'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Other')
                            ->schema([
                                Forms\Components\Section::make('Registration Settings')
                                    ->schema([
                                        Forms\Components\Select::make('registration_method')
                                            ->label('Registration Method')
                                            ->options([
                                                'full' => 'Full Details',
                                                'email' => 'Email Only',
                                                'phone' => 'Phone Number Only',
                                            ])
                                            ->required()
                                            ->reactive(),

                                        Forms\Components\CheckboxList::make('registration_fields_shown')
                                            ->label('Fields to Show')
                                            ->options([
                                                'first_name' => 'First Name',
                                                'last_name' => 'Last Name',
                                                'email' => 'Email',
                                                'phone' => 'Phone Number',
                                                'address' => 'Address',
                                                'city' => 'City',
                                                'state' => 'State',
                                                'country' => 'Country',
                                                'password' => 'Password',
                                            ])
                                            ->visible(fn (Forms\Get $get) => $get('registration_method') === 'full')
                                            ->columns(3)
                                            ->bulkToggleable(),

                                        Forms\Components\CheckboxList::make('registration_fields_required')
                                            ->label('Required Fields')
                                            ->options([
                                                'first_name' => 'First Name',
                                                'last_name' => 'Last Name',
                                                'email' => 'Email',
                                                'phone' => 'Phone Number',
                                                'address' => 'Address',
                                                'city' => 'City',
                                                'state' => 'State',
                                                'country' => 'Country',
                                                'password' => 'Password',
                                            ])
                                            ->visible(fn (Forms\Get $get) => $get('registration_method') === 'full')
                                            ->columns(3)
                                            ->bulkToggleable(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save each setting with defaults for optional fields
        Setting::set('company_name', $data['company_name'] ?? 'Company Name', 'text');
        Setting::set('company_email', $data['company_email'] ?? 'info@company.com', 'text');
        Setting::set('company_phone', $data['company_phone'] ?? '+91 XXX XXX XXXX', 'text');
        Setting::set('company_logo', $data['company_logo'] ?? null, 'text');
        Setting::set('page_header_image', $data['page_header_image'] ?? null, 'text');
        Setting::set('social_media_links', $data['social_media_links'] ?? [], 'json');
        Setting::set('header_menu', $data['header_menu'] ?? [], 'json');
        Setting::set('footer_menu', $data['footer_menu'] ?? [], 'json');
        Setting::set('maintenance_mode', $data['maintenance_mode'] ?? false, 'boolean');
        
        // Only save maintenance message if maintenance mode is enabled
        if (isset($data['maintenance_mode']) && $data['maintenance_mode']) {
            Setting::set('maintenance_message', $data['maintenance_message'] ?? 'We are currently performing maintenance. Please check back soon.', 'text');
        }
        
        Setting::set('enable_paypal', $data['enable_paypal'] ?? false, 'boolean');
        Setting::set('enable_phonepe', $data['enable_phonepe'] ?? false, 'boolean');

        Setting::set('registration_method', $data['registration_method'] ?? 'email', 'text');
        Setting::set('registration_fields_shown', $data['registration_fields_shown'] ?? [], 'json');
        Setting::set('registration_fields_required', $data['registration_fields_required'] ?? [], 'json');

        Setting::set('light_primary_color', $data['light_primary_color'] ?? '#e9ab00', 'text');
        Setting::set('dark_primary_color', $data['dark_primary_color'] ?? '#e9ab00', 'text');
        Setting::set('about_us_content', $data['about_us_content'] ?? '', 'text');

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
