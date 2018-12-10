<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function get_current_git_commit( $branch='master' ) {
      if ( $hash = file_get_contents( sprintf( base_path('../.git/refs/heads/%s'), $branch ) ) ) {
        return trim($hash);
      } else {
        return false;
      }
    }
}
