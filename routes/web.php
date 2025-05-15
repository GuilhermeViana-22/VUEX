<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppsController;
use App\Http\Controllers\UserInterfaceController;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\PageLayoutController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MiscellaneousController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ChartsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Todas as rotas web da aplicação estão registradas aqui.
| Rotas são carregadas pelo RouteServiceProvider dentro do grupo "web".
|
*/

// Rota raiz redireciona para login
Route::redirect('/', '/auth/login-cover');

Route::group(['prefix' => 'auth'], function () {
    // Rotas GET (Visualização)
    Route::get('login', [AuthenticationController::class, 'login_cover'])->name('login');
    Route::get('login-basic', [AuthenticationController::class, 'login_basic'])->name('login.basic');
    Route::get('login-cover', [AuthenticationController::class, 'login_cover'])->name('login.cover');

    Route::get('register-basic', [AuthenticationController::class, 'register_basic'])->name('register.basic');
    Route::get('register-cover', [AuthenticationController::class, 'register_cover'])->name('register.cover');

    Route::get('forgot-password-basic', [AuthenticationController::class, 'forgot_password_basic'])->name('password.request.basic');
    Route::get('forgot-password-cover', [AuthenticationController::class, 'forgot_password_cover'])->name('password.request.cover');

    Route::get('reset-password-basic', [AuthenticationController::class, 'reset_password_basic'])->name('password.reset.basic');
    Route::get('reset-password-cover', [AuthenticationController::class, 'reset_password_cover'])->name('password.reset.cover');

    Route::get('verify-email-basic', [AuthenticationController::class, 'verify_email_basic'])->name('verification.notice.basic');
    Route::get('verify-email-cover', [AuthenticationController::class, 'verify_email_cover'])->name('verification.notice.cover');

    Route::get('two-steps-basic', [AuthenticationController::class, 'two_steps_basic'])->name('two.steps.basic');
    Route::get('two-steps-cover', [AuthenticationController::class, 'two_steps_cover'])->name('two.steps.cover');

    Route::get('register-multisteps', [AuthenticationController::class, 'register_multi_steps'])->name('register.multisteps');

    // Rotas POST (Ação)
    Route::post('login', [AuthenticationController::class, 'login'])->name('login.post');
    Route::post('register', [AuthenticationController::class, 'register'])->name('register.post');

    Route::post('forgot-password', [AuthenticationController::class, 'forgot_password'])->name('password.email');
    Route::post('reset-password', [AuthenticationController::class, 'reset_password'])->name('password.update');

    Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
});
// ==================== ROTAS PROTEGIDAS ====================
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [DashboardController::class, 'dashboardEcommerce'])->name('.dashboard-ecommerce');

    Route::prefix('content/dashboard')->group(function() {
        Route::get('dashboard-analytic', [DashboardController::class, 'dashboardAnalytics'])->name('dashboard-analytics');
        Route::get('dashboard-ecommerce', [DashboardController::class, 'dashboardEcommerce'])->name('dashboard-ecommerce');
    });

    // Aplicativos
    Route::prefix('app')->group(function () {
        Route::get('email', [AppsController::class, 'emailApp'])->name('app-email');
        Route::get('chat', [AppsController::class, 'chatApp'])->name('app-chat');
        Route::get('todo', [AppsController::class, 'todoApp'])->name('app-todo');
        Route::get('calendar', [AppsController::class, 'calendarApp'])->name('app-calendar');
        Route::get('kanban', [AppsController::class, 'kanbanApp'])->name('app-kanban');

        // Faturas
        Route::prefix('invoice')->group(function () {
            Route::get('list', [AppsController::class, 'invoice_list'])->name('app-invoice-list');
            Route::get('preview', [AppsController::class, 'invoice_preview'])->name('app-invoice-preview');
            Route::get('edit', [AppsController::class, 'invoice_edit'])->name('app-invoice-edit');
            Route::get('add', [AppsController::class, 'invoice_add'])->name('app-invoice-add');
            Route::get('print', [AppsController::class, 'invoice_print'])->name('app-invoice-print');
        });

        // E-commerce
        Route::prefix('ecommerce')->group(function () {
            Route::get('shop', [AppsController::class, 'ecommerce_shop'])->name('app-ecommerce-shop');
            Route::get('details', [AppsController::class, 'ecommerce_details'])->name('app-ecommerce-details');
            Route::get('wishlist', [AppsController::class, 'ecommerce_wishlist'])->name('app-ecommerce-wishlist');
            Route::get('checkout', [AppsController::class, 'ecommerce_checkout'])->name('app-ecommerce-checkout');
        });

        // Outros apps
        Route::get('file-manager', [AppsController::class, 'file_manager'])->name('app-file-manager');

        // Controle de Acesso
        Route::get('access-roles', [AppsController::class, 'access_roles'])->name('app-access-roles');
        Route::get('access-permission', [AppsController::class, 'access_permission'])->name('app-access-permission');

        // Usuários
        Route::prefix('user')->group(function () {
            Route::get('list', [AppsController::class, 'user_list'])->name('app-user-list');
            Route::get('view/account', [AppsController::class, 'user_view_account'])->name('app-user-view-account');
            Route::get('view/security', [AppsController::class, 'user_view_security'])->name('app-user-view-security');
            Route::get('view/billing', [AppsController::class, 'user_view_billing'])->name('app-user-view-billing');
            Route::get('view/notifications', [AppsController::class, 'user_view_notifications'])->name('app-user-view-notifications');
            Route::get('view/connections', [AppsController::class, 'user_view_connections'])->name('app-user-view-connections');
        });
    });

    // UI Elements
    Route::prefix('ui')->group(function () {
        Route::get('typography', [UserInterfaceController::class, 'typography'])->name('ui-typography');
    });

    // Ícones
    Route::prefix('icons')->group(function () {
        Route::get('feather', [UserInterfaceController::class, 'icons_feather'])->name('icons-feather');
    });

    // Cards
    Route::prefix('card')->group(function () {
        Route::get('basic', [CardsController::class, 'card_basic'])->name('card-basic');
        Route::get('advance', [CardsController::class, 'card_advance'])->name('card-advance');
        Route::get('statistics', [CardsController::class, 'card_statistics'])->name('card-statistics');
        Route::get('analytics', [CardsController::class, 'card_analytics'])->name('card-analytics');
        Route::get('actions', [CardsController::class, 'card_actions'])->name('card-actions');
    });

    // Componentes
    Route::prefix('component')->group(function () {
        Route::get('accordion', [ComponentsController::class, 'accordion'])->name('component-accordion');
        Route::get('alert', [ComponentsController::class, 'alert'])->name('component-alert');
        Route::get('avatar', [ComponentsController::class, 'avatar'])->name('component-avatar');
        Route::get('badges', [ComponentsController::class, 'badges'])->name('component-badges');
        Route::get('breadcrumbs', [ComponentsController::class, 'breadcrumbs'])->name('component-breadcrumbs');
        Route::get('buttons', [ComponentsController::class, 'buttons'])->name('component-buttons');
        Route::get('carousel', [ComponentsController::class, 'carousel'])->name('component-carousel');
        Route::get('collapse', [ComponentsController::class, 'collapse'])->name('component-collapse');
        Route::get('divider', [ComponentsController::class, 'divider'])->name('component-divider');
        Route::get('dropdowns', [ComponentsController::class, 'dropdowns'])->name('component-dropdowns');
        Route::get('list-group', [ComponentsController::class, 'list_group'])->name('component-list-group');
        Route::get('modals', [ComponentsController::class, 'modals'])->name('component-modals');
        Route::get('pagination', [ComponentsController::class, 'pagination'])->name('component-pagination');
        Route::get('navs', [ComponentsController::class, 'navs'])->name('component-navs');
        Route::get('offcanvas', [ComponentsController::class, 'offcanvas'])->name('component-offcanvas');
        Route::get('tabs', [ComponentsController::class, 'tabs'])->name('component-tabs');
        Route::get('timeline', [ComponentsController::class, 'timeline'])->name('component-timeline');
        Route::get('pills', [ComponentsController::class, 'pills'])->name('component-pills');
        Route::get('tooltips', [ComponentsController::class, 'tooltips'])->name('component-tooltips');
        Route::get('popovers', [ComponentsController::class, 'popovers'])->name('component-popovers');
        Route::get('pill-badges', [ComponentsController::class, 'pill_badges'])->name('component-pill-badges');
        Route::get('progress', [ComponentsController::class, 'progress'])->name('component-progress');
        Route::get('spinner', [ComponentsController::class, 'spinner'])->name('component-spinner');
        Route::get('toast', [ComponentsController::class, 'toast'])->name('component-bs-toast');
    });

    // Extensões
    Route::prefix('ext-component')->group(function () {
        Route::get('sweet-alerts', [ExtensionController::class, 'sweet_alert'])->name('ext-component-sweet-alerts');
        Route::get('block-ui', [ExtensionController::class, 'block_ui'])->name('ext-component-block-ui');
        Route::get('toastr', [ExtensionController::class, 'toastr'])->name('ext-component-toastr');
        Route::get('sliders', [ExtensionController::class, 'sliders'])->name('ext-component-sliders');
        Route::get('drag-drop', [ExtensionController::class, 'drag_drop'])->name('ext-component-drag-drop');
        Route::get('tour', [ExtensionController::class, 'tour'])->name('ext-component-tour');
        Route::get('clipboard', [ExtensionController::class, 'clipboard'])->name('ext-component-clipboard');
        Route::get('plyr', [ExtensionController::class, 'plyr'])->name('ext-component-plyr');
        Route::get('context-menu', [ExtensionController::class, 'context_menu'])->name('ext-component-context-menu');
        Route::get('swiper', [ExtensionController::class, 'swiper'])->name('ext-component-swiper');
        Route::get('tree', [ExtensionController::class, 'tree'])->name('ext-component-tree');
        Route::get('ratings', [ExtensionController::class, 'ratings'])->name('ext-component-ratings');
        Route::get('locale', [ExtensionController::class, 'locale'])->name('ext-component-locale');
    });

    // Layouts de Página
    Route::prefix('page-layouts')->group(function () {
        Route::get('collapsed-menu', [PageLayoutController::class, 'layout_collapsed_menu'])->name('layout-collapsed-menu');
        Route::get('full', [PageLayoutController::class, 'layout_full'])->name('layout-full');
        Route::get('without-menu', [PageLayoutController::class, 'layout_without_menu'])->name('layout-without-menu');
        Route::get('empty', [PageLayoutController::class, 'layout_empty'])->name('layout-empty');
        Route::get('blank', [PageLayoutController::class, 'layout_blank'])->name('layout-blank');
    });

    // Formulários
    Route::prefix('form')->group(function () {
        Route::get('input', [FormsController::class, 'input'])->name('form-input');
        Route::get('input-groups', [FormsController::class, 'input_groups'])->name('form-input-groups');
        Route::get('input-mask', [FormsController::class, 'input_mask'])->name('form-input-mask');
        Route::get('textarea', [FormsController::class, 'textarea'])->name('form-textarea');
        Route::get('checkbox', [FormsController::class, 'checkbox'])->name('form-checkbox');
        Route::get('radio', [FormsController::class, 'radio'])->name('form-radio');
        Route::get('custom-options', [FormsController::class, 'custom_options'])->name('form-custom-options');
        Route::get('switch', [FormsController::class, 'switch'])->name('form-switch');
        Route::get('select', [FormsController::class, 'select'])->name('form-select');
        Route::get('number-input', [FormsController::class, 'number_input'])->name('form-number-input');
        Route::get('file-uploader', [FormsController::class, 'file_uploader'])->name('form-file-uploader');
        Route::get('quill-editor', [FormsController::class, 'quill_editor'])->name('form-quill-editor');
        Route::get('date-time-picker', [FormsController::class, 'date_time_picker'])->name('form-date-time-picker');
        Route::get('layout', [FormsController::class, 'layouts'])->name('form-layout');
        Route::get('wizard', [FormsController::class, 'wizard'])->name('form-wizard');
        Route::get('validation', [FormsController::class, 'validation'])->name('form-validation');
        Route::get('repeater', [FormsController::class, 'form_repeater'])->name('form-repeater');
    });

    // Tabelas
    Route::prefix('table')->group(function () {
        Route::get('', [TableController::class, 'table'])->name('table');
        Route::get('datatable/basic', [TableController::class, 'datatable_basic'])->name('datatable-basic');
        Route::get('datatable/advance', [TableController::class, 'datatable_advance'])->name('datatable-advance');
    });

    // Páginas
    Route::prefix('page')->group(function () {
        // Configurações de conta
        Route::get('account-settings-account', [PagesController::class, 'account_settings_account'])->name('page-account-settings-account');
        Route::get('account-settings-security', [PagesController::class, 'account_settings_security'])->name('page-account-settings-security');
        Route::get('account-settings-billing', [PagesController::class, 'account_settings_billing'])->name('page-account-settings-billing');
        Route::get('account-settings-notifications', [PagesController::class, 'account_settings_notifications'])->name('page-account-settings-notifications');
        Route::get('account-settings-connections', [PagesController::class, 'account_settings_connections'])->name('page-account-settings-connections');

        Route::get('profile', [PagesController::class, 'profile'])->name('page-profile');
        Route::get('faq', [PagesController::class, 'faq'])->name('page-faq');

        // Base de conhecimento
        Route::prefix('knowledge-base')->group(function () {
            Route::get('', [PagesController::class, 'knowledge_base'])->name('page-knowledge-base');
            Route::get('category', [PagesController::class, 'kb_category'])->name('page-knowledge-base-category');
            Route::get('category/question', [PagesController::class, 'kb_question'])->name('page-knowledge-base-question');
        });

        Route::get('pricing', [PagesController::class, 'pricing'])->name('page-pricing');
        Route::get('api-key', [PagesController::class, 'api_key'])->name('page-api-key');

        // Blog
        Route::prefix('blog')->group(function () {
            Route::get('list', [PagesController::class, 'blog_list'])->name('page-blog-list');
            Route::get('detail', [PagesController::class, 'blog_detail'])->name('page-blog-detail');
            Route::get('edit', [PagesController::class, 'blog_edit'])->name('page-blog-edit');
        });

        Route::get('license', [PagesController::class, 'license'])->name('page-license');
    });

    // Páginas diversas
    Route::get('coming-soon', [MiscellaneousController::class, 'coming_soon'])->name('misc-coming-soon');
    Route::get('not-authorized', [MiscellaneousController::class, 'not_authorized'])->name('misc-not-authorized');
    Route::get('maintenance', [MiscellaneousController::class, 'maintenance'])->name('misc-maintenance');

    // Exemplos de modais
    Route::get('/modal-examples', [PagesController::class, 'modal_examples'])->name('modal-examples');

    // Gráficos
    Route::prefix('chart')->group(function () {
        Route::get('apex', [ChartsController::class, 'apex'])->name('chart-apex');
        Route::get('chartjs', [ChartsController::class, 'chartjs'])->name('chart-chartjs');
        Route::get('echarts', [ChartsController::class, 'echarts'])->name('chart-echarts');
    });

    // Mapas
    Route::get('/maps/leaflet', [ChartsController::class, 'maps_leaflet'])->name('map-leaflet');
});

// ==================== ROTAS PÚBLICAS ====================
Route::get('lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/error', [MiscellaneousController::class, 'error'])->name('error');
