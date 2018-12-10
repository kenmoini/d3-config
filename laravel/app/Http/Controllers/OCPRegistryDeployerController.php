<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OCPRegistryDeployerController extends Controller
{
  public function index() {
    return view('ocp-registry-deployer');
  }
}
