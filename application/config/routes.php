<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ============================================================================
// WEB ROUTES - Sistem Informasi Apotek
// ============================================================================

// Authentication Routes
$route['auth'] = 'auth/index';
$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';

// Dashboard Routes
$route['dashboard'] = 'dashboard/index';

// Medicines Routes
$route['medicines'] = 'medicines/index';
$route['medicines/create'] = 'medicines/create';
$route['medicines/edit/(:num)'] = 'medicines/edit/$1';
$route['medicines/delete/(:num)'] = 'medicines/delete/$1';

// Users Routes
$route['users'] = 'users/index';
$route['users/create'] = 'users/create';
$route['users/edit/(:num)'] = 'users/edit/$1';
$route['users/deactivate/(:num)'] = 'users/deactivate/$1';
$route['users/activate/(:num)'] = 'users/activate/$1';

// Sales Routes
$route['sales/pos'] = 'sales/pos';
$route['sales/search'] = 'sales/search';
$route['sales/add'] = 'sales/add';
$route['sales/update'] = 'sales/update';
$route['sales/remove'] = 'sales/remove';
$route['sales/checkout'] = 'sales/checkout';
$route['sales/invoice/(:num)'] = 'sales/invoice/$1';
$route['sales/history'] = 'sales/history';

// Stock Routes
$route['stock'] = 'stock/index';
$route['stock/in'] = 'stock/in';
$route['stock/out'] = 'stock/out';
$route['stock/opname'] = 'stock/opname';

// Shifts Routes
$route['shifts'] = 'shifts/index';
$route['shifts/list'] = 'shifts/listing';
$route['shifts/assign/(:num)'] = 'shifts/assign/$1';
$route['shifts/events'] = 'shifts/events';
$route['shifts/setup'] = 'shifts/setup';
$route['shifts/rules'] = 'shifts/rules';
$route['shifts/rules/create'] = 'shifts/rules_create';
$route['shifts/rules/edit/(:num)'] = 'shifts/rules_edit/$1';
$route['shifts/rules/delete/(:num)'] = 'shifts/rules_delete/$1';
$route['shifts/rules/export'] = 'shifts/rules_export';
$route['shifts/rules/import'] = 'shifts/rules_import';
$route['shifts/create'] = 'shifts/create';
$route['shifts/edit/(:num)'] = 'shifts/edit/$1';
$route['shifts/delete/(:num)'] = 'shifts/delete/$1';
$route['shifts/assign/(:num)'] = 'shifts/assign/$1';

// Attendance Routes
$route['attendance'] = 'attendance/index';
$route['attendance/checkin'] = 'attendance/checkin';
$route['attendance/checkout'] = 'attendance/checkout';
$route['attendance/report'] = 'attendance/report';

// ============================================================================
// API ROUTES - Sistem Informasi Apotek
// ============================================================================

$route['api/v1/test'] = 'api/Test/halo';

// Sales API Routes
$route['api/v1/sales/summary/daily'] = 'api/Sales/summary_daily';
$route['api/v1/sales/items-by-day'] = 'api/Sales/items_by_day';
$route['api/v1/sales/top-products'] = 'api/Sales/top_products';

// Attendance API Routes
$route['api/v1/attendance/shift-today'] = 'api/Attendance/shift_today';
$route['api/v1/attendance/summary'] = 'api/Attendance/summary';

// Visits API Routes
$route['api/v1/visits/summary'] = 'api/Visits/summary';

// Stock API Routes
$route['api/v1/stock/check'] = 'api/Stock/check';

// Health Check Route (optional)
$route['api/v1/health'] = 'api/Health/check';
$route['reports/sales'] = 'reports/sales';
$route['reports/stock'] = 'reports/stock';
$route['reports/attendance'] = 'reports/attendance';

// Settings Routes
$route['settings'] = 'settings/index';
$route['settings/api-keys'] = 'settings/api_keys';
$route['settings/profile'] = 'settings/profile';
$route['settings/password'] = 'settings/password';
