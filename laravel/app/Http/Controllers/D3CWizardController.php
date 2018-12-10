<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class D3CWizardController extends Controller
{

  public function index() {
    return view('wizard');
  }

  public function generateScripts() {
    $input = request()->all();

    /*
    $zip = new ZipArchive();
    $filename = sys_get_temp_dir() . "/bhp-" . uniqid() . ".zip";
    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
        exit("COULD NOT CREATE ARCHIVE");
    }
    */

    if ( Isset($input['enableDMZProvisioner']) ) {
      if ($input['enableDMZProvisioner'] === "enableDMZProvisioner") {

        $dmzData = [
          'openshiftVersion' => $input['openshiftVersion'],
          'ansibleVersion' => $input['ansibleVersion'],
          'localRepoPath' => $input['localRepoPath'],
          'registryUsername' => $input['registryUsername'],
          'registryPassword' => $input['registryPassword'],
          'enabled-repos' => $input['enabled-repos'],
        ];
        $dmzScript = app('App\Http\Controllers\DMZProvisionerController')->generateScriptForTheWizard($dmzData);
        //$zip->addFromString("dmz-provisioner.sh", $dmzScript);
      }
    }

    if ( isset($input['enableBastionHostProvisioner']) ) {
      if ($input['enableBastionHostProvisioner'] === 'enableBastionHostProvisioner') {
        $bastionHostProvisionerData = [
          'bastionHostHostname' => $input['bastionHostHostname'],
          'domainName' => $input['domainName'],
          'repoContentPath' => $input['repoContentPath'],
          'enabled-repos' => $input['enabled-repos'],
          'openshiftVersion' => $input['openshiftVersion'],
          'ansibleVersion' => $input['ansibleVersion'],
          'bastionStaticIP' => $input['bastionStaticIP'],
          'dhcpCIDR' => $input['dhcpCIDR'],
          'dhcpStartRange' => $input['dhcpStartRange'],
          'dhcpStopRange' => $input['dhcpStopRange'],
          'bastionWANInterface' => $input['bastionWANInterface'],
          'bastionLANInterface' => $input['bastionLANInterface']
        ];
        if (isset($input['bastionDisableYumSMPlugin'])) { $bastionHostProvisionerData['disableYumSMPlugin'] = $input['bastionDisableYumSMPlugin']; }
        if (isset($input['bastionEnableDNSMASQ'])) { $bastionHostProvisionerData['enableDNSMASQ'] = $input['bastionEnableDNSMASQ']; }
        if (isset($input['bastionEnableChronyd'])) { $bastionHostProvisionerData['enableChronyd'] = $input['bastionEnableChronyd']; }
        if (isset($input['bastionEnableRouting'])) { $bastionHostProvisionerData['enableRouting'] = $input['bastionEnableRouting']; }

        if (isset($input['enableBastionHostRPMRepos'])) { $bastionHostProvisionerData['enableBastionHostRPMRepos'] = $input['enableBastionHostRPMRepos']; }
        if (isset($input['enableBastionHostDockerRegistry'])) { $bastionHostProvisionerData['enableBastionHostDockerRegistry'] = $input['enableBastionHostDockerRegistry']; }

        $bastionScriptStream = app('App\Http\Controllers\BastionHostProvisionerController')->generateScriptForTheWizard($bastionHostProvisionerData);

        foreach ($bastionScriptStream as $file) {
          //$zip->addFromString($file[0], $file[1]);
          //$streamedData[] = $file;
        }
      }
    }

    if ($input['inventoryBuilder-nodeCount'] > 0) {
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

      $dataIn['inventoryItems'] = $inventoryItems;
      $dataIn['registryURL'] = $input['registryURL'];
      $dataIn['bastionHostHostname'] = $input['bastionHostHostname'];
      if (isset($input['ansible_ssh_user'])) { $dataIn['ansible_ssh_user'] = $input['ansible_ssh_user']; }
      if (isset($input['ansible_become'])) { $dataIn['ansible_become'] = $input['ansible_become']; }
      if (isset($input['openshift_examples_modify_imagestreams'])) { $dataIn['openshift_examples_modify_imagestreams'] = $input['openshift_examples_modify_imagestreams']; }
      if (isset($input['enableBastionHostDockerRegistry'])) { $dataIn['enableBastionHostDockerRegistry'] = $input['enableBastionHostDockerRegistry']; }
      $dataIn['clusterAuthenticationMethod'] = $input['clusterAuthenticationMethod'];
      $dataIn['domainName'] = $input['domainName'];
      $dataIn['ocpClusterType'] = $input['ocpClusterType'];
      $dataIn['ocpRegistryType'] = $input['ocpRegistryType'];
      $dataIn['ocpAdminUsername'] = $input['ocpAdminUsername'];
      $dataIn['ocpAdminPassword'] = $input['ocpAdminPassword'];
      $dataIn['registryAuthenticationMethod'] = $input['registryAuthenticationMethod'];
      $dataIn['registryAuthenticationUsername'] = $input['registryAuthenticationUsername'];
      $dataIn['registryAuthenticationUserPassword'] = $input['registryAuthenticationUserPassword'];
      $dataIn['openshift_master_cluster_hostname'] = $input['openshift_master_cluster_hostname'];
      $dataIn['openshift_master_cluster_public_hostname'] = $input['openshift_master_cluster_public_hostname'];
      $dataIn['openshift_master_default_subdomain'] = $input['openshift_master_default_subdomain'];
      $dataIn['registry_openshift_master_default_subdomain'] = $input['registry_openshift_master_default_subdomain'];

      $ocpInventoryStream = app('App\Http\Controllers\InventoryBuilderController')->generateInventoryFileForTheWizard("registry", $dataIn);
      //$zip->addFromString("inventory/ocp-inventory", $ocpInventoryStream);
      $streamedData = $ocpInventoryStream;
    }

    return response()->json(['success'=>true, 'streamedData' => $streamedData]);
  }

}
