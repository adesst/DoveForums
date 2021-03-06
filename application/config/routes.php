<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "forums";
$route['404_override'] = '';

// Discussions.
$route['discussion/delete_discussion/(:any)'] = 'forums/delete_discussion/$1';
$route['discussion/edit_discussion/(:any)'] = 'forums/edit_discussion/$1';
$route['discussion/new_discussion'] = 'forums/discussions/new_discussion';
$route['discussion/(:any)/(:any)'] = 'forums/discussions/view/$1/$2';
$route['discussion/(:any)'] = 'forums/discussions/index/$1';
$route['discussion'] = 'forums';

//$route['discussions/(:any)'] = 'forums/discussions/$1';

// Forums.
$route['discussions'] = 'forums';
$route['discussions/reply_ajax'] = 'forums/discussions/reply_ajax';
$route['discussions/(:any)'] = 'forums/filtered/$1';

// Categories.
$route['categories'] = 'forums';
$route['categories/new_category'] = 'forums/categories/new_category';
$route['categories/(:any)'] = 'forums/discussions/index/$1';

// Members.
$route['members/(:any)'] = 'forums/members/$1';
$route['members'] = 'forums/members/view';

$route['set_language/(:any)'] = 'forums/set_language/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
