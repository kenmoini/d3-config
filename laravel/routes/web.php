<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/dmz-provisioner', 'DMZProvisionerController@index');
Route::post('/dmz-provisioner', 'DMZProvisionerController@generateScript');

Route::get('/bastion-host-provisioner', 'BastionHostProvisionerController@index');
Route::post('/bastion-host-provisioner', 'BastionHostProvisionerController@generateScript');

Route::get('/ocp-host-prep', 'OCPHostPrepController@index');
Route::post('/ocp-host-prep', 'OCPHostPrepController@generateScript');

Route::get('/ocp-registry-deployer', 'OCPRegistryDeployerController@index');

Route::get('/openshift-ansible-configurator', 'OCPAnsibleConfiguratorController@index');

Route::get('/docs', 'DocsController@index');

Route::get('/d3-config-wizard', 'D3CWizardController@index');
Route::post('/d3-config-wizard', 'D3CWizardController@generateScripts');
