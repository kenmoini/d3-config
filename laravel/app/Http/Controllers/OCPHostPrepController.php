<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class OCPHostPrepController extends Controller
{
  public function index() {
    return view('ocp-host-prep');
  }

  public function generateScript() {

    $zip = new ZipArchive();
    $filename = sys_get_temp_dir() . "/ocp-host-prep-" . uniqid() . ".zip";
    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
        exit("COULD NOT CREATE ARCHIVE");
    }

    $input = request()->all();

    $initialUsername = $input['initialUsername'] ?: 'root';
    $nodeAuthenticationMethod = $input['nodeAuthenticationMethod'];
    $initialPassword = $input['initialPassword'];
    $initialSSHKey = $input['initialSSHKey'];
    $newUsername = $input['newUsername'] ?: 'ocp-worker';
    $newUsername = $input['domainName'] ?: 'discon.lab';
    $privateRPMRepoURL = $input['privateRPMRepoURL'] ?: 'http://bastion.discon.lab/rpms/';

    $openshiftVersion = $input['openshiftVersion'] ?: '3.11';
    $ansibleVersion = $input['ansibleVersion'] ?: '2.6';

    //Defaults for checkboxes being false
    $scrambleInitialUserPassword = $disableYumSMPluginOnNodes = $pushPrivateRPMRepoOnNodes = false;
    if (isset($input['scrambleUsedPassword'])) { if($input['scrambleUsedPassword'] == "scrambleUsedPassword") { $scrambleInitialUserPassword = true; } }
    if (isset($input['disableYumSMPluginOnNodes'])) { if($input['disableYumSMPluginOnNodes'] == "disableYumSMPluginOnNodes") { $disableYumSMPluginOnNodes = true; } }
    if (isset($input['pushPrivateRPMRepoOnNodes'])) { if($input['pushPrivateRPMRepoOnNodes'] == "pushPrivateRPMRepoOnNodes") { $pushPrivateRPMRepoOnNodes = true; } }

    //Inventory Builder Array...Builder...
    $inputKeys = array_keys($input);
    $setOfKeys = preg_grep('/^inventoryBuilder-uid-/', $inputKeys);
    $setOfUIDs = $inventoryItems = [];
    foreach ($setOfKeys as $specificUID) {
      $setOfUIDs[] = $input[$specificUID];
    }
    //Compile array of inventory items
    foreach ($setOfUIDs as $specificUID) {
      $inventoryItems[] = [
        'type' => $input['inventoryBuilder-type-' . $specificUID],
        'hostname' => $input['inventoryBuilder-hostname-' . $specificUID],
        'staticIPCIDR' => $input['inventoryBuilder-staticIPCIDR-' . $specificUID],
        'networkComponents' => explode('/', $input['inventoryBuilder-staticIPCIDR-' . $specificUID]),
        'gateway' => $input['inventoryBuilder-gateway-' . $specificUID],
      ];
    }

    $streamedData = '';

    $streamedData .= '#!/bin/bash' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Add hosts to /etc/hosts..."' . "\n";
    $streamedData .= 'ansible-playbook add-inventory-to-hosts.yml';
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Initial Connection..."' . "\n";
    $streamedData .= 'ansible-playbook -i ./inventory/ocp-host-prep-inventory initial-configuration.yml' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Configuring and preparing OCP Hosts..."' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= '' . "\n";

    //$zip->addFromString("ocp-host-prep.sh", $streamedData);

    $streamedData = '';


    /*

    $streamedData = 'Make the shell script executable and go for it. #bestReadMeEver';

    $zip->addFromString("README", $streamedData);

    $zip->close();

    // http headers for zip downloads
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"bastionHostProvisioner.zip\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    ob_end_flush();
    @readfile($filename);
    */


    return response()->json(['success'=>true, 'streamedData' => $streamedData]);
  }
}
