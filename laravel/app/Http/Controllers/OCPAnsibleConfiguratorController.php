<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OCPAnsibleConfiguratorController extends Controller
{
  public function index() {
    return view('ocp-ansible-configurator');
  }
}
